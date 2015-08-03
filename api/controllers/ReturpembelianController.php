<?php

namespace app\controllers;

use Yii;
use app\models\RPembelian;
use app\models\RPembelianDet;
use app\models\PembelianDet;
use app\models\Pembelian;
use app\models\Cabang;
use app\models\Hutang;
use app\models\KartuStok;
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
//                    'pembelianlist' => ['get'],
                    'selected' => ['get'],
                    'excel' => ['get'],
                    'lastcode' => ['get'],
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
                ->select("rp.*,pe.kode as kode_pembelian,su.nama as nama_supplier,ca.nama as klinik,ca.id as cabang_id");

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                $query->andFilterWhere(['like', $key, $val]);
            }
        }
        $_SESSION['query'] = $query;
        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        foreach ($models as $keys => $valu) {
            $query = new Query;
            $query->select("pe.*,su.kode as kode_supplier, su.nama as nama_supplier,su.no_tlp as no_tlp, su.email as email, su.alamat as alamat,ca.nama as klinik")
                    ->from('pembelian as pe')
                    ->join("LEFT JOIN", 'm_supplier as su', 'pe.supplier_id = su.id')
                    ->join("LEFT JOIN", 'm_cabang as ca', 'pe.cabang_id = ca.id')
                    ->where(['like', 'pe.id', $valu['pembelian_id']]);
            $command = $query->createCommand();
            $pembelian = $command->queryOne();
            $models[$keys]['pembelian'] = $pembelian;
        }
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView() {
        $params = $_REQUEST;
//        Yii::error($params);
        $decode = json_decode($params['form']);
        $isi = array();
        $details = array();
        foreach ($decode as $key => $a) {
            $isi[$key] = $a;
        }
        $detPenj = PembelianDet::find()
                ->with('produk')
                ->where('pembelian_id=' . $isi['id'])
                ->all();
        if (!empty($detPenj)) {
            foreach ($detPenj as $key => $val) {
                $details[$key] = $val->attributes;
                $details[$key]['nama_produk'] = $val->produk->nama;
                if (!empty($params['id']) || $params['id'] != 0) {
                    $rDet = RPembelianDet::find()
                            ->where('r_pembelian_id=' . $params['id'] . ' AND pembelian_det_id=' . $val->id)
                            ->one();
                    if (!empty($rDet)) {
                        $details[$key]['jumlah_retur'] = $rDet->jumlah;
                        $details[$key]['harga_retur'] = $rDet->harga;
                    }
                } else {
                    $details[$key]['jumlah_retur'] = 0;
                    $details[$key]['harga_retur'] = 0;
                }
            }
        }
//        Yii::error($details);
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'details' => $details), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
//        Yii::error($params);
        $model = new RPembelian();
        $model->attributes = $params['retur'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));
        $model->pembelian_id = $params['retur']['pembelian']['id'];
        $model->total = $params['retur']['sub_totals'];
        $model->biaya_lain = $params['retur']['total_biaya'] - $params['retur']['sub_totals'];

        if ($model->save()) {
            $deleteAll = RPembelianDet::deleteAll('r_pembelian_id=' . $model->id);
            foreach ($params['returdet'] as $data) {
                if ($data['jumlah_retur'] != 0 || $data['jumlah_retur'] != "") {
                    $det = new RPembelianDet();
                    $det->attributes = $data;
                    $det->r_pembelian_id = $model->id;
                    $det->pembelian_det_id = $data['id'];
                    $det->jumlah = $data['jumlah_retur'];
                    $det->sub_total = $data['sub_total_retur'];
                    if ($det->save()) {
                        $pembelian = Pembelian::findOne($model->pembelian_id);
                        $keterangan = 'r_pembelian';
                        $stok = new KartuStok();
                        $update = $stok->process('out', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $pembelian->cabang_id, $det->harga, $keterangan, $model->id);
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

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params['retur'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));
        $model->pembelian_id = $params['retur']['pembelian']['id'];
        $model->total = $params['retur']['sub_totals'];
        $model->biaya_lain = $params['retur']['total_biaya'] - $params['retur']['sub_totals'];

        if ($model->save()) {
            $deleteAll = RPembelianDet::deleteAll('r_pembelian_id=' . $model->id);
            foreach ($params['returdet'] as $data) {
                if ($data['jumlah_retur'] != 0 || $data['jumlah_retur'] != "") {
                    $det = new RPembelianDet();
                    $det->attributes = $data;
                    $det->r_pembelian_id = $model->id;
                    $det->pembelian_det_id = $data['id'];
                    $det->jumlah = $data['jumlah_retur'];
                    $det->sub_total = $data['sub_total_retur'];
                    if ($det->save()) {
                        $pembelian = Pembelian::findOne($model->pembelian_id);
                        $keterangan = 'r_pembelian';
                        $stok = new KartuStok();
                        $hapus = $stok->hapusKartu($keterangan, $id);
                        $update = $stok->process('out', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $pembelian->cabang_id, $det->harga, $keterangan, $model->id);
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

    public function actionDelete($id) {
        $model = $this->findModel($id);
        $deleteDetail = RPembelianDet::deleteAll(['r_penjualan_id' => $id]);

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
    
    public function actionLastcode($id){
        $params = $_REQUEST;
//        Yii::error($id);
        $kodeCabang = Cabang::findOne($id);
        $number = RPembelian::find()->orderBy('id DESC')->one();
        $lastNumber= (empty($number)) ? 1 : $number->id+1;
        
        $format = 'RBELI/'.$kodeCabang->kode.'/'.substr('00000'.$lastNumber, -5);
        
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $format), JSON_PRETTY_PRINT);
    }

}
