<?php

namespace app\controllers;

use Yii;
use app\models\Barang;
use app\models\KartuStok;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class BarangController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'excel' => ['get'],
                    'excellaporan' => ['get'],
                    'create' => ['post'],
                    'update' => ['post'],
                    'delete' => ['delete'],
                    'kategori' => ['get'],
                    'satuan' => ['get'],
                    'caribarang' => ['get'],
                    'cari' => ['get'],
                    'cari2' => ['get'],
                    'carilagi' => ['post'],
                    'getstok' => ['get'],
                    'getpaket' => ['get'],
                    'perkategori' => ['post'],
//                    'getharga' => ['get'], AKTIFKAN JIKA HARGA PER CABANG BERBEDA
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
        $sort = "mp.kode ASC";
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
        $query->from('m_produk as mp')
                ->join('Left Join', 'm_satuan as ms', 'mp.satuan_id = ms.id')
                ->join('Left Join', 'm_kategori as mk', 'mk.id = mp.kategori_id')
                ->select('mp.*, ms.nama as satuan, mk.nama as kategori')
                ->offset($offset)
                ->limit($limit)
                ->orderBy($sort);

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key != "cabang") {
                    if ($key == 'is_deleted') {
                        $query->andFilterWhere(['like', 'mp.' . $key, $val]);
                    } else {
                        $query->andFilterWhere(['like', $key, $val]);
                    }
                }
            }
        }

        session_start();
        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();
        $totalItems = $query->count();

        $this->setHeader(200);

        $data = array();
        foreach ($models as $key => $val) {
            $stok = 0;

            $cabang = isset($filter['cabang']) ? $filter['cabang'] : $_SESSION['user']['cabang'][0]['id'];

            $st = new Barang;
            $stok = $st->stok($val['id'], $cabang);

            //============== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ==========//
//            $harga = \app\models\Harga::find()->where('cabang_id = "' . $cabang . '" and produk_id="' . $val['id'] . '" ')->one();
//            $hargaJual = isset($harga['harga_jual']) ? $harga['harga_jual'] : $val['harga_jual'];

            $data[$key] = $val;
//            $data[$key]['harga_jual'] = $hargaJual; AKTIFKAN JIKA HARGA PER CABANG BERBEDA
            $data[$key]['harga_jual'] = $val['harga_jual'];
            $data[$key]['stok'] = $stok;
        }

        echo json_encode(array('status' => 1, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionGetstok($id) {
        $listStok = array();
        session_start();
        $cabang = $_SESSION['user']['cabang'];
        $n = 1;
        $total = 0;
        foreach ($cabang as $vals) {
            $st = new Barang;
            $stok = $st->stok($id, $vals['id']);

            $listStok[$n]['no'] = $n;
            $listStok[$n]['id'] = isset($vals['id']) ? $vals['id'] : '-';
            $listStok[$n]['nama'] = isset($vals['nama']) ? $vals['nama'] : '-';
            $listStok[$n]['stok'] = $stok;
            $total += $stok;
            $n++;
        }

        echo json_encode(array('status' => 1, 'data' => $listStok, 'total' => $total), JSON_PRETTY_PRINT);
    }

    public function actionGetpaket($id) {
        $listPaket = array();

        $query = new Query;
        $query->from('paket_det as pd')
                ->join('Left JOIN', 'm_produk as mp', 'pd.barang_id = mp.id')
                ->select('mp.nama, mp.id as barang_id, pd.*')
                ->where('pd.paket_id = ' . $id);
        $command = $query->createCommand();
        $models = $command->queryAll();

        $total = 0;
        foreach ($models as $key => $val) {
            $listPaket[$key] = $val;
//            $listPaket[$key]['harga'] = $val['harga_jual'];
            $listPaket[$key]['produk'] = array('id' => $val['barang_id'], 'nama' => $val['nama']);
//            $listPaket[$key]['total'] = ($val['jml'] * $val['harga_jual']);
//            $total += ($val['jml'] * $val['harga_jual']);
        }

        echo json_encode(array('status' => 1, 'data' => $listPaket, 'total' => $total), JSON_PRETTY_PRINT);
    }

//======== AKTIFKAN JIKA HARGA PER CABANG BERBEDA ===========//
//    public function actionGetharga($id) {
//        $listHarga = array();
//        session_start();
//        $cabang = $_SESSION['user']['cabang'];
//        $n = 1;
//        foreach ($cabang as $vals) {
//            $harga = \app\models\Harga::find()->where('cabang_id = "' . $vals['id'] . '" and produk_id="' . $id . '" ')->one();
//
//            $listHarga[$n]['no'] = $n;
//            $listHarga[$n]['cabang_id'] = isset($harga->cabang_id) ? $harga->cabang_id : 0;
//            $listHarga[$n]['id'] = isset($harga['id']) ? $harga['id'] : '-';
//            $listHarga[$n]['nama'] = isset($vals['nama']) ? $vals['nama'] : '-';
//            $listHarga[$n]['harga_beli'] = isset($harga->harga_beli) ? $harga->harga_beli : 0;
//            $listHarga[$n]['harga_jual'] = isset($harga->harga_jual) ? $harga->harga_jual : 0;
//
//            $n++;
//        }
//
//        echo json_encode(array('status' => 1, 'data' => $listHarga), JSON_PRETTY_PRINT);
//    }

    public function actionView($id) {

        $model = $this->findModel($id);

        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
    }

    public function actionCreate() {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = new Barang();
        $model->attributes = $params['form'];

        if ($model->save()) {

            if ($model->type == 'Barang' and isset($params['stok'])) {
                $sMasuk = $params['stok'];
                foreach ($sMasuk as $vMasuk) {
                    if (isset($vMasuk['iStok']) and $vMasuk['iStok'] > 0) {
                        $kartu = new \app\models\KartuStok();
                        $kartu->kode = $model->kode;
                        $kartu->produk_id = $model->id;
                        $kartu->cabang_id = $vMasuk['id'];
                        $kartu->keterangan = 'Stok Awal';
                        $kartu->jumlah_masuk = $vMasuk['iStok'];
                        $kartu->harga_masuk = $model->harga_beli_terakhir;
                        $kartu->created_at = date("Y-m-d H:i:s");
                        $kartu->save();
                    }
                }

                //================ AKTIFKAN JIKA HARGA PER CABANG BERBEDA =============//
//                $sHarga = $params['harga'];
//                foreach ($sHarga as $vHarga) {
//                    $harga = new \app\models\Harga();
//                    $harga->cabang_id = $vHarga['id'];
//                    $harga->produk_id = $model->id;
//                    $harga->harga_beli = isset($vHarga['harga_beli']) ? $vHarga['harga_beli'] : 0;
//                    $harga->harga_jual = isset($vHarga['harga_jual']) ? $vHarga['harga_jual'] : 0;
//                    $harga->save();
//                }
            }

            if ($model->type == 'Paket' and isset($params['paket'])) {
                $sPaket = $params['paket'];
                foreach ($sPaket as $vPaket) {
                    if (isset($vPaket['jml']) and $vPaket['jml'] > 0) {
                        $paket = new \app\models\PaketDet();
                        $paket->barang_id = $vPaket['produk']['id'];
                        $paket->paket_id = $model->id;
//                        $paket->harga_jual = $vPaket['harga'];
                        $paket->jml = $vPaket['jml'];
                        $paket->save();
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

    public function actionKategori() {
        $query = new Query;
        $query->from('m_kategori')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'kategori' => $models));
    }

    public function actionSatuan() {
        $query = new Query;
        $query->from('m_satuan')
                ->select('*')
                ->where("is_deleted = '0'");

        $command = $query->createCommand();
        $models = $command->queryAll();

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'satuan' => $models));
    }

    public function actionUpdate($id) {
        $params = json_decode(file_get_contents("php://input"), true);
        $model = $this->findModel($id);
        $ft = $model->foto;
        $model->attributes = isset($params['form']) ? $params['form'] : $params;
        if (empty($model->foto)) {
            $model->foto = $ft;
        }

        //================ AKTIFKAN JIKA HARGA PER CABANG BERBEDA ===========//
//        $sHarga = $params['harga'];
//        foreach ($sHarga as $vHarga) {
//            $harga = \app\models\Harga::find()->where('id = ' . $vHarga['id'])->one();
//            if (empty($harga)) {
//                $harga = new \app\models\Harga();
//            }
//            $harga->produk_id = $model->id;
//            $harga->cabang_id = isset($vHarga['cabang_id']) ? $vHarga['cabang_id'] : 0;
//            $harga->harga_beli = isset($vHarga['harga_beli']) ? $vHarga['harga_beli'] : 0;
//            $harga->harga_jual = isset($vHarga['harga_jual']) ? $vHarga['harga_jual'] : 0;
//            $harga->save();
//        }

        if ($model->save()) {
            if ($model->type == 'Paket' and isset($params['paket'])) {
                $sPaket = $params['paket'];
                $delPaketDet = \app\models\PaketDet::deleteAll("paket_id=" . $model->id);
                foreach ($sPaket as $vPaket) {
                    if (isset($vPaket['jml']) and $vPaket['jml'] > 0) {
                        $paket = new \app\models\PaketDet();
                        $paket->barang_id = $vPaket['produk']['id'];
                        $paket->paket_id = $model->id;
//                        $paket->harga_jual = $vPaket['harga'];
                        $paket->jml = $vPaket['jml'];
                        $paket->save();
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
        $delPaketDet = \app\models\PaketDet::deleteAll("paket_id=" . $model->id);
        $delKartu = \app\models\KartuStok::deleteAll('produk_id = ' . $id . ' and kode = "' . $model->kode . '"');
//        $delHarga = \app\models\Harga::deleteAll('produk_id = ' . $id); AKTIFKAN JIKA HARGA PER CABANG BERBEDA
//        if ($model->type == "Paket")


        if ($model->delete()) {
            $this->setHeader(200);
            echo json_encode(array('status' => 1, 'data' => array_filter($model->attributes)), JSON_PRETTY_PRINT);
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'errors' => $model->errors), JSON_PRETTY_PRINT);
        }
    }

    protected function findModel($id) {
        if (($model = Barang::findOne($id)) !== null) {
            return $model;
        } else {

            $this->setHeader(400);
            echo json_encode(array('status' => 0, 'error_code' => 400, 'message' => 'Bad request'), JSON_PRETTY_PRINT);
            exit;
        }
    }

    public function actionCari2() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('m_produk')
                ->join('LEFT JOIN', 'harga', 'harga.produk_id = m_produk.id')
                ->select("m_produk.*, harga.harga_beli as harga_beli_cabang, harga.harga_jual as harga_jual_cabang")
                ->where(['is_deleted' => 0])
//                ->where(['is_deleted' => 0, 'type' => 'barang'])
                ->andWhere(['like', 'nama', $params['nama']])
                ->orWhere(['like', 'kode', $params['nama']])
                ->andWhere(['=', 'harga.cabang_id', $params['cabang']]);
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);

        $data = array();
        foreach ($models as $key => $val) {
            $data[$key] = $val;

            if ($val['harga_beli_cabang'] > 0) {
                $data[$key]['harga_beli_terakhir'] = $data[$key]['harga_beli_cabang'];
            }

            if ($val['harga_jual_cabang'] > 0) {
                $data[$key]['harga_jual'] = $data[$key]['harga_jual_cabang'];
            }
        }

        echo json_encode(array('status' => 1, 'data' => $data));
    }

    public function actionPerkategori() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('m_produk')
                ->select("m_produk.*")
                ->where(['is_deleted' => 0])
                ->andWhere(['like', 'nama', $params['nama']])
                ->orderBy('nama ASC')
                ->limit(10);

        if (isset($params['kategori_id']) and ! empty($params['kategori_id'])) {
            $query->andWhere('kategori_id = "' . $params['kategori_id'] . '"')
                    ->limit(null);
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCaribarang() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('m_produk')
                ->select("m_produk.*")
//                ->where(['is_deleted' => 0, 'type' => 'barang'])
                ->where(['is_deleted' => 0])
                ->andWhere(['like', 'nama', $params['nama']])
                ->orWhere(['like', 'kode', $params['nama']]);

        if (isset($params['kategori']['id'])) {
            $query->andWhere(['=', 'kategori_id', $params['kategori']['id']]);
        }

        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCari() {
        $params = $_REQUEST;
        $query = new Query;
        $query->from('m_produk')
                ->select("m_produk.*")
                ->where(['is_deleted' => 0])
                ->andWhere(['like', 'nama', $params['nama']])
                ->orWhere(['like', 'kode', $params['nama']]);
        
        if(isset($_GET['type']))
            $query->andWhere (['=','type',$_GET['type']]);
        
        $command = $query->createCommand();
        $models = $command->queryAll();
        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $models));
    }

    public function actionCarilagi() {
        $params = json_decode(file_get_contents("php://input"), true);
        $query = new Query;
        $query->from('m_produk')
                ->select("m_produk.*")
//                ->where(['is_deleted' => 0, 'type' => 'Barang'])
                ->where(['is_deleted' => 0])
                ->orderBy('nama ASC')
                ->andWhere(['like', 'nama', $params['nama']]);

        $command = $query->createCommand();
        $models = $command->queryAll();
        if (!isset($params['cabang'])) {
            $data = $models;
        } else {
            $data = array();
            $n = 0;
            foreach ($models as $key => $val) {
                $st = new Barang;
                $stok = $st->stok($val['id'], $params['cabang']);

                $data[$key] = $val;
                $data[$n]['stok'] = $stok;

                $n++;
            }
        }



        $this->setHeader(200);
        echo json_encode(array('status' => 1, 'data' => $data));
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

    public function actionExcellaporan() {
        session_start();
        $query = $_SESSION['query'];
        $query->offset("");
        $query->limit("");
        $command = $query->createCommand();
        $models = $command->queryAll();
        return $this->render("excellaporan", ['models' => $models]);
    }

}

?>
