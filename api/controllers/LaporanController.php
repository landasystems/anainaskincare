<?php

namespace app\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;

class LaporanController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'bonus' => ['post'],
                    'labarugi' => ['post'],
                    'kartustok' => ['post'],
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

    public function actionBonus() {
        $params = json_decode(file_get_contents("php://input"), true);
        $detail = array();
        $start = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $end = date("Y-m-d", strtotime($params['tanggal']['endDate']));

        $detail['start'] = $start;
        $detail['end'] = $end;

        $criteria = ' and penjualan.tanggal >= "' . $start . '" and penjualan.tanggal <= "' . $end . '"';

        if (!empty($params['cabang_id'])) {
            $cbg = \app\models\Cabang::findOne(['id' => $params['cabang_id']]);
            $detail['cabang'] = strtoupper($cbg->nama);
            $criteria .= ' and penjualan.cabang_id = ' . $params['cabang_id'];
        } else {
            $detail['cabang'] = 'SEMUA CABANG';
        }

        if (!empty($params['pegawai_id'])) {
            $pgw = \app\models\Pegawai::findOne(['id' => $params['pegawai_id']]);
            $detail['pegawai'] = $pgw['nama'];
            if ($pgw->jabatan == "terapis") {
                $criteria .= ' and penjualan_det.pegawai_terapis_id = ' . $params['pegawai_id'];
            } else {
                $criteria .= ' and penjualan_det.pegawai_dokter_id = ' . $params['pegawai_id'];
            }
        } else {
            $detail['pegawai'] = 'SEMUA PEGAWAI';
        }

        //create query
        $query = new Query;
        $query->from(['m_pegawai', 'm_produk', 'penjualan', 'penjualan_det'])
                ->select("m_pegawai.id as id_pegawai , m_produk.nama as produk, m_pegawai.nama as pegawai, penjualan.tanggal as tanggal, penjualan.kode as kode, m_pegawai.jabatan as jabatan, penjualan_det.fee_terapis as fee_terapis, penjualan_det.fee_dokter as fee_dokter")
                ->where("(m_pegawai.id = penjualan_det.pegawai_terapis_id or m_pegawai.id = penjualan_det.pegawai_dokter_id) and penjualan_det.produk_id = m_produk.id and penjualan.id = penjualan_det.penjualan_id $criteria");

        $command = $query->createCommand();
        $models = $command->queryAll();
        $body = array();
        $total = 0;
        $totalA = 0;
        foreach ($models as $val) {

            if (isset($body[$val['id_pegawai']])) {
                $i = $i;
            } else {
                $i = 0;
            }

            if ($val['jabatan'] == "terapis") {
                $fee = $val['fee_terapis'];
            } else {
                $fee = $val['fee_dokter'];
            }

            if (!empty($params['pegawai_id'])) {
                $pegawai_id = $params['pegawai_id'];
                if ($val['jabatan'] == $pgw->jabatan) {
                    $body[$pegawai_id]['title']['nama'] = $val['pegawai'];
                    $body[$pegawai_id]['title']['sub_total'] = isset($body[$val['id_pegawai']]['title']['sub_total']) ? $body[$val['id_pegawai']]['title']['sub_total'] += $fee : $fee;
                    $body[$pegawai_id]['body'][$i]['tanggal'] = $val['tanggal'];
                    $body[$pegawai_id]['body'][$i]['kode'] = $val['kode'];
                    $body[$pegawai_id]['body'][$i]['produk'] = $val['produk'];
                    $body[$pegawai_id]['body'][$i]['jabatan'] = $val['jabatan'];
                    $body[$pegawai_id]['body'][$i]['fee'] = $fee;

                    $totalA += $body[$pegawai_id]['body'][$i]['fee'];
                }
            } else {
                $pegawai_id = $val['id_pegawai'];
                $body[$pegawai_id]['title']['nama'] = $val['pegawai'];
                $body[$pegawai_id]['title']['sub_total'] = isset($body[$val['id_pegawai']]['title']['sub_total']) ? $body[$val['id_pegawai']]['title']['sub_total'] += $fee : $fee;
                $body[$pegawai_id]['body'][$i]['tanggal'] = $val['tanggal'];
                $body[$pegawai_id]['body'][$i]['kode'] = $val['kode'];
                $body[$pegawai_id]['body'][$i]['produk'] = $val['produk'];
                $body[$pegawai_id]['body'][$i]['jabatan'] = $val['jabatan'];
                $body[$pegawai_id]['body'][$i]['fee'] = $fee;

                $totalA += $body[$pegawai_id]['body'][$i]['fee'];
            }


            $i++;
        }
        $detail['total'] = $totalA;
        $this->setHeader(200);

        echo json_encode(array('status' => 1, 'data' => $body, 'detail' => $detail), JSON_PRETTY_PRINT);
    }

    public function actionLabarugi() {
        $params = json_decode(file_get_contents("php://input"), true);
        $data = array();
        $start = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $end = date("Y-m-d", strtotime($params['tanggal']['endDate']));
        if (!empty($params['cabang_id'])) {
            $cbg = \app\models\Cabang::findOne(['id' => $params['cabang_id']]);
            $data['cabang'] = strtoupper($cbg->nama);
            $criteria = ' and penjualan.cabang_id = ' . $params['cabang_id'];
        } else {
            $data['cabang'] = 'SEMUA CABANG';
            $criteria = '';
        }
        $connection = \Yii::$app->db;

        $data['start'] = $start;
        $data['end'] = $end;

        //penjualan gross
        $penjualan = $connection->createCommand("SELECT sum(cash) as  penjualan FROM penjualan where (tanggal >= '" . $start . "' and tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['penjualan'] = empty($penjualan['penjualan']) ? 0 : $penjualan['penjualan'];

        //pembayaran piutang
        $penjualan = $connection->createCommand("SELECT sum(pinjaman.credit) as pinjaman FROM pinjaman, penjualan where pinjaman.penjualan_id = penjualan.id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['pemb_piutang'] = empty($penjualan['pinjaman']) ? 0 : $penjualan['pinjaman'];

        //diskon, bonus terapis, bonus dokter
        $penjualan = $connection->createCommand("SELECT sum(penjualan_det.diskon) as diskon, sum(penjualan_det.fee_terapis) as bonus_terapis, sum(penjualan_det.fee_dokter) as bonus_dokter FROM penjualan, penjualan_det where penjualan.id=penjualan_det.penjualan_id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['diskon'] = empty($penjualan['diskon']) ? 0 : $penjualan['diskon'];
        $data['bonus_terapis'] = empty($penjualan['bonus_terapis']) ? 0 : $penjualan['bonus_terapis'];
        $data['bonus_dokter'] = empty($penjualan['bonus_dokter']) ? 0 : $penjualan['bonus_dokter'];

        $criteria = !empty($params['cabang_id']) ? ' and pembelian.cabang_id = ' . $params['cabang_id'] : '';

        $data['total_nett'] = $data['penjualan'] + $data['pemb_piutang'] - $data['diskon'] - $data['bonus_terapis'] - $data['bonus_dokter'];

        //hpp
        $pembelian = $connection->createCommand("SELECT sum(cash) as  pembelian FROM pembelian where (tanggal >= '" . $start . "' and tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['pembelian'] = empty($pembelian['pembelian']) ? 0 : $pembelian['pembelian'];

        //pembayaran hutang
        $pembelian = $connection->createCommand("SELECT sum(hutang.credit) as pemb_hutang FROM hutang, pembelian where (pembelian.tanggal >= '" . $start . "' and pembelian.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['pemb_hutang'] = empty($pembelian['pemb_hutang']) ? 0 : $pembelian['pemb_hutang'];

        $data['laba_kotor'] = $data['total_nett'] - $data['pemb_hutang'] - $data['pembelian'];

        echo json_encode(array('status' => 1, 'data' => $data), JSON_PRETTY_PRINT);
    }

    public function actionKartustok() {
        $params = json_decode(file_get_contents("php://input"), true);
        $start = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $end = date("Y-m-d", strtotime($params['tanggal']['endDate']));
        $data = array();
        
    }

    protected function findModel($id) {
        if (($model = Cabang::findOne($id)) !== null) {
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
