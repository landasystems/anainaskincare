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
                    'excel' => ['get'],
                    'customer' => ['get'],
                    'produk' => ['get'],
                    'nm_customer' => ['post'],
                    'det_produk' => ['get'],
                    'kodepenjualan' => ['get'],
                    'det_kodepenjualan' => ['post'],
                    'kode_cabang' => ['get'],
                    'select' => ['post'],
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
        session_start();
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
                ->from('r_penjualan')
                ->join('JOIN', 'penjualan', 'penjualan.id = r_penjualan.penjualan_id')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->orderBy($sort)
                ->select("r_penjualan.*, penjualan.kode as kode_penjualan , penjualan.total as total_penjualan, m_cabang.nama as cabang, "
                        . "m_customer.nama as nama_customer, penjualan.customer_id as customer_id,penjualan.cabang_id as cabang_id");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'r_penjualan.tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'r_penjualan.tanggal', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {
        $model = $this->findModel($id);

        //detail
        $query2 = new Query;
        $query2->from('penjualan_det')
                ->join('LEFT JOIN', 'm_produk', 'penjualan_det.produk_id = m_produk.id')
                ->join('LEFt JOIN', 'r_penjualan_det as rp', 'rp.penjualan_det_id= penjualan_det.id')
                ->where('r_penjualan_id="' . $model['id'] . '"')
                ->select('penjualan_det.id as id, penjualan_det.type as type, penjualan_det.produk_id as produk_id,penjualan_det.jumlah as jumlah, penjualan_det.harga as harga_awal, rp.diskon as diskon_awal , m_produk.nama,
                        rp.harga as harga, rp.jumlah_retur as jumlah_retur');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();

        $query = new Query;
        $query->from('penjualan')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->where('penjualan.id="' . $model['penjualan_id'] . '"')
                ->select("m_customer.no_tlp as no_tlp, m_customer.nama as nama_customer, m_customer.email as email, m_customer.alamat as alamat, 
                        m_cabang.nama as klinik, m_cabang.no_tlp as cab_telp, penjualan.*");
        $command = $query->createCommand();
        $models = $command->queryOne();
        $models['penjualan'] = ['kode' => $model->penjualan->kode];

        Yii::error($models);
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $detail, 'penjualan' => $models), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new RPenjualan();
        Yii::error($params);
        $model->attributes = $params['retur_penjualan'];
        $model->penjualan_id = $params['retur_penjualan']['penjualan']['id'];


        if ($model->save()) {
            $deleteAll = RPenjualanDet::deleteAll('r_penjualan_id=' . $model->id);
            foreach ($params['retur_penjualandet'] as $data) {
                if (!empty($data['jumlah_retur']) || $data['jumlah_retur'] != 0) {
                    $detail = new RPenjualanDet();
                    $detail->attributes = $data;
                    $detail->r_penjualan_id = $model->id;
                    $detail->penjualan_det_id = $data['id'];
                    $detail->sub_total = ($data['jumlah_retur'] * $data['harga']) - ($data['jumlah_retur'] * $data['diskon_awal']);

                    if ($detail->save()) {
                        $pinjaman = Penjualan::findOne($model->penjualan_id);
                        $keterangan = 'r_penjualan';
                        $stok = new \app\models\KartuStok();
                        $update = $stok->process('in', $model->tanggal, $model->kode, $detail->produk_id, $detail->jumlah_retur, $pinjaman->cabang_id, $detail->harga, $keterangan, $model->id);
                    }
                    // stock
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
        $model->attributes = $params['retur_penjualan'];


        if ($model->save()) {
            $deleteAll = RPenjualanDet::deleteAll('r_penjualan_id=' . $model->id);

            //hapus kartu stok
            $keterangan = 'r_penjualan';
            $stok = new \app\models\KartuStok();
            $hapus = $stok->hapusKartu($keterangan, $id);

            foreach ($params['retur_penjualandet'] as $data) {
                if (!empty($data['jumlah_retur']) || $data['jumlah_retur'] != 0) {
                    $detail = new RPenjualanDet();
                    $detail->attributes = $data;
                    $detail->r_penjualan_id = $model->id;
                    $detail->penjualan_det_id = $data['id'];
                    $detail->diskon = $data['diskon_awal'];
                    $detail->sub_total = ($data['jumlah_retur'] * $data['harga']) - ($data['jumlah_retur'] * $data['diskon_awal']);
                    $detail->save();
                    if ($detail->save()) {
                        $pinjaman = Penjualan::findOne($model->penjualan_id);
                        $update = $stok->process('in', $model->tanggal, $model->kode, $detail->produk_id, $detail->jumlah_retur, $pinjaman->cabang_id, $detail->harga, $keterangan, $model->id);
                    }
                }
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

    public function actionDet_kodepenjualan() {
        $params = json_decode(file_get_contents("php://input"), true);

        $id = $params['id'];
        Yii::error($params);
        $query = new Query;
        $query->from('penjualan')
                ->join('JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('JOIN', 'm_cabang', 'penjualan.cabang_id= m_cabang.id')
                ->where('penjualan.id="' . $id . '"')
                ->select("m_customer.no_tlp as no_tlp, m_customer.email as email, m_customer.alamat as alamat, 
                        m_cabang.nama as klinik,m_cabang.id as cabang_id, penjualan.*");
        $command = $query->createCommand();
        $models = $command->queryOne();

        $query2 = new Query;
        $query2->from('penjualan_det')
                ->join('LEFT JOIN', 'm_produk', 'penjualan_det.produk_id = m_produk.id')
                ->join('LEFt JOIN', 'r_penjualan_det as rp', 'rp.penjualan_det_id= penjualan_det.id')
                ->where('penjualan_id="' . $id . '" and penjualan_det.type = "Barang"')
                ->select('penjualan_det.id as id, penjualan_det.type as type, penjualan_det.produk_id as produk_id,penjualan_det.jumlah as jumlah, penjualan_det.harga as harga_awal, penjualan_det.diskon as diskon_awal , m_produk.nama,
                        rp.harga as harga, rp.jumlah_retur as jumlah_retur');
        $command2 = $query2->createCommand();
        $detail = $command2->queryAll();

        // kode
        $query3 = new Query;
        $query3->from('m_cabang')
                ->where('id="' . $models['cabang_id'] . '"')
                ->select("*");
        $command3 = $query3->createCommand();
        $models3 = $command3->query()->read();
        $code = $models3['kode'];

        $query4 = new Query;
        $query4->from('r_penjualan')
                ->select('kode')
                ->orderBy('kode DESC')
                ->limit(1);

        $command4 = $query4->createCommand();
        $models4 = $command4->query()->read();
        $kode_mdl = (substr($models4['kode'], -5) + 1);
        $kode = substr('00000' . $kode_mdl, strlen($kode_mdl));

        $this->setHeader(200);

        echo json_encode(array('penjualan' => $models, 'detail' => $detail, 'kode' => 'RJUAL/' . $code . '/' . $kode));
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = RPenjualanDet::deleteAll(['r_penjualan_id' => $id]);

        $keterangan = 'r_penjualan';
        $stok = new \app\models\KartuStok();
        $hapus = $stok->hapusKartu($keterangan, $id);

        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = RPenjualan::findOne($id)) !== null) {
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excel", ['models' => $models]);
    }

}

?>
