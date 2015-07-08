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
        $s = strtotime(date("Y-m-d", strtotime($params['tanggal']['startDate'])));
        $e = strtotime(date("Y-m-d", strtotime($params['tanggal']['endDate'])));
        $start = date("Y-m-d", strtotime('+1 day', $s));
        $end = date("Y-m-d", strtotime($e));

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
        $s = strtotime(date("Y-m-d", strtotime($params['tanggal']['startDate'])));
        $e = strtotime(date("Y-m-d", strtotime($params['tanggal']['endDate'])));
        $start = date("Y-m-d", strtotime('+1 day', $s));
        $end = date("Y-m-d", strtotime($e));
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
        $data = array();
        $body = array();

        $params = json_decode(file_get_contents("php://input"), true);
        $s = strtotime(date("Y-m-d", strtotime($params['tanggal']['startDate'])));
        $start = date("Y-m-d", strtotime('+1 day', $s));
        $end = date("Y-m-d", strtotime($params['tanggal']['endDate']));

        $data['start'] = $start;
        $data['end'] = $end;

        $criteria = '';

        if (!empty($params['cabang_id'])) {
            $cbg = \app\models\Cabang::findOne(['id' => $params['cabang_id']]);
            $data['cabang'] = strtoupper($cbg->nama);
            $criteria .= ' and kartu_stok.cabang_id = ' . $params['cabang_id'];
        } else {
            $data['cabang'] = 'SEMUA CABANG';
        }

        if (!empty($params['kategori_id'])) {
            $cbg = \app\models\Kategori::findOne(['id' => $params['kategori_id']]);
            $data['kategori'] = strtoupper($cbg->nama);
            $criteria .= ' and m_produk.kategori_id = ' . $params['kategori_id'];
        } else {
            $data['kategori'] = 'SEMUA KATEGORI';
        }

//        $kartu = \app\models\KartuStok::find()->joinWith('m_produk', true, 'RIGHT JOIN')->where("produk.id = kartu_stok.produk_id and (date(created_at) >= '" . $start . "' and date(created_at) <= '" . $end . "') $criteria")->all();
        $query = new Query;
        $query->from(['m_produk', 'm_satuan', 'm_kategori', 'kartu_stok'])
                ->select("kartu_stok.*, m_produk.nama as produk, m_kategori.nama as kategori, m_satuan.nama as satuan")
                ->where("m_produk.kategori_id = m_kategori.id and m_produk.satuan_id = m_satuan.id and m_produk.id = kartu_stok.produk_id and (date(kartu_stok.created_at) >= '" . $start . "' and date(kartu_stok.created_at) <= '" . $end . "') $criteria")
                ->orderBy("kartu_stok.produk_id, kartu_stok.created_at ASC, kartu_stok.id ASC");

        $command = $query->createCommand();
        $kartu = $command->queryAll();
        $i = 0;

        if (empty($kartu)) {
            
        } else {
            $produk_id = 0;
            $created = '';
            $a = 1;
            foreach ($kartu as $val) {
                if ($produk_id != $val['produk_id']) {
                    $tmpSaldo = array('jumlah' => '', 'harga' => '', 'sub_total' => '');
                    $tmpKeluar = array('jumlah' => '', 'harga' => '', 'sub_total' => '');
                    $tmp[1]['jumlah'] = 0;
                    $tmp[1]['harga'] = 0;
                    $a = 1;
                    $totalJml['saldo'] = 0;
                    $totalHarga['saldo'] = 0;
                    $totalJml['masuk'] = 0;
                    $totalHarga['masuk'] = 0;
                    $totalJml['keluar'] = 0;
                    $totalHarga['keluar'] = 0;
                };

//                $totalJml['saldo'] += ($val['jumlah_masuk'] - $val['jumlah_keluar']);
//                $totalHarga['saldo'] += (($val['jumlah_masuk'] - $val['jumlah_keluar']) * $val['harga_masuk']);

                $totalJml['masuk'] += $val['jumlah_masuk'];
                $totalHarga['masuk'] += ($val['jumlah_masuk'] * $val['harga_masuk']);

                $totalJml['keluar'] += $val['jumlah_keluar'];
                $totalHarga['keluar'] += ($val['jumlah_keluar'] * $val['harga_keluar']);

                $tmpSaldo['jumlah'] = $tmpSaldo['jumlah'];
                $tmpSaldo['harga'] = $tmpSaldo['harga'];
                $tmpSaldo['sub_total'] = $tmpSaldo['sub_total'];

                $tmpKeluar['jumlah'] = $tmpKeluar['jumlah'];
                $tmpKeluar['harga'] = $tmpKeluar['harga'];
                $tmpKeluar['sub_total'] = $tmpKeluar['sub_total'];

                $tmpKeluar = array('jumlah' => '', 'harga' => '', 'sub_total' => '');

                if ($val['jumlah_keluar'] == 0) {
                    $tmpSaldo['jumlah'][$a] = $val['jumlah_masuk'];
                    $tmpSaldo['harga'][$a] = (int) $val['harga_masuk'];
                    $tmpSaldo['sub_total'][$a] = ($val['harga_masuk'] * $val['jumlah_masuk']);

                    $tmp[$a]['jumlah'] = $val['jumlah_masuk'];
                    $tmp[$a]['harga'] = $val['harga_masuk'];
                } else {
                    $tempQty = $val['jumlah_keluar'];
                    $boolStatus = true;
                    $tmpSaldo = array('jumlah' => '', 'harga' => '', 'sub_total' => '');
                    $index = 1;
                    foreach ($tmp as $valS) {
                        if ($boolStatus) {
                            if ($valS['jumlah'] > $tempQty) {
                                $valS['jumlah'] -= $tempQty;
                                $tmp[$index]['jumlah'] = $valS['jumlah'];

                                $boolStatus = false;
                                $tmpSaldo['jumlah'][$a] = $valS['jumlah'];
                                $tmpSaldo['harga'][$a] = $valS['harga'];
                                $tmpSaldo['sub_total'][$a] = ($valS['jumlah'] * $valS['harga']);

                                $tmpKeluar['jumlah'][$a] = $tempQty;
                                $tmpKeluar['harga'][$a] = $val['harga_keluar'];
                                $tmpKeluar['sub_total'][$a] = $tempQty * $tmpKeluar['harga'][$a];
                            } else {
                                $tmpKeluar['jumlah'][$a] = $valS['jumlah'];
                                $tmpKeluar['harga'][$a] = $val['harga_keluar'];
                                $tmpKeluar['sub_total'][$a] = $valS['jumlah'] * $tmpKeluar['harga'][$a];

                                $tempQty -= $valS['jumlah'];
                                unset($tmp[$index]);
                            }
                        } else {
                            $tmpSaldo['jumlah'][$a] = $valS['jumlah'];
                            $tmpSaldo['harga'][$a] = $valS['harga'];
                            $tmpSaldo['sub_total'][$a] = ($valS['jumlah'] * $valS['harga']);
                        }
                        $totalJml['saldo'] += isset($tmpSaldo['jumlah'][$a]) ? $tmpSaldo['jumlah'][$a] : 0;
                        $totalHarga['saldo'] += isset($tmpSaldo['sub_total'][$a]) ? $tmpSaldo['sub_total'][$a] : 0;
                        $a++;
                        $index++;
                        $totalJml['saldo'] += isset($valS['jumlah']) ? $valS['jumlah'] : 0;
                        $totalHarga['saldo'] += isset($valS['sub_total']) ? $valS['sub_total'] : 0;
                    }
                }
                $body[$val['produk_id']]['title']['produk'] = $val['produk'];
                $body[$val['produk_id']]['title']['kategori'] = $val['kategori'];
                $body[$val['produk_id']]['title']['satuan'] = $val['satuan'];
                $body[$val['produk_id']]['body'][$i]['tanggal'] = date("Y-m-d", strtotime($val['created_at']));
                $body[$val['produk_id']]['body'][$i]['kode'] = $val['kode'];
                $body[$val['produk_id']]['body'][$i]['keterangan'] = $val['keterangan'];
                $body[$val['produk_id']]['body'][$i]['masuk']['jumlah'] = (int) $val['jumlah_masuk'];
                $body[$val['produk_id']]['body'][$i]['masuk']['harga'] = (int) $val['harga_masuk'];
                $body[$val['produk_id']]['body'][$i]['masuk']['sub_total'] = $val['jumlah_masuk'] * $val['harga_masuk'];
                $body[$val['produk_id']]['body'][$i]['keluar']['jumlah'] = $tmpKeluar['jumlah'];
                $body[$val['produk_id']]['body'][$i]['keluar']['harga'] = $tmpKeluar['harga'];
                $body[$val['produk_id']]['body'][$i]['keluar']['sub_total'] = $tmpKeluar['sub_total'];
                $body[$val['produk_id']]['body'][$i]['saldo']['jumlah'] = $tmpSaldo['jumlah'];
                $body[$val['produk_id']]['body'][$i]['saldo']['harga'] = $tmpSaldo['harga'];
                $body[$val['produk_id']]['body'][$i]['saldo']['sub_total'] = $tmpSaldo['sub_total'];
                $body[$val['produk_id']]['total']['saldo']['jumlah'] = $totalJml['saldo'];
                $body[$val['produk_id']]['total']['saldo']['harga'] = $totalHarga['saldo'];
                $body[$val['produk_id']]['total']['masuk']['jumlah'] = $totalJml['masuk'];
                $body[$val['produk_id']]['total']['masuk']['harga'] = $totalHarga['masuk'];
                $body[$val['produk_id']]['total']['keluar']['jumlah'] = $totalJml['keluar'];
                $body[$val['produk_id']]['total']['keluar']['harga'] = $totalHarga['keluar'];

                $i++;
                $a++;
                $produk_id = $val['produk_id'];
            }
        }
        $grandJml = 0;
        $grandHarga = 0;
        foreach ($body as $val) {
            $grandJml += $val['total']['saldo']['jumlah'];
            $grandHarga += $val['total']['saldo']['harga'];
        }
        $data['grandJml'] = $grandJml;
        $data['grandHarga'] = $grandHarga;

        echo json_encode(array('status' => 1, 'data' => $data, 'detail' => $body), JSON_PRETTY_PRINT);
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
