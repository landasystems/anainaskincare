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

        $_SESSION['filter'] = $param['filter'];

        $start = date("Y-m-d", strtotime($param['filter']['tanggal']['startDate']));
        $end = date("Y-m-d", strtotime($param['filter']['tanggal']['endDate']));

        if (isset($param['filter']['kategori'])) {
            $kategori_id = array();
            foreach ($param['filter']['kategori'] as $val) {
                if (isset($param['filter']['jenis']) and ! empty($param['filter']['jenis'])) {
                    if ($param['filter']['jenis'] == $val['jenis'])
                        $kategori_id[] = $val['id'];
                } else {
                    $kategori_id[] = $val['id'];
                }
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
                ->join('LEFT JOIN', 'm_kategori', 'm_kategori.id = m_produk.kategori_id')
                ->orderBy('penjualan.tanggal ASC, penjualan_det.id ASC')
                ->select('m_user.nama as kasir, penjualan.id as id_penjualan, penjualan.tanggal, penjualan.kode, penjualan.cash, penjualan.credit, penjualan.atm, m_customer.nama as customer, m_produk.nama as produk, penjualan_det.jumlah, penjualan_det.harga, penjualan_det.diskon, penjualan_det.sub_total, penjualan_det.type');

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
                } else if ($key == 'jenis' and ! empty($key)) {
                    $query->andFilterWhere(['m_kategori.jenis' => $val]);
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

        if (isset($filter['jenis']) and ! empty($filter['jenis'])) {
            $detail['kategori'] = 'KATEGORI : ' . strtoupper($filter['jenis']) . '<br>';
        } else {
            $detail['kategori'] = '';
        }

        if (isset($filter['kategori']) and ! empty($filter['kategori'])) {
            $kategori = \app\models\Kategori::findAll(['id' => $kategori_id]);
            $nmKategori = array();
            foreach ($kategori as $val) {
                if (isset($param['filter']['jenis']) and ! empty($param['filter']['jenis'])) {
                    if ($param['filter']['jenis'] == $val['jenis'])
                        $nmKategori[] = strtoupper($val['nama']);
                } else {
                    $nmKategori[] = strtoupper($val['nama']);
                }
            }
            $nama = join(',', $nmKategori);
            $detail['kategori'] .= empty($nmKategori) ? '' : 'SUB KATEGORI : <b>' . $nama . '</b>';
        } else {
            $detail['kategori'] .= '';
        }

        $data = array();
        $total = 0;
        $totalDiskon = 0;
        $totalCash = 0;
        $totalCredit = 0;
        $totalAtm = 0;
        $tempKode = '';
        $tempId = '';
        $indek = 0;
        foreach ($models as $key => $val) {
            $subTotal = ($val['harga'] * $val['jumlah']) - ($val['diskon'] * $val['jumlah']);
            $total += $subTotal;
            $totalDiskon += $val['diskon'];

            if ($tempId != $val['id_penjualan']) {
                $tempId = $val['id_penjualan'];
                $indek ++;

                $totalCash += $val['cash'];
                $totalCredit += $val['credit'];
                $totalAtm += $val['atm'];
                $tempKode = $val['kode'];
            }


            $data[$indek]['tanggal'] = date("d-m-Y", strtotime($val['tanggal']));
            $data[$indek]['kode'] = $val['kode'];
            $data[$indek]['cash'] = Yii::$app->landa->rp($val['cash'], false);
            $data[$indek]['credit'] = Yii::$app->landa->rp($val['credit'], false);
            $data[$indek]['atm'] = Yii::$app->landa->rp($val['atm'], false);
            $data[$indek]['customer'] = $val['customer'];
            $data[$indek]['kasir'] = empty($val['kasir']) ? '-' : $val['kasir'];

            if ($val['type'] == "Paket" && $val['harga'] == 0) {
                $produk = ' &nbsp;&nbsp - ' . strtoupper($val['produk']);
                $harga = '';
                $diskon = '';
                $subT = '';
            } else {
                $produk = strtoupper($val['produk']);
                $harga = Yii::$app->landa->rp($val['harga'], false);
                $diskon = Yii::$app->landa->rp($val['diskon'], false);
                $subT = Yii::$app->landa->rp($subTotal, false);
            }

            $data[$indek]['produk'] = isset($data[$indek]['produk']) ? $data[$indek]['produk'] . '<br>' . $produk : $produk;
            $data[$indek]['jumlah'] = isset($data[$indek]['jumlah']) ? $data[$indek]['jumlah'] . '<br>' . $val['jumlah'] : $val['jumlah'];
            $data[$indek]['harga'] = isset($data[$indek]['harga']) ? $data[$indek]['harga'] . '<br>' . $harga : $harga;
            $data[$indek]['diskon'] = isset($data[$indek]['diskon']) ? $data[$indek]['diskon'] . '<br>' . $diskon : $diskon;
            $data[$indek]['sub_total'] = isset($data[$indek]['sub_total']) ? $data[$indek]['sub_total'] . '<br>' . $subT : $subT;
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
