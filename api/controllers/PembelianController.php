<?php

namespace app\controllers;

use Yii;
use app\models\Pembelian;
use app\models\PembelianDet;
use app\models\Hutang;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class PembelianController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'detail' => ['get'],
                    'view' => ['get'],
                    'selectedsupplier' => ['get'],
                    'selectedproduk' => ['get'],
                    'supplierlist' => ['get'],
                    'kliniklist' => ['get'],
                    'produklist' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                ],
            ]
        ];
    }

    public function beforeAction($event) {
        $action = $event->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (excel(isset($this->actions['*']))) {
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

    public function actionDetail($id) {
        //create query
        $query = new Query;
        $query->select("*")
                ->from('pembelian_det')
                ->where('pembelian_id=' . $id);

        //filter

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'detail' => $models), JSON_PRETTY_PRINT);
    }

    public function actionKliniklist() {
        //create query
        $query = new Query;
        $query->select("*")
                ->from('m_cabang')
                ->where('is_deleted=0');

        //filter

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'listKlinik' => $models), JSON_PRETTY_PRINT);
    }

    public function actionSupplierlist() {
        //create query
        $query = new Query;
        $query->select("*")
                ->from('m_supplier')
                ->where('is_deleted=0');

        //filter

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'listSupplier' => $models), JSON_PRETTY_PRINT);
    }

    public function actionProduklist() {
        //create query
        $query = new Query;
        $query->select("*")
                ->from('m_produk as p')
//                ->join('JOIN')
                ->where('is_deleted=0 AND type="Barang"');

        //filter

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'listProduct' => $models), JSON_PRETTY_PRINT);
    }

    public function actionSelectedsupplier($id) {
        $query = new Query;
        $query->select("*")
                ->from('m_supplier')
                ->where('id=' . $id);

        //filter

        $command = $query->createCommand();
        $models = $command->queryOne();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'selected' => $models), JSON_PRETTY_PRINT);
    }

    public function actionSelectedproduk($id) {
        $query = new Query;
        $query->select("*")
                ->from('m_produk')
                ->where('id=' . $id);

        //filter

        $command = $query->createCommand();
        $models = $command->queryOne();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'selected' => $models), JSON_PRETTY_PRINT);
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tanggal DESC";
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
                ->from('pembelian as pe')
                ->join('JOIN', 'm_supplier as su', 'pe.supplier_id = su.id')
                ->join('JOIN', 'm_cabang as ca', 'pe.cabang_id= ca.id')
                ->orderBy($sort)
                ->select("pe.*,ca.nama as klinik, su.nama as nama_supplier,su.alamat as alamat, su.no_tlp as no_tlp,su.email as email");

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

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Pembelian();
        $model->attributes = $params['pembelian'];
        $model->tanggal = date("Y-m-d", strtotime($params['pembelian']['tanggal']));


        if ($model->save()) {
            if ($model->credit > 0) {
                $credit = new Hutang();
                $credit->pembelian_id = $model->id;
                $credit->credit = $model->credit;
                $credit->status = 'belum lunas';
                $credit->save();
            }
            foreach ($params['pembeliandet'] as $val) {
                $modelDet = new PembelianDet();
                $modelDet->attributes = $val;
                $modelDet->pembelian_id = $model->id;
                $modelDet->save();
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
        $model->attributes = $params['pembelian'];
        $model->tanggal = date("Y-m-d", strtotime($params['pembelian']['tanggal']));

        if ($model->save()) {
            $credit = Hutang::find()->where('pembelian_id=' . $model->id)->one();
            if (!empty($credit)) {
                $credit->credit = $model->credit;
                $credit->status = ($model->credit > 0) ? 'belum lunas' : 'lunas';
                $credit->save();
            } else if (empty($credit) && $model->credit != 0) {
                $credit = new Hutang();
                $credit->credit = $model->credit;
                $credit->status = ($model->credit > 0) ? 'belum lunas' : 'lunas';
                $credit->save();
            }

            $pembelianDet = $params['pembeliandet'];
            $id_det = array();
//            Yii::error($pembelianDet);
            foreach ($pembelianDet as $val) {
                $det = PembelianDet::findOne($val['id']);
                if (empty($det)) {
                    $det = new PembelianDet();
                }
                $det->attributes = $val;
                $det->pembelian_id = $id;
                if($det->save()){
                    $id_det[] = $det->id;
                }
            }
            $deleteDetail = PembelianDet::deleteAll('id NOT IN (' . implode(',', $id_det) . ') AND pembelian_id=' . $model->id);
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = PembelianDet::deleteAll(['pembelian_id' => $model->id]);
        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Pembelian::findOne($id)) !== null) {
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
