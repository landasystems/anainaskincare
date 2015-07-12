<?php

namespace app\controllers;

use Yii;
use app\models\Penjualan;
use app\models\PenjualanDet;
use app\models\Pinjaman;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class PenjualanController extends Controller {

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
                    'kode_cabang' => ['get'],
                    'dokter' => ['post'],
                    'terapis' => ['post'],
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
        $sort = "penjualan.id ASC";
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
                ->from(['penjualan', 'm_cabang', 'm_customer'])
                ->where('penjualan.cabang_id = m_cabang.id and penjualan.customer_id = m_customer.id')
                ->orderBy($sort)
                ->select("m_cabang.nama as cabang, m_customer.nama as customer, penjualan.kode as kode, penjualan.tanggal as tanggal,
                    penjualan.keterangan as keterangan, penjualan.total as total, penjualan.cash as cash, penjualan.credit as credit, penjualan.status as status,
                    penjualan.kode as kode, penjualan.id as id,  penjualan.customer_id as customer_id, penjualan.cabang_id as cabang_id,
                    m_customer.no_tlp as no_tlp, m_customer.email as email, m_customer.alamat as alamat");

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
        $det = PenjualanDet::find()
                ->with(['barang'])
                ->orderBy('id')
                ->where(['penjualan_id' => $model['id']])
                ->all();

        $detail = array();

        foreach ($det as $key => $val) {
            $detail[$key] = $val->attributes;

            $namaBarang = (isset($val->barang->nama)) ? $val->barang->nama : '';
            $hargaBarang = (isset($val->barang->harga_beli_terakhir)) ? $val->barang->harga_beli_terakhir : '';
            $jualBarang = (isset($val->barang->harga_jual)) ? $val->barang->harga_jual : '';
            $dokter = (isset($val->dokter->nama)) ? $val->dokter->nama : '';
            $terapis= (isset($val->terapis->nama)) ? $val->terapis->nama : '';
            $detail[$key]['produk'] = ['id' => $val->produk_id, 'nama' => $namaBarang, 'harga_beli_terakhir' => $hargaBarang, 'harga_jual' => $jualBarang];
            $detail[$key]['terapis'] = ['id' => $val->pegawai_terapis_id, 'nama'=> $terapis];
            $detail[$key]['dokter'] = ['id' => $val->pegawai_dokter_id, 'nama'=> $dokter];
            
        }


        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes), 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Penjualan();
//        print_r($params['penjualandet']);

        $model->attributes = $params['penjualan'];
        $model->tanggal = date('Y-m-d', strtotime($model->tanggal));


        if ($model->save()) {
            if ($model->status == "Pesan") {
                if ($model->credit > 0) {
                    $pinjaman = new Pinjaman();
                    $pinjaman->penjualan_id = $model->id;
                    $pinjaman->debet = $model->credit;
                    $pinjaman->status = 'Belum Lunas';
                    $pinjaman->save();
                }
            }
            foreach ($params['penjualandet'] as $data) {
                $det = new PenjualanDet();
                $det->attributes = $data;
                $det->penjualan_id = $model->id;
                $det->produk_id = $data['produk']['id'];
                $det->pegawai_terapis_id = isset($data['terapis']['id']) ? $data['terapis']['id'] : '';
                $det->pegawai_dokter_id = isset($data['dokter']['id']) ? $data['terapis']['id'] : '';
//                $det->sub_total = str_replace('.', '', $data['sub_total']);

                if ($det->save()) {
                    $keterangan = 'penjualan';
                    $stok = new \app\models\KartuStok();
                    $update = $stok->process('out', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $model->cabang_id, $det->harga, $keterangan, $model->id);
                }
                // stock
            }
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {
            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    public function actionUpdate($id) {
        $id_det = array();
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $model->attributes = $params['penjualan'];


        if ($model->save()) {
            if ($model->status == 'Selesai') {
                if ($model->credit > 0) {
                    $pinjaman = Pinjaman::find()->where('penjualan_id=' . $model->id)->one();
                    if (empty($pinjaman)) {
                        $pinjaman = new Pinjaman();
//                        $pinjaman->save();
                    }

                    $pinjaman->penjualan_id = $model->id;
                    $pinjaman->debet = $model->credit;
                    $pinjaman->status = 'Belum Lunas';
                    $pinjaman->save();
                    //stock
                }
            }
            $penjualanDet = $params['penjualandet'];

            foreach ($params['penjualandet'] as $val) {
                $det = PenjualanDet::findOne($val['id']);
                if (empty($det)) {
                    $det = new PenjualanDet();
                }
                $det->attributes = $val;
                $det->produk_id = $val['produk']['id'];
                $det->pegawai_terapis_id = $val['terapis']['id'];
                $det->pegawai_dokter_id = $val['dokter']['id'];
                $det->penjualan_id = $model->id;
                if ($det->save()) {
                    $id_det[] = $det->id;
                    $keterangan = 'penjualan';
                    $stok = new \app\models\KartuStok();
                    $hapus = $stok->hapusKartu($keterangan, $model->id);
                    $update = $stok->process('out', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $model->cabang_id, $det->harga, $keterangan, $model->id);
                }
                //stok
            }
            $deleteDetail = PenjualanDet::deleteAll('id NOT IN (' . implode(',', $id_det) . ') AND penjualan_id=' . $model->id);

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
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'customer' => $models));
    }

    public function actionCabang() {
        $query = new Query;
        $query->from('m_cabang')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'cabang' => $models));
    }

    public function actionProduk() {
        $query = new Query;
        $query->from('m_produk')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'produk' => $models));
    }

    public function actionDokter() {
        $query = new Query;
        $query->from('m_pegawai')
                ->select('*')
                ->where("is_deleted = '0' and jabatan = 'dokter'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'dokter' => $models));
    }

    public function actionTerapis() {
        $query = new Query;
        $query->from('m_pegawai')
                ->select('*')
                ->where("is_deleted = '0' and jabatan = 'terapis'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'terapis' => $models));
    }

    public function actionNm_customer() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;

        $query->from('m_customer')
                ->where('id="' . $params . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->query()->read();
        $this->setHeader(200);
        $model['no_tlp'] = $models['no_tlp'];
        $model['alamat'] = $models['alamat'];
        $model['email'] = $models['email'];

        echo json_encode(array('customer' => $model));
    }

    public function actionKode_cabang($id) {
        $query = new Query;

        $query->from('m_cabang')
                ->where('id="' . $id . '"')
                ->select("*");
        $command = $query->createCommand();
        $models = $command->query()->read();
        $code = $models['kode'];

        $query2 = new Query;
        $query2->from('penjualan')
                ->select('kode')
                ->orderBy('kode DESC')
                ->limit(1);

        $command2 = $query2->createCommand();
        $models2 = $command2->query()->read();
        $kode_mdl = (substr($models2['kode'], -5) + 1);
        $kode = substr('00000' . $kode_mdl, strlen($kode_mdl));
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => 'JUAL/' . $code . '/' . $kode));
    }

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

        //hapus kartu stok
        $keterangan = 'penjualan';
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
