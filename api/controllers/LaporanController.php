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
        $params = json_decode(file_get_contents("php://input"), true);
        $data = array();
        $kartu = array();
        $s = strtotime(date("Y-m-d", strtotime($params['tanggal']['startDate'])));
        $start = date("Y-m-d", strtotime('+1 day', $s));
        $end = date("Y-m-d", strtotime($params['tanggal']['endDate']));
        $data['start'] = $start;
        $data['end'] = $end;
        $tglSaldo = date("Y-m-d", strtotime('-1 day', $s));
        $criteriaSM = '';
        $criteriaSk = '';
        $criteriaPm = '';
        $criteriaPn = '';
        $criteriaRpm = '';
        $criteriaRpn = '';
        if (!empty($params['cabang_id'])) {
            $cbg = \app\models\Cabang::findOne(['id' => $params['cabang_id']]);
            $data['cabang'] = strtoupper($cbg->nama);

            //add criteria
            $criteriaSM .= ' and stok_masuk.cabang_id=' . $params['cabang_id'];
            $criteriaSk .= ' and stok_keluar.cabang_id=' . $params['cabang_id'];
            $criteriaPm .= ' and pembelian.cabang_id=' . $params['cabang_id'];
            $criteriaPn .= ' and penjualan.cabang_id=' . $params['cabang_id'];
            $criteriaRpm .= ' and pembelian.cabang_id=' . $params['cabang_id'];
            $criteriaRpn .= ' and penjualan.cabang_id=' . $params['cabang_id'];
        } else {
            $data['cabang'] = 'SEMUA CABANG';
        }

        if (!empty($params['kategori_id'])) {
            $cbg = \app\models\Kategori::findOne(['id' => $params['kategori_id']]);
            $data['kategori'] = strtoupper($cbg->nama);

            //add criteria
            $criteriaSM .= ' and m_produk.kategori_id=' . $params['kategori_id'];
            $criteriaSk .= ' and m_produk.kategori_id=' . $params['kategori_id'];
            $criteriaPm .= ' and m_produk.kategori_id=' . $params['kategori_id'];
            $criteriaPn .= ' and m_produk.kategori_id=' . $params['kategori_id'];
            $criteriaRpm .= ' and m_produk.kategori_id=' . $params['kategori_id'];
            $criteriaRpn .= ' and m_produk.kategori_id=' . $params['kategori_id'];
        } else {
            $data['kategori'] = 'SEMUA KATEGORI';
        }

        $connection = \Yii::$app->db;

        $saldo = array();

        //cari saldo awal
        $saldoSm = $connection->createCommand("select m_satuan.nama as satuan, stok_masuk_det.harga as harga, stok_masuk_det.jumlah as jumlah, m_kategori.id as kategori_id, m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, stok_masuk, stok_masuk_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and stok_masuk.id = stok_masuk_det.stok_masuk_id and stok_masuk_det.produk_id = m_produk.id and stok_masuk.tanggal < '" . $start . "' $criteriaSM"
                        . " order by stok_masuk.tanggal ASC")
                ->queryAll();

        if (!empty($saldoSm)) {
            foreach ($saldoSm as $val) {
                $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] = (isset($saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah']) ? $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] : 0 ) + $val['jumlah'];
                $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] = isset($saldoMasuk[$val['produk_id'] . $val['harga']]['harga']) ? $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] : 0;
                $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] = 0;
                $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] = 0;
            }
        }

        $saldoSk = $connection->createCommand("select m_satuan.nama as satuan, sum(stok_keluar_det.harga) as harga, sum(stok_keluar_det.jumlah) as jumlah, m_kategori.id as kategori_id, m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, stok_keluar, stok_keluar_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and stok_keluar.id = stok_keluar_det.stok_keluar_id and stok_keluar_det.produk_id = m_produk.id and stok_keluar.tanggal < '" . $start . "' $criteriaSk"
                        . " order by stok_keluar.tanggal ASC")
                ->queryAll();

        if (!empty($saldoSk)) {
            foreach ($saldoSk as $val) {
                $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] = (isset($saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah']) ? $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] : 0 ) + $val['jumlah'];
                $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] = isset($saldoKeluar[$val['produk_id'] . $val['harga']]['harga']) ? $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] : 0;
                $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] = 0;
                $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] = 0;
            }
        }

        $saldoPn = $connection->createCommand("select m_satuan.nama as satuan, sum(penjualan_det.harga) as harga, sum(penjualan_det.jumlah) as jumlah, m_kategori.id as kategori_id, m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, penjualan, penjualan_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and penjualan.id = penjualan_det.penjualan_id and penjualan_det.produk_id = m_produk.id and penjualan.tanggal < '" . $start . "' $criteriaPn"
                        . " order by penjualan.tanggal ASC")
                ->queryAll();

        if (!empty($saldoPn)) {
            foreach ($saldoPn as $val) {
                $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] = (isset($saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah']) ? $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] : 0 ) + $val['jumlah'];
                $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] = isset($saldoKeluar[$val['produk_id'] . $val['harga']]['harga']) ? $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] : 0;
                $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] = 0;
                $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] = 0;
            }
        }

        $saldoPm = $connection->createCommand("select m_satuan.nama as satuan, sum(pembelian_det.harga) as harga, sum(pembelian_det.jumlah) as jumlah, m_produk.kategori_id as kategori_id, m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, pembelian, pembelian_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and pembelian.id = pembelian_det.pembelian_id and pembelian_det.produk_id = m_produk.id and pembelian.tanggal < '" . $start . "' $criteriaPm"
                        . " order by pembelian.tanggal ASC")
                ->queryAll();

        if (!empty($saldoPm)) {
            foreach ($saldoPm as $val) {
                $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] = (isset($saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah']) ? $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] : 0 ) + $val['jumlah'];
                $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] = isset($saldoMasuk[$val['produk_id'] . $val['harga']]['harga']) ? $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] : 0;
                $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] = 0;
                $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] = 0;
            }
        }

        $saldoRpn = $connection->createCommand("select m_satuan.nama as satuan, sum(r_penjualan_det.harga) as harga, sum(r_penjualan_det.jumlah) as jumlah, m_produk.kategori_id as kategori_id, m_produk.id as produk_id"
                        . " from penjualan, m_satuan, m_kategori, r_penjualan, r_penjualan_det, m_produk "
                        . " where r_penjualan.penjualan_id = penjualan.id and m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and r_penjualan.id = r_penjualan_det.r_penjualan_id and r_penjualan_det.produk_id = m_produk.id and r_penjualan.tanggal < '" . $start . "' $criteriaRpn"
                        . " order by r_penjualan.tanggal ASC")
                ->queryAll();

        if (!empty($saldoRpn)) {
            foreach ($saldoRpn as $val) {
                $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] = (isset($saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah']) ? $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] : 0 ) + $val['jumlah'];
                $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] = isset($saldoMasuk[$val['produk_id'] . $val['harga']]['harga']) ? $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] : 0;
                $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] = 0;
                $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] = 0;
            }
        }

        $saldoRpm = $connection->createCommand("select m_satuan.nama as satuan, sum(r_pembelian_det.harga) as harga, sum(r_pembelian_det.jumlah) as jumlah, m_produk.kategori_id as kategori_id , m_produk.id as produk_id"
                        . " from pembelian, m_satuan, m_kategori, r_pembelian, r_pembelian_det, m_produk "
                        . " where r_pembelian.pembelian_id = pembelian.id and m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and r_pembelian.id = r_pembelian_det.r_pembelian_id and r_pembelian_det.produk_id = m_produk.id and r_pembelian.tanggal < '" . $start . "' $criteriaRpm"
                        . " order by r_pembelian.tanggal ASC")
                ->queryAll();

        if (!empty($saldoRpm)) {
            foreach ($saldoRpm as $val) {
                $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] = (isset($saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah']) ? $saldoKeluar[$val['produk_id'] . $val['harga']]['jumlah'] : 0 ) + $val['jumlah'];
                $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] = isset($saldoKeluar[$val['produk_id'] . $val['harga']]['harga']) ? $saldoKeluar[$val['produk_id'] . $val['harga']]['harga'] : 0;
                $saldoMasuk[$val['produk_id'] . $val['harga']]['jumlah'] = 0;
                $saldoMasuk[$val['produk_id'] . $val['harga']]['harga'] = 0;
            }
        }
        
        //Kartu Stok
        $stokMasuk = $connection->createCommand("select m_satuan.nama as satuan, stok_masuk_det.harga as harga, stok_masuk_det.jumlah as jumlah, stok_masuk.kode as kode, m_produk.kategori_id as kategori_id, m_kategori.nama as kategori, stok_masuk.tanggal as tanggal, m_produk.nama as produk, m_produk.id as produk_id "
                        . " from m_satuan, m_kategori, stok_masuk, stok_masuk_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and stok_masuk.id = stok_masuk_det.stok_masuk_id and stok_masuk_det.produk_id = m_produk.id and (stok_masuk.tanggal >= '" . $start . "' and stok_masuk.tanggal <= '" . $end . "') $criteriaSM"
                        . " order by stok_masuk.tanggal ASC")
                ->queryAll();
        $i = 0;
        
        foreach ($stokMasuk as $val) {
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['produk'] = $val['produk'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['kategori'] = $val['kategori'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['saldo']['masuk'] = $saldoMasuk;
            $kartu[$val['kategori_id'] . $val['produk_id']]['saldo']['keluar'] = $saldoKeluar;
//            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['jumlah'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0) - (isset($saldoKeluar[$val['produk_id']]['jumlah']) ? $saldoKeluar[$val['produk_id']]['jumlah'] : 0);
//            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
//            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['harga'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) && $saldoMasuk[$val['produk_id']]['jumlah'] > 0) ? ((isset($saldoMasuk[$val['produk_id']]['harga']) ? $saldoMasuk[$val['produk_id']]['harga'] : 0) / (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0)) : 0;
//            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['tanggal'] = $val['tanggal'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['masuk'] = $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['masuk'] = $val['harga'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['masuk'] = $val['harga'] * $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['keluar'] = '0';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['keluar'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['keluar'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['satuan'] = $val['satuan'];

            $i++;
        }

        $stokKeluar = $connection->createCommand("select m_satuan.nama as satuan, stok_keluar_det.harga as harga, stok_keluar_det.jumlah as jumlah, stok_keluar.kode as kode, m_produk.kategori_id as kategori_id, m_kategori.nama as kategori, stok_keluar.tanggal as tanggal, m_produk.nama as produk , m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, stok_keluar, stok_keluar_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and stok_keluar.id = stok_keluar_det.stok_keluar_id and stok_keluar_det.produk_id = m_produk.id and (stok_keluar.tanggal >= '" . $start . "' and stok_keluar.tanggal <= '" . $end . "') $criteriaSk"
                        . " order by stok_keluar.tanggal ASC")
                ->queryAll();
        foreach ($stokKeluar as $val) {
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['produk'] = $val['produk'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['kategori'] = $val['kategori'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['jumlah'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0) - (isset($saldoKeluar[$val['produk_id']]['jumlah']) ? $saldoKeluar[$val['produk_id']]['jumlah'] : 0);
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['harga'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) && $saldoMasuk[$val['produk_id']]['jumlah'] > 0) ? ((isset($saldoMasuk[$val['produk_id']]['harga']) ? $saldoMasuk[$val['produk_id']]['harga'] : 0) / (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0)) : 0;
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['tanggal'] = $val['tanggal'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['keluar'] = $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['keluar'] = $val['harga'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['keluar'] = $val['harga'] * $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['masuk'] = '0';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['masuk'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['masuk'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['satuan'] = $val['satuan'];

            $i++;
        }

        $pembelian = $connection->createCommand("select m_satuan.nama as satuan, pembelian_det.harga as harga, pembelian_det.jumlah as jumlah, pembelian.kode as kode, m_produk.kategori_id as kategori_id, m_kategori.nama as kategori, pembelian.tanggal as tanggal, m_produk.nama as produk , m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, pembelian, pembelian_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and pembelian.id = pembelian_det.pembelian_id and pembelian_det.produk_id = m_produk.id and (pembelian.tanggal >= '" . $start . "' and pembelian.tanggal <= '" . $end . "') $criteriaPm"
                        . " order by pembelian.tanggal ASC")
                ->queryAll();
        foreach ($pembelian as $val) {
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['produk'] = $val['produk'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['kategori'] = $val['kategori'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['jumlah'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0) - (isset($saldoKeluar[$val['produk_id']]['jumlah']) ? $saldoKeluar[$val['produk_id']]['jumlah'] : 0);
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['harga'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) && $saldoMasuk[$val['produk_id']]['jumlah'] > 0) ? ((isset($saldoMasuk[$val['produk_id']]['harga']) ? $saldoMasuk[$val['produk_id']]['harga'] : 0) / (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0)) : 0;
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['tanggal'] = $val['tanggal'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['masuk'] = $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['masuk'] = $val['harga'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['masuk'] = $val['harga'] * $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['keluar'] = '0';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['keluar'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['keluar'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['satuan'] = $val['satuan'];

            $i++;
        }

        $penjualan = $connection->createCommand("select m_satuan.nama as satuan, penjualan_det.harga as harga, penjualan_det.jumlah as jumlah, penjualan.kode as kode, m_produk.kategori_id as kategori_id, m_kategori.nama as kategori, penjualan.tanggal as tanggal, m_produk.nama as produk , m_produk.id as produk_id"
                        . " from m_satuan, m_kategori, penjualan, penjualan_det, m_produk "
                        . " where m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and penjualan.id = penjualan_det.penjualan_id and penjualan_det.produk_id = m_produk.id and (penjualan.tanggal >= '" . $start . "' and penjualan.tanggal <= '" . $end . "') $criteriaPn"
                        . " order by penjualan.tanggal ASC")
                ->queryAll();
        foreach ($penjualan as $val) {
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['produk'] = $val['produk'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['kategori'] = $val['kategori'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['jumlah'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0) - (isset($saldoKeluar[$val['produk_id']]['jumlah']) ? $saldoKeluar[$val['produk_id']]['jumlah'] : 0);
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['harga'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) && $saldoMasuk[$val['produk_id']]['jumlah'] > 0) ? ((isset($saldoMasuk[$val['produk_id']]['harga']) ? $saldoMasuk[$val['produk_id']]['harga'] : 0) / (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0)) : 0;
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['tanggal'] = $val['tanggal'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['keluar'] = $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id'] . $val['produk_id']]['body'][$i]['harga']['keluar'] = $val['harga'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['keluar'] = $val['harga'] * $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['masuk'] = '0';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['masuk'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['masuk'] = '';
            $kartu[$val['kategori_id']]['body'][$i]['satuan'] = $val['satuan'];

            $i++;
        }

        $r_pembelian = $connection->createCommand("select m_satuan.nama as satuan, r_pembelian_det.harga as harga, r_pembelian_det.jumlah as jumlah, r_pembelian.kode as kode, m_produk.kategori_id as kategori_id, m_kategori.nama as kategori, r_pembelian.tanggal as tanggal, m_produk.nama as produk , m_produk.id as produk_id"
                        . " from pembelian, m_satuan, m_kategori, r_pembelian, r_pembelian_det, m_produk "
                        . " where r_pembelian.pembelian_id = pembelian.id and m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and r_pembelian.id = r_pembelian_det.r_pembelian_id and r_pembelian_det.produk_id = m_produk.id and (r_pembelian.tanggal >= '" . $start . "' and r_pembelian.tanggal <= '" . $end . "') $criteriaRpm"
                        . " order by r_pembelian.tanggal ASC")
                ->queryAll();
        foreach ($r_pembelian as $val) {
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['produk'] = $val['produk'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['kategori'] = $val['kategori'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['jumlah'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0) - (isset($saldoKeluar[$val['produk_id']]['jumlah']) ? $saldoKeluar[$val['produk_id']]['jumlah'] : 0);
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['harga'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) && $saldoMasuk[$val['produk_id']]['jumlah'] > 0) ? ((isset($saldoMasuk[$val['produk_id']]['harga']) ? $saldoMasuk[$val['produk_id']]['harga'] : 0) / (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0)) : 0;
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['tanggal'] = $val['tanggal'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['keluar'] = $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['keluar'] = $val['harga'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['keluar'] = $val['harga'] * $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['masuk'] = '0';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['masuk'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['masuk'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['satuan'] = $val['satuan'];

            $i++;
        }

        $criteria = '';
        $criteria .= (!empty($params['cabang_id'])) ? : '';
        $criteria .= (!empty($params['kategori_id'])) ? ' and m_produk.kategori_id=' . $params['kategori_id'] : '';

        $r_penjualan = $connection->createCommand("select m_satuan.nama as satuan, r_penjualan_det.harga as harga, r_penjualan_det.jumlah as jumlah, r_penjualan.kode as kode, m_produk.kategori_id as kategori_id, m_kategori.nama as kategori, r_penjualan.tanggal as tanggal, m_produk.nama as produk , m_produk.id as produk_id"
                        . " from penjualan, m_satuan, m_kategori, r_penjualan, r_penjualan_det, m_produk "
                        . " where r_penjualan.penjualan_id = penjualan.id and m_satuan.id = m_produk.satuan_id and m_produk.kategori_id = m_kategori.id and r_penjualan.id = r_penjualan_det.r_penjualan_id and r_penjualan_det.produk_id = m_produk.id and (r_penjualan.tanggal >= '" . $start . "' and r_penjualan.tanggal <= '" . $end . "') $criteriaRpn"
                        . " order by r_penjualan.tanggal ASC")
                ->queryAll();
        foreach ($r_penjualan as $val) {
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['produk'] = $val['produk'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['kategori'] = $val['kategori'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['jumlah'] = (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0) - (isset($saldoKeluar[$val['produk_id']]['jumlah']) ? $saldoKeluar[$val['produk_id']]['jumlah'] : 0);
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['satuan'] = $val['satuan'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['harga'] = (isset($saldoMasuk[$val['produk_id']]['harga']) ? $saldoMasuk[$val['produk_id']]['harga'] : 0) / (isset($saldoMasuk[$val['produk_id']]['jumlah']) ? $saldoMasuk[$val['produk_id']]['jumlah'] : 0);
            $kartu[$val['kategori_id'] . $val['produk_id']]['title']['saldo']['tanggal'] = $tglSaldo;
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['tanggal'] = $val['tanggal'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['kode'] = $val['kode'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['masuk'] = $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['masuk'] = $val['harga'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['masuk'] = $val['harga'] * $val['jumlah'];
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['jumlah']['keluar'] = '0';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['harga']['keluar'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['sub_total']['keluar'] = '';
            $kartu[$val['kategori_id'] . $val['produk_id']]['body'][$i]['satuan'] = $val['satuan'];

            $i++;
        }

        echo json_encode(array('status' => 1, 'data' => $data, 'detail' => $kartu), JSON_PRETTY_PRINT);
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
