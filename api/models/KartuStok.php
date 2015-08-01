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

    public function saldo($type, $cabang, $kategori, $date, $id_produk = '') {
        $criteria = '';
        $body = array();

        if (!empty($cabang))
            $criteria .= ' and kartu_stok.cabang_id = ' . $cabang;

        if (empty($id_produk)) {
            if (!empty($kategori))
                $criteria .= ' and m_produk.kategori_id = ' . $kategori;
        }else {
            if (!empty($id_produk))
                $criteria .= ' and m_produk.id = ' . $id_produk;
        }

        if ($type == 'balance') {
            $criteria .= " and date(kartu_stok.created_at) < '" . $date . "'";
        } else if ($type == 'today') {
            $criteria .= " and date(kartu_stok.created_at) <= '" . date("Y-m-d") . "'";
        }

        $query = new Query;
        $query->from(['m_produk', 'kartu_stok'])
                ->select("kartu_stok.*")
                ->where("m_produk.is_deleted = 0 and m_produk.type = 'Barang' and m_produk.id = kartu_stok.produk_id $criteria")
                ->orderBy("kartu_stok.produk_id, kartu_stok.created_at ASC, kartu_stok.id ASC");
        $command = $query->createCommand();
        $kartu = $command->queryAll();
        $pr = 0;
        $i = 0;
        foreach ($kartu as $val) {

            if ($pr != $val['produk_id']) {
                $indeks = 1;
                //setting nilai awal temporari
                unset($tmpSaldo);
                unset($tmp);
                unset($tmpKeluar);
                unset($totalJml);
                unset($totalHarga);
                $tmpSaldo['jumlah'][0] = 0;
                $tmpSaldo['harga'][0] = 0;
                $tmpSaldo['sub_total'][0] = 0;

                $tmp[0]['jumlah'] = 0;
                $tmp[0]['harga'] = 0;
            } else {
                unset($tmpKeluar);
            }

            if ($val['jumlah_masuk'] > 0) {
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
                    if ($valS['jumlah'] > 0) {
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
                        $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['harga'][$indeks] * $tmpSaldo['jumlah'][$indeks];
                        $tmp[$indeks]['jumlah'] = $tmpSaldo['jumlah'][$indeks];
                        $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];
                        $indeks++;
                    }
                }
            } else {
                $tempQty = $val['jumlah_keluar'];
                $first = true;
                $boolStatus = true;
                $saldo = 0;
                foreach ($tmp as $valS) {
                    if ($valS['jumlah']) {
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
                                    $tempQty = $tempQty - $tmpKeluar['jumlah'][$indeks];
                                } else {
                                    $tempQty -= $valS['jumlah'];
                                }
                            }
                            //simpan stok keluar
                            $tmpKeluar['harga'][$indeks] = $val['harga_keluar'];
                            $tmpKeluar['sub_total'][$indeks] = $tmpKeluar['jumlah'][$indeks] * $tmpKeluar['harga'][$indeks];
                        }
                        //simpan stok saldo
                        $tmpSaldo['jumlah'][$indeks] = (isset($tmpKeluar['jumlah'][$indeks]) and $valS['jumlah'] == $tmpKeluar['jumlah'][$indeks]) ? 0 : $valS['jumlah'];
                        $tmpSaldo['harga'][$indeks] = $valS['harga'];
                        $tmpSaldo['sub_total'][$indeks] = $tmpSaldo['harga'][$indeks] * $tmpSaldo['jumlah'][$indeks];

                        $tmp[$indeks]['jumlah'] = $tmpSaldo['jumlah'][$indeks];
                        $tmp[$indeks]['harga'] = $tmpSaldo['harga'][$indeks];

                        $indeks++;
                    }
                }
            }

            $body[$val['produk_id']]['jumlah'] = $tmpSaldo['jumlah'];
            $body[$val['produk_id']]['harga'] = $tmpSaldo['harga'];
            $body[$val['produk_id']]['sub_total'] = $tmpSaldo['sub_total'];

            $indeks++;
            $pr = $val['produk_id'];
            $i++;
        }
        return $body;
    }

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
