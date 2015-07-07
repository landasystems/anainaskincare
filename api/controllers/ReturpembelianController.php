<?php

namespace app\controllers;

use Yii;
use app\models\RPembelian;
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
        $sort = "pembelian.id ASC";
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
                ->from(['pembelian'])
                ->orderBy($sort)
                ->select("*");

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
//        $model = new RPembelian();
//
//        $model->attributes = $params['retur'];
//        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));
//
//
//        if ($model->save()) {
//            foreach ($params['returdet'] as $data) {
//                $det = PembelianDet::findOne($data['id']);
//                $det->attributes = $data;
//                $det->save();
//            }
//            $this->setHeader(200);
//            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
//        } else {
//            $this->setHeader(400);
//            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
//        }
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
        $query2->select('pb.*,pr.nama as nama_produk,rp.jumlah as jumlah_retur,rp.harga as harga_retur')
                ->from('pembelian_det as pb')
                ->join('LEFT JOIN', 'm_produk as pr', 'pb.produk_id = pr.id')
                ->join('LEFT JOIN', 'r_pembelian_det as rp', 'rp.produk_id = pb.id')
                ->where('pembelian_id=' . $id);
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