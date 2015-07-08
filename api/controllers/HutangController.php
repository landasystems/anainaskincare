<?php

namespace app\controllers;

use Yii;
use app\models\Hutang;
use app\models\Pembelian;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class HutangController extends Controller {

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
                    'listpembelian' => ['get'],
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

//
//    public function actionListpembelian() {
//        $query = new Query;
//        $query->from('hutang as h')
//                ->JOIN('JOIN','pembelian as p',' h.pembelian_id = p.id')
//                ->join('LEFT JOIN', 'm_supplier as s', 'p.supplier_id = s.id')
//                ->where('h.credit IS NOT NULL')
//                ->select("p.*,s.kode as kode_supplier, s.nama as nama,");
//
//        $command = $query->createCommand();
//        $models = $command->queryAll();
//
//        $this->setHeader(200);
//
//        echo json_encode(array('status' => 1, 'listPembelian' => $models));
//    }
    public function actionSelected($id) {
        $query = new Query;
        $query->from('hutang as h')
                ->JOIN('JOIN', 'pembelian as p', ' h.pembelian_id = p.id')
                ->JOIN('JOIN', 'm_cabang as c', ' p.cabang_id = c.id')
                ->join('LEFT JOIN', 'm_supplier as s', 'p.supplier_id = s.id')
                ->where('pembelian_id=' . $id)
                ->select("h.*, sum(h.debet) as sumDebet,sum(h.credit) as sumCredit,p.*,s.kode as kode_supplier, s.nama as nama,s.no_tlp as no_tlp, s.email as email, s.alamat as alamat,c.nama as nama_cabang");

        $command = $query->createCommand();
        $models = $command->queryOne();
        $query2 = new Query;
        $query2->select("*")
                ->from("hutang")
                ->where("pembelian_id=" . $id)
                ->orderBy('tanggal_transaksi DESC');
        $history = $query2->createCommand()->queryAll();

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'selected' => $models, 'history' => $history));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "h.id ASC";
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
                ->from('hutang as h')
                ->join('JOIN', 'pembelian as p', ' h.pembelian_id= p.id')
                ->join('JOIN', 'm_supplier as s', ' p.supplier_id= s.id')
                ->join('JOIN', 'm_cabang as c', ' p.cabang_id= c.id')
                ->where('h.credit IS NOT NULL')
                ->orderBy($sort)
                ->select("h.*,p.*,s.nama as nama,c.nama as klinik");

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
        $noErrors = false;
        $detail = $params['detail'];
        $id = array();
        foreach ($detail as $value) {
            $model = Hutang::findOne($value['id']);
            if (empty($model)) {
                $model = new Hutang();
            }
            $model->attributes = $value;
            $model->pembelian_id = $params['form']['pembelian_id'];
            if ($model->save()) {
                $noErrors = true;
            }
            $id[] = $model->id;
        }
        $deleteAll = Hutang::deleteAll('id NOT IN(' . implode(',', $id) . ') AND pembelian_id=' . $params['form']['pembelian_id']);
        
        if ($deleteAll) {
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
        $model->attributes = $params;

        if ($model->save()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Hutang::findOne($id)) !== null) {
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