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
        $start = date("Y-m-d", strtotime('+1 day', $s));
        $end = date("Y-m-d", strtotime($params['tanggal']['endDate']));
        $criteria = '';
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
        $penjualan = $connection->createCommand("SELECT sum(total-credit) as  penjualan FROM penjualan where (tanggal >= '" . $start . "' and tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        \Yii::error($penjualan);
        $data['penjualan'] = empty($penjualan['penjualan']) ? 0 : $penjualan['penjualan'];

        //pembayaran piutang
        $penjualan = $connection->createCommand("SELECT sum(pinjaman.credit) as pinjaman FROM pinjaman, penjualan where pinjaman.penjualan_id = penjualan.id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['pemb_piutang'] = empty($penjualan['pinjaman']) ? 0 : $penjualan['pinjaman'];

        //diskon
        $penjualan = $connection->createCommand("SELECT sum(penjualan_det.diskon) as diskon FROM penjualan, penjualan_det where penjualan.id=penjualan_det.penjualan_id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['diskon'] = empty($penjualan['diskon']) ? 0 : $penjualan['diskon'];

        //bonus terapis
        $penjualan = $connection->createCommand("SELECT sum(penjualan_det.fee_terapis) as bonus_terapis FROM penjualan, penjualan_det where penjualan_det.pegawai_terapis_id is not NULL and penjualan.id=penjualan_det.penjualan_id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['bonus_terapis'] = empty($penjualan['bonus_terapis']) ? 0 : $penjualan['bonus_terapis'];

        //bonus dokter
        $penjualan = $connection->createCommand("SELECT sum(penjualan_det.fee_dokter) as bonus_dokter FROM penjualan, penjualan_det where penjualan_det.pegawai_dokter_id is not NULL and penjualan.id=penjualan_det.penjualan_id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteria")
                ->queryOne();
        $data['bonus_dokter'] = empty($penjualan['bonus_dokter']) ? 0 : $penjualan['bonus_dokter'];

        $criteria = !empty($params['cabang_id']) ? ' and pembelian.cabang_id = ' . $params['cabang_id'] : '';

        $data['total_nett'] = $data['penjualan'] + $data['pemb_piutang'] - $data['diskon'] - $data['bonus_terapis'] - $data['bonus_dokter'];

        //pembelian
        $pembelian = $connection->createCommand("SELECT sum(total-credit) as  pembelian FROM pembelian where (tanggal >= '" . $start . "' and tanggal <= '" . $end . "') $criteria")
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
                ->orderBy("ks.produk_id, ks.created_at ASC");

        $command = $query->createCommand();
        $kartu = $command->queryAll();
        $pr = 0;

        //mengisi saldo awal produk per kategori
        foreach ($produk as $pro) {
            if ($pr != $pro->id) {
                $indeks = 0;
                $tempSaldo[$pro->id]['jumlah'][0] = 0;
                $tempSaldo[$pro->id]['harga'][0] = 0;
                $tempSaldo[$pro->id]['sub_total'][0] = 0;
            }

            if (!empty($saldoAwal[$pro->id])) {
                foreach ($saldoAwal[$pro->id]['jumlah'] as $key => $sAwal) {
                    if ($sAwal > 0) {
                        $tempSaldo[$pro->id]['jumlah'][$indeks] = $sAwal;
                    }
                }

                foreach ($saldoAwal[$pro->id]['harga'] as $key => $sAwal) {
                    if ($sAwal > 0) {
                        $tempSaldo[$pro->id]['harga'][$indeks] = $sAwal;
                    }
                }

                foreach ($saldoAwal[$pro->id]['sub_total'] as $key => $sAwal) {
                    if ($sAwal['jumlah'] > 0) {
                        $tempSaldo[$pro->id]['sub_total'][$indeks] = $sAwal;
                    }
                }

                $indeks++;
            } else {
                $tempSaldo[$pro->id]['jumlah'][0] = 0;
                $tempSaldo[$pro->id]['harga'][0] = 0;
                $tempSaldo[$pro->id]['sub_total'][0] = 0;
            }

            //detail produk
            $body[$pro->id]['title']['produk'] = $pro->nama;
            $body[$pro->id]['title']['kategori'] = $pro->kategori->nama;
            $body[$pro->id]['title']['satuan'] = $pro->satuan->nama;

            //saldo awal
            $body[$pro->id]['saldo_awal']['jumlah'] = $tempSaldo[$pro->id]['jumlah'];
            $body[$pro->id]['saldo_awal']['harga'] = $tempSaldo[$pro->id]['harga'];
            $body[$pro->id]['saldo_awal']['sub_total'] = $tempSaldo[$pro->id]['sub_total'];
            //total saldo
            $body[$pro->id]['total']['saldo']['jumlah'] = array_sum($tempSaldo[$pro->id]['jumlah']);
            $body[$pro->id]['total']['saldo']['harga'] = array_sum($tempSaldo[$pro->id]['sub_total']);

            $body[$pro->id]['temp'] = $tempSaldo[$pro->id];
            $pr = $pro->id;
            $indeks++;
        }

        $pr = 0;
        $i = 0;
        foreach ($kartu as $val) {

            if ($pr != $val['produk_id']) {
                $indeks = count(isset($body[$val['produk_id']]['saldo_awal']) ? $body[$val['produk_id']]['saldo_awal'] : 0) + 1;
                //setting nilai awal temporari
                unset($tmpSaldo);
                unset($tmp);
                unset($tmpKeluar);
                unset($totalJml);
                unset($totalHarga);

                $tmp[$indeks]['jumlah'] = 0;
                $tmp[$indeks]['harga'] = 0;

                $totalJml = array('saldo' => 0, 'masuk' => 0, 'keluar' => 0);
                $totalHarga = array('saldo' => 0, 'masuk' => 0, 'keluar' => 0);
                if (!empty($tempSaldo[$val['produk_id']])) {
                    for ($idk = 0; $idk < count($tempSaldo[$val['produk_id']]); $idk++) {
                        $sAwal = $tempSaldo[$val['produk_id']];
                        if (isset($sAwal['jumlah'][$idk]) and $sAwal['jumlah'][$idk] > 0) {
                            $tmpSaldo['jumlah'][$indeks] = isset($sAwal['jumlah'][$idk]) ? $sAwal['jumlah'][$idk] : 0;
                            $tmpSaldo['harga'][$indeks] = isset($sAwal['harga'][$idk]) ? $sAwal['harga'][$idk] : 0;
                            $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['jumlah'][$indeks] * $tmpSaldo['harga'][$indeks];

                            $tmp[$indeks]['jumlah'] = $tmpSaldo['jumlah'][$indeks];
                            $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];

                            $indeks++;
                        }
                    }
                } else {
                    $tmpSaldo['jumlah'][0] = 0;
                    $tmpSaldo['harga'][0] = 0;
                    $tmpSaldo['sub_total'][0] = 0;
                }
            } else {
                unset($tmpKeluar);
            }

            if ($val['jumlah_masuk'] > 0) {
                //total masuk
                $totalJml['masuk'] += $val['jumlah_masuk'];
                $totalHarga['masuk'] += ($val['jumlah_masuk'] * $val['harga_masuk']);

                $tempQty = $val['jumlah_masuk'];
                $masuk = $tempQty;
                $first = true;
                $boolStatus = true;

                $jml = array_sum(isset($tmpSaldo['jumlah']) ? $tmpSaldo['jumlah'] : array(0));

                if ($jml >= 0) {
                    $tmp[$indeks]['jumlah'] = $val['jumlah_masuk'];
                    $tmp[$indeks]['harga'] = $val['harga_masuk'];
                }

                foreach ($tmp as $key => $valS) {
                    if ($first) {
                        unset($tmpSaldo);
                        unset($tmp);
                        $first = false;
                    }

                    if ($valS['jumlah'] < 0) {
                        if ($boolStatus) {
                            if ($valS['jumlah'] >= $masuk) {
                                $boolStatus = true;
                            } else {
                                $valS['jumlah'] += $tempQty;
                                $valS['harga'] = $val['harga_masuk'];
                                $tempQty += $valS['jumlah'];
                            }
                        }
                    }

                    $tmpSaldo['jumlah'][$indeks] = $valS['jumlah'];
                    $tmpSaldo['harga'][$indeks] = $valS['harga'];
                    $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['harga'] [$indeks] * $tmpSaldo['jumlah'][$indeks];

                    $tmp[$indeks]['jumlah'] = $tmpSaldo['jumlah'][$indeks];
                    $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];

                    $indeks++;
                }

                $tmpKeluar['jumlah'][$indeks] = 0;
                $tmpKeluar['harga'][$indeks] = 0;
                $tmpKeluar['sub_total'][$indeks] = 0;
            } else {
                $totalJml['keluar'] += $val['jumlah_keluar'];
                $totalHarga['keluar'] += ($val['jumlah_keluar'] * $val['harga_keluar']);

                $tempQty = $val['jumlah_keluar'];
                $first = true;
                $boolStatus = true;
                $saldo = 0;
                foreach ($tmp as $valS) {
                    if ($first) {
                        unset($tmpSaldo);
                        unset($tmp);
                        unset($tmpKeluar);
                        $first = false;
                    }
                    if ($boolStatus) {
                        if ($valS['jumlah'] > $tempQty) {
                            $tmpKeluar['jumlah'][$indeks] = $tempQty;
                            $valS['jumlah'] -= $tempQty;
                            $boolStatus = false;
                        } else {
                            $tmpKeluar['jumlah'][$indeks] = $valS['jumlah'];
                            if ($valS['jumlah'] <= 0) {
                                $valS['jumlah'] -= $tempQty;
                                $tmpKeluar['jumlah'][$indeks] = $tempQty;
                                $valS['harga'] = $val['harga_keluar'];
                            }
                            $tempQty -= $valS['jumlah'];
                        }
                        //simpan stok keluar
                        $tmpKeluar['harga'][$indeks] = $val['harga_keluar'];
                        $tmpKeluar['sub_total'][$indeks] = $tmpKeluar['jumlah'][$indeks] * $tmpKeluar['harga'][$indeks];
                    }
                    //simpan stok saldo
                    $tmpSaldo['jumlah'][$indeks] = (isset($tmpKeluar['jumlah'][$indeks]) and $tmpKeluar['jumlah'][$indeks] == $valS['jumlah']) ? 0 : $valS['jumlah'];
                    $tmpSaldo['harga'][$indeks] = $valS['harga'];
                    $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['harga'][$indeks] * $tmpSaldo['jumlah'][$indeks];

                    $tmp[$indeks]['jumlah'] = $valS['jumlah'];
                    $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];

                    $indeks++;
                }
            }

            $totalJml['saldo'] += ($totalJml['masuk'] - $val['jumlah_keluar']);
            $totalHarga['saldo'] = 0;
            foreach ($tmpSaldo['sub_total'] as $key => $vKeluar) {
                $totalHarga['saldo'] += $vKeluar;
            }

            $body[$val['produk_id']]['body'][$i]['tanggal'] = date("Y-m-d", strtotime($val['created_at']));
            $body[$val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $body[$val['produk_id']]['body'][$i]['keterangan'] = $val['keterangan'];
            $body[$val['produk_id']]['body'][$i]['masuk']['jumlah'] = $val['jumlah_masuk'];
            $body[$val['produk_id']]['body'][$i]['masuk']['harga'] = $val['harga_masuk'];
            $body[$val['produk_id']]['body'][$i]['masuk']['sub_total'] = $val['jumlah_masuk'] * $val['harga_masuk'];
            $body[$val['produk_id']]['body'][$i]['keluar']['jumlah'] = $tmpKeluar['jumlah'];
            $body[$val['produk_id']]['body'][$i]['keluar']['harga'] = $tmpKeluar['harga'];
            $body[$val['produk_id']]['body'][$i]['keluar']['sub_total'] = $tmpKeluar['sub_total'];
            $body[$val['produk_id']]['body'][$i]['saldo']['jumlah'] = $tmpSaldo['jumlah'];
            $body[$val['produk_id']]['body'][$i]['saldo']['harga'] = $tmpSaldo['harga'];
            $body[$val['produk_id']]['body'][$i]['saldo']['sub_total'] = $tmpSaldo['sub_total'];
            $body[$val['produk_id']]['total']['masuk']['jumlah'] = $totalJml['masuk'];
            $body[$val['produk_id']]['total']['masuk']['harga'] = $totalHarga['masuk'];
            $body[$val['produk_id']]['total']['keluar']['jumlah'] = $totalJml['keluar'];
            $body[$val['produk_id']]['total']['keluar']['harga'] = $totalHarga['keluar'];
            $body[$val['produk_id']]['total']['saldo']['jumlah'] = $body[$val['produk_id']]['total']['masuk']['jumlah'] - $body[$val['produk_id']]['total']['keluar']['jumlah'];
            $body[$val['produk_id']]['total']['saldo']['harga'] = $totalHarga['saldo'];

            $indeks++;
            $pr = $val['produk_id'];
            $i++;
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
