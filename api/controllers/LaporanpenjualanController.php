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
                    'index' => ['get'],
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
        session_start();

        //init variable
        $params = $_REQUEST;
        $filter = array();
        $sort = "penjualan.id DESC";
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
        $query->from('penjualan')
                ->join('LEFT JOIN', 'penjualan_det', 'penjualan_det.penjualan_id = penjualan.id')
                ->join('LEFT JOIN', 'm_cabang', 'penjualan.cabang_id = m_cabang.id')
                ->join('LEFT JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
                ->join('LEFT JOIN', 'm_produk', 'penjualan_det.produk_id = m_produk.id')
                ->join('LEFT JOIN', 'm_user', 'penjualan.created_by = m_user.id')
                ->orderBy('penjualan.kode DESC')
                ->select('m_user.nama as kasir, penjualan.id as id_penjualan, penjualan.tanggal, penjualan.kode, m_customer.nama as customer, m_produk.nama as produk, penjualan_det.jumlah, penjualan_det.harga, penjualan_det.diskon, penjualan_det.sub_total');

        //filter
        if (isset($params['filter'])) {
            $filter = (array) json_decode($params['filter']);
            foreach ($filter as $key => $val) {
                if ($key != 'm_produk.type') {
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
        }

        $_SESSION['query'] = $filter;

        $command = $query->createCommand();
        $models = $command->queryAll();

        $detail = array();

        if (isset($filter['cabang_id'])) {
            $selCabang = \app\models\Cabang::findOne(['id' => $filter['cabang_id']]);
            $detail['cabang'] = $selCabang['nama'];
            $detail['alamat'] = '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['alamat'] . '</h5>';
            $detail['no_tlp'] = '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['no_tlp'] . '</h5>';
        } else {
            $detail['cabang'] = 'SEMUA CABANG';
            $detail['alamat'] = '';
            $detail['no_tlp'] = '';
        }
        if (isset($filter['tanggal'])) {
            $value = explode(' - ', $filter['tanggal']);
            $start = date("d-m-Y", strtotime($value[0]));
            $end = date("d-m-Y", strtotime($value[1]));
            if ($start == $end)
                $detail['tgl'] = 'TANGGAL : ' . $start;
            else
                $detail['tgl'] = 'TANGGAL : ' . $start . ' - ' . $end;
        }else {
            $tgl = '';
        }
        if (isset($filter['m_produk.kategori_id'])) {
            $kategori = \app\models\Kategori::findOne(['id' => $filter['m_produk.kategori_id']]);
            $detail['kategori'] = 'KATEGORI PRODUK : <b>' . $kategori['nama'] . '</b>';
        } else {
            $detail['kategori'] = '';
        }

        $data = array();
        $total = 0;
        foreach ($models as $key => $val) {
            $subTotal = ($val['harga'] * $val['jumlah']) - ($val['diskon'] * $val['jumlah']);
            $total += $subTotal;
            $data[$val['id_penjualan']]['tanggal'] = date("d-m-Y", strtotime($val['tanggal']));
            $data[$val['id_penjualan']]['kode'] = $val['kode'];
            $data[$val['id_penjualan']]['customer'] = $val['customer'];
            $data[$val['id_penjualan']]['kasir'] = empty($val['kasir']) ? '-' : $val['kasir'];
            $data[$val['id_penjualan']]['produk'] = isset($data[$val['id_penjualan']]['produk']) ? $data[$val['id_penjualan']]['produk'] . '<br>' . strtoupper($val['produk']) : strtoupper($val['produk']);
            $data[$val['id_penjualan']]['jumlah'] = isset($data[$val['id_penjualan']]['jumlah']) ? $data[$val['id_penjualan']]['jumlah'] . '<br>' . $val['jumlah'] : $val['jumlah'];
            $data[$val['id_penjualan']]['harga'] = isset($data[$val['id_penjualan']]['harga']) ? $data[$val['id_penjualan']]['harga'] . '<br>' . $val['harga'] : $val['harga'];
            $data[$val['id_penjualan']]['diskon'] = isset($data[$val['id_penjualan']]['diskon']) ? $data[$val['id_penjualan']]['diskon'] . '<br>' . $val['diskon'] : $val['diskon'];
            $data[$val['id_penjualan']]['sub_total'] = isset($data[$val['id_penjualan']]['sub_total']) ? $data[$val['id_penjualan']]['sub_total'] . '<br>' . $subTotal : $subTotal;
        }
        
        $detail['total'] = $total;

        $totalItems = count($models);

        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'detail' => $detail, 'data' => $data, 'totalItems' => $totalItems), JSON_PRETTY_PRINT);
    }

    public function actionExcel() {
        session_start();
        $filter = $_SESSION['query'];
        return $this->render("excel", ['filter' => $filter]);
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
