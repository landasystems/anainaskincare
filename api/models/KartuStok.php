<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "kartu_stok".
 *
 * @property integer $id
 * @property integer $produk_id
 * @property integer $cabang_id
 * @property string $keterangan
 * @property integer $jumlah_masuk
 * @property integer $harga_masuk
 * @property integer $jumlah_keluar
 * @property integer $harga_keluar
 * @property integer $jumlah_saldo
 * @property integer $harga_saldo
 * @property string $created_at
 * @property integer $created_by
 */
class KartuStok extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'kartu_stok';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['produk_id', 'cabang_id', 'jumlah_masuk', 'harga_masuk', 'jumlah_keluar', 'harga_keluar', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['keterangan'], 'string', 'max' => 255]
        ];
    }

    public static function process($type, $date, $kode, $product_id, $qty, $cabang_id, $price = 0, $keterangan, $trans_id) {
        $masuk = array();
        $keluar = array();
        $masuk['jumlah'] = 0;
        $masuk['harga'] = 0;
        $keluar['jumlah'] = 0;
        $keluar['harga'] = 0;

        if ($type == 'in') {
            $boolStatus = true;
            $masuk['jumlah'] = $qty;
            $masuk['harga'] = $price;
        } else if ($type == 'out') {
            $keluar['jumlah'] = $qty;
            $keluar['harga'] = $price;
        }
        $kartu = new KartuStok();
        $update = $kartu->isiKartu($keterangan, $trans_id, $date, $kode, $product_id, $masuk, $keluar, $cabang_id);
    }

    public function hapusKartu($keterangan, $trans_id) {
        $criteria = '';

        if ($keterangan == "stok masuk") {
            $criteria .= ' and stok_masuk_id = ' . $trans_id;
        } elseif ($keterangan == "stok keluar") {
            $criteria .= ' and stok_keluar_id = ' . $trans_id;
        } elseif ($keterangan == "penjualan") {
            $criteria .= ' and penjualan_id = ' . $trans_id;
        } elseif ($keterangan == "pembelian") {
            $criteria .= ' and pembelian_id = ' . $trans_id;
        } elseif ($keterangan == "r_penjualan") {
            $criteria .= ' and r_penjualan_id = ' . $trans_id;
        } elseif ($keterangan == "r_pembelian") {
            $criteria .= ' and r_pembelian_id = ' . $trans_id;
        } elseif ($keterangan == "transfer") {
            $criteria .= ' and transfer_id = ' . $trans_id;
        }

        KartuStok::deleteAll("keterangan = '" . $keterangan . "' $criteria");
    }

    public function isiKartu($keterangan, $trans_id, $date, $kode, $produk_id, $masuk, $keluar, $cabang_id) {
        $sv = new KartuStok();

        if ($keterangan == "stok masuk") {
            $sv->stok_masuk_id = $trans_id;
        } elseif ($keterangan == "stok keluar") {
            $sv->stok_keluar_id = $trans_id;
        } elseif ($keterangan == "penjualan") {
            $sv->penjualan_id = $trans_id;
        } elseif ($keterangan == "pembelian") {
            $sv->pembelian_id = $trans_id;
        } elseif ($keterangan == "r_penjualan") {
            $sv->r_penjualan_id = $trans_id;
        } elseif ($keterangan == "r_pembelian") {
            $sv->r_pembelian_id = $trans_id;
        } elseif ($keterangan == "transfer") {
            $sv->transfer_id = $trans_id;
        }

        $sv->created_at = $date . " " . date("H:i:s");
        $sv->kode = $kode;
        $sv->produk_id = $produk_id;
        $sv->cabang_id = $cabang_id;
        $sv->keterangan = $keterangan;
        $sv->jumlah_masuk = $masuk['jumlah'];
        $sv->harga_masuk = $masuk['harga'];
        $sv->jumlah_keluar = $keluar['jumlah'];
        $sv->harga_keluar = $keluar['harga'];
        $sv->save();
    }

    public function saldo($type, $date, $param = array()) {
        $criteria = '';
        $body = array();

        //========== SETTING PARAMETER ==========//
        if (isset($param['cabang']) and ! empty($param['cabang'])) {
            $criteria .= ' and kartu_stok.cabang_id = ' . $param['cabang'];
        }

        if (isset($param['kategori_id']) and ! empty($param['kategori_id'])) {
            $criteria .= ' and m_produk.kategori_id = ' . $param['kategori_id'];
        }

        if (isset($param['produk_id']) and ! empty($param['produk_id'])) {
            $criteria .= ' and m_produk.id = ' . $param['produk_id'];
        }

        if ($type == 'balance') {
            $criteria .= " and date(kartu_stok.created_at) < '" . $date . "'";
        } else if ($type == 'today') {
            $criteria .= " and date(kartu_stok.created_at) <= '" . date("Y-m-d") . "'";
        }
        //=============== END ===========//
        //============== AMBIL KARTU STOK =============//
        $query = new Query;
        $query->from(['m_produk', 'kartu_stok'])
                ->select("kartu_stok.*")
                ->where("m_produk.is_deleted = 0 and m_produk.type = 'Barang' and m_produk.id = kartu_stok.produk_id $criteria")
                ->orderBy("kartu_stok.produk_id, kartu_stok.created_at ASC, kartu_stok.id ASC");
        $command = $query->createCommand();
        $kartu = $command->queryAll();
        //============= END =============//

        $tmpSaldo = array();
        foreach ($kartu as $key => $vKartu) {

            //========= KONDISI STOK MASUK =======//
            if ($vKartu['jumlah_masuk'] > 0) {
                //========= AMBIL ARRAY SALDO SEBELUMNYA JIKA ADA ======//
                if (isset($tmpSaldo[$vKartu['produk_id']]['harga'])) {
                    //=== MENCARI HARGA YANG SAMA PADA SALDO ===//
                    $key = array_search($vKartu['harga_masuk'], $tmpSaldo[$vKartu['produk_id']]['harga']);
                    if ($key != false && isset($tmpSaldo[$vKartu['produk_id']]['jumlah'][$key])) {
                        //=== JIKA ADA HARGA SAMA STOK DITAMBAHKAN KE SALDO YANG SUDAH ADA====//
                        $tmpSaldo[$vKartu['produk_id']]['jumlah'][$key] += $vKartu['jumlah_masuk'];
                        $tmpSaldo[$vKartu['produk_id']]['harga'][$key] = (double) $vKartu['harga_masuk'];
                        $tmpSaldo[$vKartu['produk_id']]['sub_total'][$key] = (double) ($vKartu['harga_masuk'] * $tmpSaldo[$vKartu['produk_id']]['jumlah'][$key]);
                    } else {
                        //=== JIKA TIDAK ADA HARGA YANG SAMA SALDO DITAMBAHKAN ===//
                        $tmpSaldo[$vKartu['produk_id']]['jumlah'][] = (double) $vKartu['jumlah_masuk'];
                        $tmpSaldo[$vKartu['produk_id']]['harga'][] = (double) $vKartu['harga_masuk'];
                        $tmpSaldo[$vKartu['produk_id']]['harga'][] = (double) ($vKartu['harga_masuk'] * $vKartu['jumlah_masuk']);
                    }
                } else {
                    //=== JIKA TIDAK ADA SALDO SEBELUMNYA BUAT SALDO BARU ===//
                    $tmpSaldo[$vKartu['produk_id']]['jumlah'][] = (double) $vKartu['jumlah_masuk'];
                    $tmpSaldo[$vKartu['produk_id']]['harga'][] = (double) $vKartu['harga_masuk'];
                    $tmpSaldo[$vKartu['produk_id']]['harga'][] = (double) ($vKartu['harga_masuk'] * $vKartu['jumlah_masuk']);
                }
            } else {
                if (isset($tmpSaldo[$vKartu['produk_id']])) {
                    $tempQ = $vKartu['jumlah_keluar'];
                    $bool = true;
                    foreach ($tmpSaldo[$vKartu['produk_id']]['jumlah'] as $key2 => $vSaldo) {
                        $sSaldo = $vSaldo;
                        if ($bool == true) {
                            $sSaldo = 0;
                            if ($vSaldo > $tempQ) {
                                $sSaldo = $vSaldo - $tempQ;
                                $vSaldo -= $tempQ;
                                $bool = false;
                            } else {
                                $sSaldo = $vSaldo - $tempQ;
                                $tempQ -= $vSaldo;
                            }
                        }

                        //=== JIKA SISA SALDO KURANG DARI 0 DAN MASIH ADA SALDO SELANJUTNYA MAKA SISA SALDO DI HAPUS ===//
                        if ($sSaldo <= 0 and isset($tmpSaldo[$vKartu['produk_id']]['jumlah'][$key2 + 1])) {
                            unset($tmpSaldo[$vKartu['produk_id']]['jumlah'][$key2]);
                            unset($tmpSaldo[$vKartu['produk_id']]['harga'][$key2]);
                            unset($tmpSaldo[$vKartu['produk_id']]['sub_total'][$key2]);
                        } else {
                            $tmpSaldo[$vKartu['produk_id']]['jumlah'][$key2] = (double) $sSaldo;
                            $tmpSaldo[$vKartu['produk_id']]['sub_total'][$key2] = $tmpSaldo[$vKartu['produk_id']]['harga'][$key2] * $sSaldo;
                        }
                    }
                } else {
                    $tmpSaldo[$vKartu['produk_id']]['jumlah'][] = 0 - $vKartu['jumlah_keluar'];
                    $tmpSaldo[$vKartu['produk_id']]['harga'][] = 0;
                    $tmpSaldo[$vKartu['produk_id']]['sub_total'][] = 0;
                }
            }
            //========= END ========//

            $body[$vKartu['produk_id']]['jumlah'] = isset($tmpSaldo[$vKartu['produk_id']]['jumlah']) ? $tmpSaldo[$vKartu['produk_id']]['jumlah'] : array();
            $body[$vKartu['produk_id']]['harga'] = isset($tmpSaldo[$vKartu['produk_id']]['harga']) ? $tmpSaldo[$vKartu['produk_id']]['harga'] : array();
            $body[$vKartu['produk_id']]['harga'] = isset($tmpSaldo[$vKartu['produk_id']]['sub_total']) ? $tmpSaldo[$vKartu['produk_id']]['sub_total'] : array();
        }

        return $body;
    }

//    public function saldo($type, $cabang, $kategori, $date, $id_produk = '') {
//        $criteria = '';
//        $body = array();
//
//        if (!empty($cabang))
//            $criteria .= ' and kartu_stok.cabang_id = ' . $cabang;
//
//        if (empty($id_produk)) {
//            if (!empty($kategori))
//                $criteria .= ' and m_produk.kategori_id = ' . $kategori;
//        }else {
//            if (!empty($id_produk))
//                $criteria .= ' and m_produk.id = ' . $id_produk;
//        }
//
//        if ($type == 'balance') {
//            $criteria .= " and date(kartu_stok.created_at) < '" . $date . "'";
//        } else if ($type == 'today') {
//            $criteria .= " and date(kartu_stok.created_at) <= '" . date("Y-m-d") . "'";
//        }
//
//        $query = new Query;
//        $query->from(['m_produk', 'kartu_stok'])
//                ->select("kartu_stok.*")
//                ->where("m_produk.is_deleted = 0 and m_produk.type = 'Barang' and m_produk.id = kartu_stok.produk_id $criteria")
//                ->orderBy("kartu_stok.produk_id, kartu_stok.created_at ASC, kartu_stok.id ASC");
//        $command = $query->createCommand();
//        $kartu = $command->queryAll();
//        $pr = 0;
//        $i = 0;
//        foreach ($kartu as $val) {
//
//            if ($pr != $val['produk_id']) {
//                $indeks = 1;
//                //setting nilai awal temporari
//                unset($tmpSaldo);
//                unset($tmp);
//                unset($tmpKeluar);
//                unset($totalJml);
//                unset($totalHarga);
//
//                $tmpSaldo['jumlah'][$indeks] = 0;
//                $tmpSaldo['harga'][$indeks] = 0;
//                $tmpSaldo['sub_total'][$indeks] = 0;
//
//                $tmp[$indeks]['jumlah'] = 0;
//                $tmp[$indeks]['harga'] = 0;
//            } else {
//                unset($tmpKeluar);
//            }
//
//            if ($val['jumlah_masuk'] > 0) {
//                $tempQty = $val['jumlah_masuk'];
//                $masuk = $tempQty;
//                $first = true;
//                $boolStatus = true;
//
//                $jml = array_sum(isset($tmpSaldo['jumlah']) ? $tmpSaldo['jumlah'] : array(0));
//
//                if ($jml >= 0) {
//                    $tmp[$indeks]['jumlah'] = $val['jumlah_masuk'];
//                    $tmp[$indeks]['harga'] = $val['harga_masuk'];
//                }
//
//                foreach ($tmp as $key => $valS) {
////                    if ($valS['jumlah'] > 0) {
//                    if ($first) {
//                        unset($tmpSaldo);
//                        unset($tmp);
//                        $first = false;
//                    }
//
//                    if ($valS['jumlah'] < 0) {
//                        if ($boolStatus) {
//                            if ($valS['jumlah'] >= $masuk) {
//                                $boolStatus = true;
//                            } else {
//                                $valS['jumlah'] += $tempQty;
//                                $valS['harga'] = $val['harga_masuk'];
//                                $tempQty += $valS['jumlah'];
//                            }
//                        }
//                    }
//
//                    if ($valS['jumlah'] != 0) {
////                        $tmpSaldo['jumlah'][$indeks] = $valS['jumlah'];
//                        $tmpSaldo['jumlah'][$indeks] = ((isset($tmpSaldo['jumlah'][$indeks]) ? $tmpSaldo['jumlah'][$indeks] : 0)) + $valS['jumlah'];
//                        $tmpSaldo['harga'][$indeks] = $valS['harga'];
//                        $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['harga'][$indeks] * $tmpSaldo['jumlah'][$indeks];
//
//                        $tmp[$indeks]['jumlah'] = $tmpSaldo['jumlah'][$indeks];
//                        $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];
//                    }
//
////                    $indeks++;
////                    }
//                }
//
//                $tmpKeluar['jumlah'][$indeks] = 0;
//                $tmpKeluar['harga'][$indeks] = 0;
//                $tmpKeluar['sub_total'][$indeks] = 0;
//            } else {
//                $tempQty = $val['jumlah_keluar'];
//                $first = true;
//                $boolStatus = true;
////                $saldo = 0;
//                $jml = array_sum(isset($tmpSaldo['jumlah']) ? $tmpSaldo['jumlah'] : array(0));
//                if ($jml >= 0) {
//                    $tmp[$indeks]['jumlah'] = $val['jumlah_masuk'];
//                    $tmp[$indeks]['harga'] = $val['harga_masuk'];
//                }
//                foreach ($tmp as $valS) {
////                    if ($valS['jumlah']) {
//                    if ($first) {
//                        unset($tmpSaldo);
//                        unset($tmp);
//                        unset($tmpKeluar);
//                        $first = false;
//                    }
//                    if ($boolStatus) {
//                        if ($valS['jumlah'] > $tempQty) {
//                            $tmpKeluar['jumlah'][$indeks] = $tempQty;
//                            $valS['jumlah'] -= $tempQty;
//                            $boolStatus = false;
//                        } else {
//                            $tmpKeluar['jumlah'][$indeks] = ($tempQty > 0) ? $valS['jumlah'] : $tempQty;
//                            if ($valS['jumlah'] <= 0) {
//                                $valS['jumlah'] -= $tempQty;
//                                $tmpKeluar['jumlah'][$indeks] = $tempQty;
//                                $valS['harga'] = $val['harga_keluar'];
//                                $tempQty = $tempQty - $tmpKeluar['jumlah'][$indeks];
//                            } else {
//                                $tempQty -= $valS['jumlah'];
//                            }
//                        }
//                        //simpan stok keluar
//                        $tmpKeluar['harga'][$indeks] = ($tmpKeluar['jumlah'][$indeks] == 0) ? 0 : $val['harga_keluar'];
//                        $tmpKeluar['sub_total'][$indeks] = $tmpKeluar['jumlah'][$indeks] * $tmpKeluar['harga'][$indeks];
//                    }
//
//                    if ($valS['jumlah'] != 0) {
//                        //simpan stok saldo
//
//                        $tmpSaldo['jumlah'][$indeks] = (isset($end) && $end == $valS['jumlah']) ? 0 : $valS['jumlah'];
//                        $tmpSaldo['harga'][$indeks] = ($tmpSaldo['jumlah'][$indeks] == 0) ? 0 : $valS['harga'];
//                        $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['harga'][$indeks] * $tmpSaldo['jumlah'][$indeks];
//
//                        if (isset($tmpSaldo['jumlah'])) {
//                            $last_key = key($tmpSaldo['jumlah']);
//                            $end = $tmpSaldo['jumlah'][$last_key];
//
//                            if ($tmpSaldo['jumlah'][$indeks] == 0) {
//                                $tmpSaldo['jumlah'][$indeks] = 0;
//                            } else {
//                                $tmpSaldo['jumlah'][$indeks] = $valS['jumlah'];
//                            }
//                        }
//
//                        $tmp[$indeks]['jumlah'] = $tmpSaldo['jumlah'][$indeks];
//                        $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];
//                    }
//
//                    $indeks++;
////                    }
//                }
//            }
//
//            $body[$val['produk_id']]['jumlah'] = isset($tmpSaldo['jumlah']) ? $tmpSaldo['jumlah'] : 0;
//            $body[$val['produk_id']]['harga'] = isset($tmpSaldo['harga']) ? $tmpSaldo['harga'] : 0;
//            $body[$val['produk_id']]['sub_total'] = isset($tmpSaldo['sub_total']) ? $tmpSaldo['sub_total'] : 0;
//
//            $indeks++;
//            $pr = $val['produk_id'];
//            $i++;
//        }
//        return $body;
//    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'produk_id' => 'Produk ID',
            'cabang_id' => 'Cabang ID',
            'keterangan' => 'Keterangan',
            'jumlah_masuk' => 'Jumlah Masuk',
            'harga_masuk' => 'Harga Masuk',
            'jumlah_keluar' => 'Jumlah Keluar',
            'harga_keluar' => 'Harga Keluar',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

}
