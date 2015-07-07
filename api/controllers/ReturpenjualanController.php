<?php

namespace app\controllers;

use Yii;
use app\models\RPenjualan;
use app\models\RPenjualanDet;
use app\models\Pinjaman;
use app\models\Penjualan;
use app\models\PenjualanDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ReturpenjualanController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'cabang' => ['get'],
                    'customer' => ['get'],
                    'produk' => ['get'],
                    'nm_customer' => ['post'],
                    'det_produk' => ['get'],
                    'kodepenjualan' => ['get'],
                    'det_kodepenjualan' => ['get'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }
        $verb = Yii::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);
//        Yii::error($allowed);

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "r_penjualan.id ASC";
        $offset = 0;
        $limit = 10;
        //        Yii::error($params);
        //limit & offset pagination
        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

        //sorting
        if (isset($params['sort'])) {
            $sort = $params['sort'];
            if (isset($params['order'])) {
                if ($params['order'] == "false")
                    $sort.=" ASC";
                else
                    $sort.=" DESC";
            }
        }

        //create query
        $query = new Query;
        $query->offset($offset)
                ->limit($limit)
                ->from(['r_penjualan', 'penjualan', 'm_cabang', 'm_customer'])
                ->where('r_penjualan.penjualan_id = penjualan.id and penjualan.cabang_id = m_cabang.id and penjualan.customer_id = m_customer.id')
                ->orderBy($sort)
                ->select("r_penjualan.id as id, r_penjualan.tanggal as tanggal_retur, r_penjualan.total as total_retur, r_penjualan.keterangan as ketrangan_retur,"
                        . " r_penjualan.kode as kode_retur, penjualan.kode as kode_penjualan , penjualan.total as total_penjualan, m_cabang.nama as cabang, "
                        . "m_customer.nama as nama_customer, penjualan.customer_id as customer_id,penjualan.cabang_id as cabang_id");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                Yii::error($key);
                if ($key == 'cabang_id') {
                    $query->andFilterWhere(['like', 'penjualan.' . $key, $val]);
                    Yii::error($query);
                } elseif($key = 'kode_penjualan'){
                     $query->andFilterWhere(['like', 'penjualan.kode', $val]);
                } elseif($key = 'customer_id'){
                     $query->andFilterWhere(['like', 'penjualan.'. $key, $val]);
                } else {
                     $query->andFilterWhere(['like', 'r_penjualan.'.$key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $det = RPenjualanDet::find()
                ->where(['r_penjualan_id' => $model['id']])
                ->all();

        $detail = array();

        foreach ($det as $val) {
            $detail[] = $val->attributes;
        }


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new RPenjualan();
        print_r($params['retur_penjualandet']);
        Yii::error($params);
        $model->attributes = $params['retur_penjualan'];


        if ($model->save()) {
        foreach($params['retur_penjualandet'] as $data){
            if(!empty($data['jumlah_retur'])){
                $detail= new RPenjualanDet();
                $detail->attributes = $data;
                $detail->r_penjualan_id = $model->id;
                $detail->penjualan_det_id = $data['id'];
//                $detail->sub_total = $data['sub_total_retur'];
                $detail->save();
                
                
            }
        }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params['penjualan'];


        if ($model->save()) {
            if ($model->credit > 0) {
                $pinjaman = Pinjaman::find()->where('penjualan_id=' . $model->id)->one();
                $pinjaman->credit = $model->credit;
                $pinjaman->status = ($model->credit > 0) ? 'belum lunas' : 'lunas';
                $pinjaman->save();
            }

            foreach ($params['penjualandet'] as $data) {
                $det = new PenjualanDet();
                $det->attributes = $data;
                $det->penjualan_id = $model->id;
                $det->sub_total = str_replace('.', '', $data['sub_total']);

                $det->save();
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }
    

    public function actionCustomer() {
        $query = new Query;
        $query->from('m_customer')
                ->select('*')
                ->where("is_deleted = 0");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'customer' => $models));
    }
    public function actionKodepenjualan() {
        $query = new Query;
        $query->from('penjualan')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->where('penjualan.status="selesai"')
                ->select('penjualan.kode as kode, penjualan.id as id, m_customer.nama as customer, m_cabang.nama as cabang');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'listkode' => $models));
    }

    public function actionCabang() {
        $query = new Query;
        $query->from('m_cabang')
                ->select('*')
                ->where("is_deleted = 0");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'cabang' => $models));
    }

    public function actionProduk() {
        $query = new Query;
        $query->from('m_produk')
                ->select('*');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'produk' => $models));
    }

    public function actionDet_kodepenjualan($id) {
             $query = new Query;
        $query->from('penjualan')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->where('penjualan.id="' . $id . '"')
                ->select("penjualan.*,m_customer.no_tlp as no_tlp, m_customer.email as email, m_customer.alamat as alamat, penjualan.tanggal as tanggal,
                        m_cabang.nama as klinik");
        $command = $query->createCommand();
        $models = $command->queryOne();

        $query2 = new Query;
        $query2->from('penjualan_det')
                ->join('JOIN', 'm_produk', 'penjualan_det.produk_id = m_produk.id')
                ->where('penjualan_id="'.$id.'"')
                ->select('penjualan_det.*, m_produk.nama');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();
        $this->setHeader(200);

        echo json_encode(array('penjualan' => $models, 'detail' => $detail));
       
    }
//    public function actionNm_customer() {
//        $params = json_decode(file_get_contents("php://input"), true);
//        $query = new Query;
//
//        $query->from('m_customer')
//                ->where('id="' . $params . '"')
//                ->select("*");
//        $command = $query->createCommand();
//        $models = $command->query()->read();
//        $this->setHeader(200);
//        $model['no_tlp'] = $models['no_tlp'];
//        $model['alamat'] = $models['alamat'];
//        $model['email'] = $models['email'];
//
//        echo json_encode(array('customer' => $model));
//    }

    public function actionDet_produk($id) {
        $query = new Query;
        $query->from('m_produk')
                ->where('id="' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->queryOne();
        $this->setHeader(200);

        echo json_encode(array('produk' => $models));
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = PenjualanDet::deleteAll(['penjualan_id' => $id]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Penjualan::findOne($id)) !== null) {
            return $model;
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Bad request'), JSON_PRETTY_PRINT);
            exit;
        }
    }

    private function setHeader($status) {

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type = "application/json; charset=utf-8";

        header($status_header);
        header('Content-type: ' . $content_type);
        header('X-Powered-By: ' . "Nintriva <nintriva.com>");
    }

    private function _getStatusCodeMessage($status) {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

}

?>
