<?php

namespace app\controllers;

use Yii;
use app\models\RPembelian;
use app\models\RPembelianDet;
use app\models\PembelianDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class ReturpembelianController extends Controller {

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
                    'pembelianlist' => ['get'],
                    'selected' => ['get'],
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
        $sort = "rp.tanggal DESC";
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
                ->from(['r_pembelian as rp'])
                ->join('LEFT JOIN', 'pembelian as pe', 'rp.pembelian_id = pe.id')
                ->join('LEFT JOIN', 'm_supplier as su', 'pe.supplier_id = su.id')
                ->join('LEFT JOIN', 'm_cabang as ca', 'pe.cabang_id = ca.id')
                ->orderBy($sort)
                ->select("rp.*,pe.kode as kode_pembelian,su.nama as nama_supplier,ca.nama as klinik");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
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
        Yii::error($params);
        $model = new RPembelian();

        $model->attributes = $params['retur'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));


        if ($model->save()) {
            $deleteAll = RPembelianDet::deleteAll('r_pembelian_id=' . $model->id);
            foreach ($params['returdet'] as $data) {
                if ($data['jumlah_retur'] != 0 || $data['jumlah_retur'] != "") {
//                    $det = RPembelianDet::findOne($data['rp_id']);
//                    if(empty($det)){
                    $det = new RPembelianDet();
//                    }
                    $det->attributes = $data;
                    $det->r_pembelian_id = $model->id;
                    $det->pembelian_det_id = $data['id'];
                    $det->jumlah = $data['jumlah_retur'];
                    $det->sub_total = $data['sub_total_retur'];
                    $det->save();
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
        $model->attributes = $params['retur'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));

        if ($model->save()) {
            $deleteAll = RPembelianDet::deleteAll('r_pembelian_id=' . $model->id);
            foreach ($params['returdet'] as $data) {
                if ($data['jumlah_retur'] != 0 || $data['jumlah_retur'] != "") {
//                    $det = RPembelianDet::findOne($data['rp_id']);
//                    if(empty($det)){
                    $det = new RPembelianDet();
//                    }
                    $det->attributes = $data;
                    $det->r_pembelian_id = $model->id;
                    $det->pembelian_det_id = $data['id'];
                    $det->jumlah = $data['jumlah_retur'];
                    $det->sub_total = $data['sub_total_retur'];
                    $det->save();
                }
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionPembelianlist() {
        $query = new Query;
        $query->from('pembelian')
                ->select('*');

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'listPembelian' => $models));
    }

    public function actionSelected($id) {
        $query = new Query;
        $query->select('p.*,c.nama as klinik,s.*')
                ->from('pembelian as p')
                ->join('LEFT JOIN', 'm_supplier as s', 'p.supplier_id = s.id')
                ->join('LEFT JOIN', 'm_cabang as c', 'p.cabang_id = c.id')
                ->where('p.id=' . $id);

        $command = $query->createCommand();
        $models = $command->queryOne();
        $query2 = new Query;
        $query2->select('pb.*,pr.nama as nama_produk,rp.id as rp_id,rp.jumlah as jumlah_retur,rp.harga as harga_retur')
                ->from('pembelian_det as pb')
                ->join('LEFT JOIN', 'm_produk as pr', 'pb.produk_id = pr.id')
                ->join('LEFT JOIN', 'r_pembelian_det as rp', 'rp.pembelian_det_id = pb.id')
                ->where('pb.pembelian_id=' . $id);
        $command2 = $query2->createCommand();
        $details = $command2->queryAll();
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'pembelian' => $models, 'details' => $details));
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
        if (($model = RPembelian::findOne($id)) !== null) {
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