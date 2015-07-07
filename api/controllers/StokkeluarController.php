<?php

namespace app\controllers;

use Yii;
use app\models\StokKeluar;
use app\models\StokKeluarDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class StokkeluarController extends Controller {

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
                    'product' => ['get'],
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

        if (!in_array($verb, $allowed)) {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Method not allowed'), JSON_PRETTY_PRINT);
            exit;
        }

        return true;
    }

    public function actionCabang() {
        $query = new Query;
        $query->from('m_cabang')
                ->select("*")
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionProduct() {
        $query = new Query;
        $query->from('m_produk')
                ->select("*")
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionIndex() {
        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "stok_keluar.id ASC";
        $offset = 0;
        $limit = 10;

        if (isset($params['limit']))
            $limit = $params['limit'];
        if (isset($params['offset']))
            $offset = $params['offset'];

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
                ->from(['stok_keluar', 'm_cabang'])
                ->orderBy($sort)
                ->select("stok_keluar.id, stok_keluar.kode, stok_keluar.tanggal, m_cabang.nama as cabang, stok_keluar.keterangan, stok_keluar.total")
                ->where('m_cabang.id = stok_keluar.cabang_id');

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->where("stok_keluar.tanggal >= '$start' and stok_keluar.tanggal <= '$end'");
                } else {
                    $query->andFilterWhere(['like', 'stok_keluar.' . $key, $val]);
                }
            }
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
//        $models['tanggal'] = strtotime($model['tanggal']);
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $det = StokKeluarDet::find()
                ->where(['stok_keluar_id' => $model['id']])
                ->all();

        $detail = array();

        foreach ($det as $val) {
            $detail[] = $val->attributes;
        }
        $data = array();
        $data = $model->attributes;
        $data['tanggal'] = date("d-m-Y", strtotime($model['tanggal']));

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($data), 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new StokKeluar();
        $model->attributes = $params['stokkeluar'];
        $model->tanggal = date("Y-m-d", strtotime($params['stokkeluar']['tanggal']));

        if ($model->save()) {
            $detailskeluar = $params['detailskeluar'];
            foreach ($detailskeluar as $val) {
                $det = new StokKeluarDet();
                $det->attributes = $val;
                $det->jumlah = str_replace('.', '', $det->jumlah);
                $det->harga = str_replace('.', '', $det->harga);
                $det->stok_keluar_id = $model->id;
                $det->save();
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
        $model->attributes = $params['stokkeluar'];
        $model->tanggal = date("Y-m-d", strtotime($params['stokkeluar']['tanggal']));

        if ($model->save()) {
            $deleteDetail = StokKeluarDet::deleteAll(['stok_keluar_id' => $model->id]);
            $detailSkeluar = $params['detailskeluar'];
            foreach ($detailSkeluar as $val) {
                $det = new StokKeluarDet();
                $det->attributes = $val;
                $det->jumlah = str_replace('.', '', $det->jumlah);
                $det->harga = str_replace('.', '', $det->harga);
                $det->stok_keluar_id = $model->id;
                $det->save();
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = StokKeluarDet::deleteAll(['stok_keluar_id' => $id]);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = StokKeluar::findOne($id)) !== null) {
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
