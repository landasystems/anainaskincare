<?php

namespace app\controllers;

use Yii;
use app\models\Customer;
use app\models\Barang;
use app\models\Penjualan;
use app\models\PenjualanDet;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class LaporanpenjualanController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'laporan' => ['post'],
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

    public function actionLaporan() {

        session_start();

        //init variable
        $param = json_decode(file_get_contents("php://input"), true);

        $filter = $param['filter'];
        $_SESSION['filter'] = $param['filter'];

        $start = date("Y-m-d", strtotime($param['filter']['tanggal']['startDate']));
        $end = date("Y-m-d", strtotime($param['filter']['tanggal']['endDate']));

        if (isset($param['filter']['kategori'])) {
            $kategori_id = array();
            foreach ($param['filter']['kategori'] as $val) {
                $kategori_id[] = $val['id'];
            }
        }

        if (isset($param['filter']['kasir'])) {
            $kasir = array();
            foreach ($param['filter']['kasir'] as $val) {
                $kasir[] = $val['id'];
            }
        }

        $query = new Query;
        $query->from('penjualan')
                ->join('LEFT JOIN', 'penjualan_det', 'penjualan_det.penjualan_id = penjualan.id')
                ->join('LEFT JOIN', 'm_cabang', 'penjualan.cabang_id = m_cabang.id')
                ->join('LEFT JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('LEFT JOIN', 'm_produk', 'penjualan_det.produk_id = m_produk.id')
                ->join('LEFT JOIN', 'm_user', 'penjualan.created_by = m_user.id')
                ->orderBy('penjualan.kode')
                ->select('m_user.nama as kasir, penjualan.id as id_penjualan, penjualan.tanggal, penjualan.kode, penjualan.cash, penjualan.credit, penjualan.atm, m_customer.nama as customer, m_produk.nama as produk, penjualan_det.jumlah, penjualan_det.harga, penjualan_det.diskon, penjualan_det.sub_total');

        if (isset($param['filter'])) {
            $filter = $param['filter'];
            foreach ($filter as $key => $val) {
                if ($key == 'tanggal') {
                    $query->andFilterWhere(['between', 'tanggal', $start, $end]);
                } else if ($key == 'cabang') {
                    $query->andFilterWhere(['like', 'penjualan.cabang_id', $val]);
                } else if ($key == 'kategori') {
                    $query->andFilterWhere(['m_produk.kategori_id' => $kategori_id]);
                } else if ($key == 'kasir') {
                    $query->andFilterWhere(['penjualan.created_by' => $kasir]);
                }
            }
        }

        $_SESSION['query'] = $query;

        $command = $query->createCommand();
        $models = $command->queryAll();

        $detail = array();

        if (isset($filter['cabang'])) {
            $selCabang = \app\models\Cabang::findOne(['id' => $filter['cabang']]);
            $detail['cabang'] = $selCabang['nama'];
            $detail['alamat'] = '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['alamat'] . '</h5>';
            $detail['no_tlp'] = '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['no_tlp'] . '</h5>';
        } else {
            $detail['cabang'] = 'SEMUA CABANG';
            $detail['alamat'] = '';
            $detail['no_tlp'] = '';
        }
        if (isset($filter['tanggal'])) {
            $start = date("d-m-Y", strtotime($start));
            $end = date("d-m-Y", strtotime($end));
            if ($start == $end)
                $detail['tgl'] = 'TANGGAL : ' . $start;
            else
                $detail['tgl'] = 'TANGGAL : ' . $start . ' - ' . $end;
        }else {
            $tgl = '';
        }
        if (isset($filter['kategori'])) {
            $kategori = \app\models\Kategori::findAll(['id' => $kategori_id]);
            $nmKategori = array();
            foreach ($kategori as $val) {
                $nmKategori[] = strtoupper($val['nama']);
            }
            $nama = join(',', $nmKategori);
            $detail['kategori'] = 'KATEGORI PRODUK : <b>' . $nama . '</b>';
        } else {
            $detail['kategori'] = '';
        }

        $data = array();
        $total = 0;
        $totalDiskon = 0;
        $totalCash = 0;
        $totalCredit = 0;
        $totalAtm = 0;
        $tempKode = '';
        foreach ($models as $key => $val) {
            $subTotal = ($val['harga'] * $val['jumlah']) - ($val['diskon'] * $val['jumlah']);
            $total += $subTotal;
            $totalDiskon += $val['diskon'];
            
            if ($tempKode != $val['kode']) {
                $totalCash += $val['cash'];
                $totalCredit += $val['credit'];
                $totalAtm += $val['atm'];
                $tempKode = $val['kode'];
            }
            $data[$val['id_penjualan']]['tanggal'] = date("d-m-Y", strtotime($val['tanggal']));
            $data[$val['id_penjualan']]['kode'] = $val['kode'];
            $data[$val['id_penjualan']]['cash'] = $val['cash'];
            $data[$val['id_penjualan']]['credit'] = $val['credit'];
            $data[$val['id_penjualan']]['atm'] = $val['atm'];
            $data[$val['id_penjualan']]['customer'] = $val['customer'];
            $data[$val['id_penjualan']]['kasir'] = empty($val['kasir']) ? '-' : $val['kasir'];
            $data[$val['id_penjualan']]['produk'] = isset($data[$val['id_penjualan']]['produk']) ? $data[$val['id_penjualan']]['produk'] . '<br>' . strtoupper($val['produk']) : strtoupper($val['produk']);
            $data[$val['id_penjualan']]['jumlah'] = isset($data[$val['id_penjualan']]['jumlah']) ? $data[$val['id_penjualan']]['jumlah'] . '<br>' . $val['jumlah'] : $val['jumlah'];
            $data[$val['id_penjualan']]['harga'] = isset($data[$val['id_penjualan']]['harga']) ? $data[$val['id_penjualan']]['harga'] . '<br>' . $val['harga'] : $val['harga'];
            $data[$val['id_penjualan']]['diskon'] = isset($data[$val['id_penjualan']]['diskon']) ? $data[$val['id_penjualan']]['diskon'] . '<br>' . $val['diskon'] : $val['diskon'];
            $data[$val['id_penjualan']]['sub_total'] = isset($data[$val['id_penjualan']]['sub_total']) ? $data[$val['id_penjualan']]['sub_total'] . '<br>' . $subTotal : $subTotal;
        }

        $detail['totalCash'] = $totalCash;
        $detail['totalCredit'] = $totalCredit;
        $detail['totalAtm'] = $totalAtm;
        $detail['total'] = $total;
        $detail['totalDiskon'] = $totalDiskon;

        $totalItems = count($models);

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'detail' => $detail, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
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
