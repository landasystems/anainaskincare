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

        session_start();
        $_SESSION['param'] = $params;

        if (isseT($_GET['is_excel'])) {
            $params = $_SESSION['param'];
        } else {
            $params = $params;
        }

        $detail = array();
        $s = strtotime(date("Y-m-d", strtotime($params['tanggal']['startDate'])));
        $start = date("Y-m-d", strtotime('+1 day', $s));
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

        if (!isset($_GET['is_excel'])) {
            echo json_encode(array('status' => 1, 'data' => $body, 'detail' => $detail), JSON_PRETTY_PRINT);
        } else {
            return $this->render("/laporan/excelBonus", ['data' => $detail, 'detail' => $body]);
        }
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

        $data['tanggal_saldo'] = date("Y-m-d", strtotime($params['tanggal']['startDate']));
        $data['start'] = $start;
        $data['end'] = $end;

        $criteria = '';

        if (!empty($params['cabang_id'])) {
            $cbg = \app\models\Cabang::findOne(['id' => $params['cabang_id']]);
            $data['cabang'] = strtoupper($cbg->nama);
            $criteria .= ' and ks.cabang_id = ' . $params['cabang_id'];
            $cabang = $params['cabang_id'];
        } else {
            $data['cabang'] = 'SEMUA CABANG';
            $cabang = '';
        }

        if (!empty($params['kategori_id'])) {
            $cbg = \app\models\Kategori::findOne(['id' => $params['kategori_id']]);
            $data['kategori'] = strtoupper($cbg->nama);
            $criteria .= ' and mp.kategori_id = ' . $params['kategori_id'];
            $kategori = $params['kategori_id'];
        } else {
            $data['kategori'] = 'SEMUA KATEGORI';
            $kategori = '';
        }

        //mencari saldo awal per kategori
        $tes = new \app\models\KartuStok();
        $saldoAwal = $tes->saldo('balance', $cabang, $kategori, $start);

        //mencari semua produk per kategori
        $ktg_id = !empty($params['kategori_id']) ? 'and kategori_id = ' . $params['kategori_id'] : '';
        $produk = \app\models\Barang::find()->with(['kategori', 'satuan'])->where("is_deleted = 0 and type='Barang' $ktg_id")->all();

        //mencari data transaksi di table kartu stok
        $query = new Query;
        $query->select("ks.*")
                ->from('kartu_stok as ks')
                ->join('JOIN', 'm_produk as mp', 'ks.produk_id = mp.id')
                ->where("mp.is_deleted = 0 and mp.type = 'Barang' and (date(ks.created_at) >= '" . $start . "' and date(ks.created_at) <= '" . $end . "') $criteria")
                ->orderBy("ks.produk_id, ks.created_at ASC, ks.id ASC");

        $command = $query->createCommand();
        $kartu = $command->queryAll();
        $a = 1;
        $pr = 0;

        foreach ($produk as $pro) {
            if ($pr != $pro->id) {
                $a = 1;
            }

            $tmpSaldo['jumlah'][$a] = 0;
            $tmpSaldo['harga'][$a] = 0;
            $tmpSaldo['sub_total'][$a] = 0;

            $tmpS[$pro->id][$a]['jumlah'] = 0;
            $tmpS[$pro->id][$a]['harga'] = 0;

            if (!empty($saldoAwal[$pro->id])) {
                foreach ($saldoAwal[$pro->id] as $sAwal) {
                    if (isset($sAwal['jumlah'])) {
                        $tmpSaldo['jumlah'][$a] = (int) isset($sAwal['jumlah']) ? $sAwal['jumlah'] : 0;
                        $tmpSaldo['harga'][$a] = (int) isset($sAwal['harga']) ? $sAwal['harga'] : 0;
                        $tmpSaldo['sub_total'][$a] = (int) $tmpSaldo['jumlah'][$a] * $tmpSaldo['harga'][$a];

                        $tmpS[$pro->id][$a]['jumlah'] = $tmpSaldo['jumlah'][$a];
                        $tmpS[$pro->id][$a]['harga'] = $tmpSaldo['harga'][$a];
                    }
                }
            }

            $body[$pro->id]['title']['produk'] = $pro->nama;
            $body[$pro->id]['title']['kategori'] = $pro->kategori->nama;
            $body[$pro->id]['title']['satuan'] = $pro->satuan->nama;
            $body[$pro->id]['saldo_awal']['jumlah'] = $tmpSaldo['jumlah'];
            $body[$pro->id]['saldo_awal']['harga'] = $tmpSaldo['harga'];
            $body[$pro->id]['saldo_awal']['sub_total'] = $tmpSaldo['sub_total'];
            $body[$pro->id]['total']['saldo']['jumlah'] = array_sum($tmpSaldo['jumlah']);
            $body[$pro->id]['total']['saldo']['harga'] = array_sum($tmpSaldo['sub_total']);

            unset($tmpSaldo);
            $pr = $pro->id;
        }

        if (empty($kartu)) {
            
        } else {
            $i = 0;
            $produk_id = 0;
            $created = '';
            foreach ($kartu as $val) {
                if ($produk_id != $val['produk_id']) {
                    $tmpKeluar = array('jumlah' => '', 'harga' => '', 'sub_total' => '');
                    $a = count($tmpS[$val['produk_id']]) + 1;

                    //hapus temporary
                    unset($tmp);

                    $tmpSaldo = array('jumlah' => '', 'harga' => '', 'sub_total' => '');

                    //memasukkan temporari saldo awal
                    for ($s = 1; $s <= count($tmpS[$val['produk_id']]); $s++) {
                        if (isset($tmpS[$val['produk_id']][$s]['jumlah'])) {
                            $tmp[$s]['jumlah'] = (int) $tmpS[$val['produk_id']][$s]['jumlah'];
                            $tmp[$s]['harga'] = (int) $tmpS[$val['produk_id']][$s]['harga'];

                            //memasang saldo awal
                            if ($tmpS[$val['produk_id']][$s]['harga'] > 0) {
                                $tmpSaldo['jumlah'][$s] = (int) $tmpS[$val['produk_id']][$s]['jumlah'];
                                $tmpSaldo['harga'][$s] = (int) $tmpS[$val['produk_id']][$s]['harga'];
                                $tmpSaldo['sub_total'][$s] = $tmpSaldo['jumlah'][$s] * $tmpSaldo['harga'][$s];
                            }
                        }
                    }

                    $totalJml['saldo'] = 0;
                    $totalHarga['saldo'] = 0;
                    $totalJml['masuk'] = 0;
                    $totalHarga['masuk'] = 0;
                    $totalJml['keluar'] = 0;
                    $totalHarga['keluar'] = 0;
                }

                $totalJml['masuk'] += $val['jumlah_masuk'];
                $totalHarga['masuk'] += ($val['jumlah_masuk'] * $val['harga_masuk']);

                $totalJml['keluar'] += $val['jumlah_keluar'];
                $totalHarga['keluar'] += ($val['jumlah_keluar'] * $val['harga_keluar']);

                $tmpSaldo['jumlah'] = $tmpSaldo['jumlah'];
                $tmpSaldo['harga'] = $tmpSaldo['harga'];
                $tmpSaldo['sub_total'] = $tmpSaldo['sub_total'];

//                $tmpKeluar['jumlah'] = $tmpKeluar['jumlah'];
//                $tmpKeluar['harga'] = $tmpKeluar['harga'];
//                $tmpKeluar['sub_total'] = $tmpKeluar['sub_total'];

                $tmpKeluar = array('jumlah' => '', 'harga' => '', 'sub_total' => '');
                // stok masuk
                if ($val['jumlah_masuk'] > 0) {
                    $tmpSaldo['jumlah'][$a] = (int) $val['jumlah_masuk'];
                    $tmpSaldo['harga'][$a] = (int) $val['harga_masuk'];
                    $tmpSaldo['sub_total'][$a] = (int) ($val['harga_masuk'] * $val['jumlah_masuk']);

                    $tmp[$a]['jumlah'] = (int) $val['jumlah_masuk'];
                    $tmp[$a]['harga'] = (int) $val['harga_masuk'];

                    //stok keluar
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

                                $sub = $valS['jumlah'] * $valS['harga'];

                                $tmpSaldo['jumlah'][$a] = !empty($valS['jumlah']) ? $valS['jumlah'] : 0;
                                $tmpSaldo['harga'][$a] = !empty($valS['harga']) ? $valS['harga'] : 0;
                                $tmpSaldo['sub_total'][$a] = !empty($sub) ? $sub : 0;

                                if ($tempQty > 0) {
                                    $tmpKeluar['jumlah'][$a] = (int) $tempQty;
                                    $tmpKeluar['harga'][$a] = (int) $val['harga_keluar'];
                                    $tmpKeluar['sub_total'][$a] = (int) ($tmpKeluar['jumlah'][$a] * $tmpKeluar['harga'][$a]);
                                }
                            } else {
                                if ($valS['jumlah'] > 0) {
                                    $tmpKeluar['jumlah'][$a] = (int) $valS['jumlah'];
                                    $tmpKeluar['harga'][$a] = (int) $val['harga_keluar'];
                                    $tmpKeluar['sub_total'][$a] = (int) ($tmpKeluar['jumlah'][$a] * $tmpKeluar['harga'][$a]);
                                }
                                $tempQty -= $valS['jumlah'];
                                unset($tmp[$index]);
                            }
                        } else {
                            $tmpSaldo['jumlah'][$a] = !empty($valS['jumlah']) ? (int) $valS['jumlah'] : 0;
                            $tmpSaldo['harga'][$a] = !empty($valS['harga']) ? (int) $valS['harga'] : 0;
                            $sub = ($tmpSaldo['jumlah'][$a] * $tmpSaldo['harga'][$a]);
                            $tmpSaldo['sub_total'][$a] = !empty($sub) ? (int) $sub : 0;
                        }
                        $totalJml['saldo'] += isset($tmpSaldo['jumlah'][$a]) ? (int) $tmpSaldo['jumlah'][$a] : 0;
                        $totalHarga['saldo'] += isset($tmpSaldo['sub_total'][$a]) ? (int) $tmpSaldo['sub_total'][$a] : 0;
                        $a++;
                        $index++;
                        $totalJml['saldo'] += isset($valS['jumlah']) ? (int) $valS['jumlah'] : 0;
                        $totalHarga['saldo'] += isset($valS['sub_total']) ? (int) $valS['sub_total'] : 0;
                    }
                }

                $totalJml['saldo'] = 0;
                $totalHarga['saldo'] = 0;
                if (!empty($tmpSaldo['jumlah'])) {
                    foreach ($tmpSaldo['jumlah'] as $totSaldo => $o) {
                        $totalJml['saldo'] += $o;
                    }
                }
                if (!empty($tmpSaldo['sub_total'])) {
                    foreach ($tmpSaldo['sub_total'] as $totSaldo => $o) {
                        $totalHarga['saldo'] += $o;
                    }
                }

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
//
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
