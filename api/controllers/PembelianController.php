<?php

namespace app\controllers;

use Yii;
use app\models\Pembelian;
use app\models\PembelianDet;
use app\models\Hutang;
use app\models\Cabang;
use app\models\KartuStok;
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
                    'cari' => ['get'],
                    'kliniklist' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kode_cabang' => ['get'],
                    'lastcode' => ['get'],
                    'excel' => ['get'],
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

    public function actionIndex() {
        session_start();

        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "tanggal DESC";
        $offset = 0;
        $limit = 10;

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
                ->join('JOIN', 'm_user', 'm_user.id = pe.created_by')
                ->orderBy($sort)
                ->select("m_user.nama as petugas, pe.*,ca.nama as klinik, su.nama as nama_supplier,su.alamat as alamat, su.no_tlp as no_tlp,su.email as email")
                ->andWhere(['pe.cabang_id' => $_SESSION['user']['cabang_id']]);
        $_SESSION['query'] = $query;
        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key == 'tanggal') {
                    $value = explode(' - ', $val);
                    $start = date("Y-m-d", strtotime($value[0]));
                    $end = date("Y-m-d", strtotime($value[1]));
                    $query->andFilterWhere(['between', 'tanggal', $start, $end]);
                } else {
                    $query->andFilterWhere(['like', $key, $val]);
                }
            }
        }
        $command = $query->createCommand();
        $models = $command->queryAll();
        foreach ($models as $key => $val) {
            $cabang = Cabang::findOne($val['cabang_id']);
            $models[$key]['cabang'] = $cabang->attributes;
        }
        $totalItems = $query->count();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $models, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionView($id) {

        $model = $this->findModel($id);
        $querySup = new Query;
        $querySup->select("*")
                ->from('m_supplier')
                ->where('id=' . $model->supplier_id);

        $commandSup = $querySup->createCommand();
        $supplier = $commandSup->queryOne();
        $queryDet = new Query;
        $queryDet->select("*")
                ->from("pembelian_det")
                ->where("pembelian_id=" . $id);

        $commandDet = $queryDet->createCommand();
        $detail = $commandDet->queryAll();

        foreach ($detail as $key => $val) {
            $queryBrg = new Query;
            $queryBrg->select("*")
                    ->from("m_produk")
                    ->where("id=" . $val['produk_id']);

            $commandBrg = $queryBrg->createCommand();
            $barang = $commandBrg->queryOne();
            $detail[$key]['barang'] = $barang;
        }

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'supplier' => $supplier, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Pembelian();
        $model->attributes = $params['pembelian'];
        $model->supplier_id = $params['pembelian']['supplier']['id'];
        $model->tanggal = date("Y-m-d", strtotime($params['pembelian']['tanggal']));


        if ($model->save()) {
            if ($model->credit > 0) {
                $credit = new Hutang();
                $credit->pembelian_id = $model->id;
                $credit->credit = $model->credit;
                $credit->status = 'Belum Lunas';
                $credit->tanggal_transaksi = $model->tanggal;
                $credit->save();
            }

            foreach ($params['pembeliandet'] as $val) {
                $modelDet = new PembelianDet();
                $modelDet->attributes = $val;
                $modelDet->produk_id = $val['barang']['id'];
                $modelDet->pembelian_id = $model->id;
                if ($modelDet->save()) {
                     //======== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ===========//
                    //======== SIMPAN HARGA BELI BARU ============//
//                    $harga = \app\models\Harga::find()->where('cabang_id="' . $model->cabang_id . '" and produk_id="' . $det->produk_id . '"')->one();
//                    if (!empty($harga)) {
//                        $harga->harga_beli = $det->harga;
//                        $harga->save();
//                    } else {
//                        $harga = new \app\models\Harga();
//                        $harga->cabang_id = $model->cabang_id;
//                        $harga->produk_id = $modelDet->produk_id;
//                        $harga->harga_beli = $modelDet->harga;
//                        $harga->save();
//                    }

                    if ($model->status == 'clear') {
                        $keterangan = 'pembelian';
                        $stok = new KartuStok();
                        $update = $stok->process('in', $model->tanggal, $model->kode, $modelDet->produk_id, $modelDet->jumlah, $model->cabang_id, $modelDet->harga, $keterangan, $model->id);
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
        $model->attributes = $params['pembelian'];
        $model->tanggal = date("Y-m-d", strtotime($params['pembelian']['tanggal']));

        if ($model->save()) {
            $credit = Hutang::find()->where('pembelian_id=' . $model->id)->one();
            if (!empty($credit)) {
                $credit->credit = $model->credit;
                $credit->status = ($model->credit > 0) ? 'belum lunas' : 'lunas';
                $credit->tanggal_transaksi = $model->tanggal;
                $credit->save();
            } else if (empty($credit) && $model->credit != 0) {
                $credit = new Hutang();
                $credit->credit = $model->credit;
                $credit->status = ($model->credit > 0) ? 'belum lunas' : 'lunas';
                $credit->save();
            }

            $pembelianDet = $params['pembeliandet'];
            $id_det = array();

            //hapus kartu stok
            $keterangan = 'pembelian';
            $stok = new KartuStok();
            $hapus = $stok->hapusKartu($keterangan, $model->id);

            foreach ($pembelianDet as $val) {
                $det = PembelianDet::findOne($val['id']);
                if (empty($det)) {
                    $det = new PembelianDet();
                }
                $det->attributes = $val;
                $det->produk_id = $val['barang']['id'];
                $det->pembelian_id = $model->id;

                if ($det->save()) {
                    //======== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ===========//
                    //======== SIMPAN HARGA BELI BARU ============//
//                    $harga = \app\models\Harga::find()->where('cabang_id="' . $model->cabang_id . '" and produk_id="' . $det->produk_id . '"')->one();
//                    if (!empty($harga)) {
//                        $harga->harga_beli = $det->harga;
//                        $harga->save();
//                    } else {
//                        $harga = new \app\models\Harga();
//                        $harga->cabang_id = $model->cabang_id;
//                        $harga->produk_id = $det->produk_id;
//                        $harga->harga_beli = $det->harga;
//                        $harga->save();
//                    }


                    $id_det[] = $det->id;
                    if ($model->status == 'clear') {
                        $update = $stok->process('in', $model->tanggal, $model->kode, $det->produk_id, $det->jumlah, $model->cabang_id, $det->harga, $keterangan, $model->id);
                    }
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
        $deleteHutang = Hutang::deleteAll(['pembelian_id' => $model->id]);
        $keterangan = 'pembelian';
        $stok = new KartuStok();
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

    public function actionExcel() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        Yii::error($models);
        return $this->render("excel", ['models' => $models]);
    }

    public function actionCari() {
        session_start();
        $params = $_REQUEST;
        $query = new Query;
        $query->select("pe.*,su.kode as kode_supplier, su.nama as nama_supplier,su.no_tlp as no_tlp, su.email as email, su.alamat as alamat,ca.nama as klinik")
                ->from('pembelian as pe')
                ->join("LEFT JOIN", 'm_supplier as su', 'pe.supplier_id = su.id')
                ->join("LEFT JOIN", 'm_cabang as ca', 'pe.cabang_id = ca.id')
                ->where(['like', 'pe.kode', $params['kode']])
                ->andWhere(['pe.cabang_id' => $_SESSION['user']['cabang_id']])
                ->limit(10);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionLastcode() {
        $params = $_REQUEST;
        $findLast = Pembelian::find()->orderBy("id DESC")->one();
        $number = (empty($findLast)) ? 1 : $findLast->id + 1;
        $lastCode = 'BELI/' . $params['kode'] . '/' . substr('00000' . $number, -5);
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'kode' => $lastCode));
    }

}
