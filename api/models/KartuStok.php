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
            $criteria .= ' and penjualan = ' . $trans_id;
        } elseif ($keterangan == "pembelian") {
            $criteria .= ' and pembelian = ' . $trans_id;
        } elseif ($keterangan == "r_penjualan") {
            $criteria .= ' and r_penjualan = ' . $trans_id;
        } elseif ($keterangan == "r_pembelian") {
            $criteria .= ' and r_pembelian = ' . $trans_id;
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

    public function saldo($type, $cabang, $kategori, $date) {
        $criteria = '';
        $body = array();

        if (!empty($cabang))
            $criteria .= ' and kartu_stok.cabang_id = ' . $cabang;

        if (!empty($kategori))
            $criteria .= ' and m_produk.kategori_id = ' . $kategori;

        if ($type == 'balance') {
            $criteria .= "date(kartu_stok.created_at) < '" . $date . "'";
        } else if ($type == 'today') {
            $criteria .= "date(kartu_stok.created_at) <= '" . date("Y-m-d") . "'";
        }

        $query = new Query;
        $query->from(['m_produk', 'm_satuan', 'm_kategori', 'kartu_stok'])
                ->select("kartu_stok.*, m_produk.nama as produk, m_kategori.nama as kategori, m_satuan.nama as satuan")
                ->where("m_produk.kategori_id = m_kategori.id and m_produk.satuan_id = m_satuan.id and m_produk.id = kartu_stok.produk_id and $criteria")
                ->orderBy("kartu_stok.produk_id, kartu_stok.created_at ASC, kartu_stok.id ASC");

        $command = $query->createCommand();
        $kartu = $command->queryAll();
        $i = 0;
        $produk_id = 0;
        $created = '';
        $a = 1;
        $tmpSaldo = array();
        foreach ($kartu as $val) {
            if ($produk_id != $val['produk_id']) {
                $tmp[1]['jumlah'] = 0;
                $tmp[1]['harga'] = 0;
                $a = 1;
            }

            if ($val['jumlah_keluar'] == 0) {

                $tmpSaldo[$a] = array('jumlah' => $val['jumlah_masuk'], 'harga' => $val['harga_masuk'], 'sub_total' => ($val['harga_masuk'] * $val['jumlah_masuk']));

                $tmp[$a]['jumlah'] = $val['jumlah_masuk'];
                $tmp[$a]['harga'] = $val['harga_masuk'];
            } else {
                $tempQty = $val['jumlah_keluar'];
                $boolStatus = true;
                $index = 1;
                foreach ($tmp as $valS) {
                    if ($boolStatus) {
                        if ($valS['jumlah'] > $tempQty) {
                            $valS['jumlah'] -= $tempQty;
                            $tmp[$index]['jumlah'] = $valS['jumlah'];

                            $boolStatus = false;
                            $tmpSaldo[$a] = array('jumlah' => $valS['jumlah_masuk'], 'harga' => $valS['harga_masuk'], 'sub_total' => ($valS['harga_masuk'] * $valS['jumlah_masuk']));
                        } else {
                            $tempQty -= $valS['jumlah'];
                            unset($tmp[$index]);
                        }
                    } else {
                        $tmpSaldo[$a] = array('jumlah' => $valS['jumlah_masuk'], 'harga' => $valS['harga_masuk'], 'sub_total' => ($valS['harga_masuk'] * $valS['jumlah_masuk']));
                    }
                    $a++;
                    $index++;
                }
            }

            $body[$val['produk_id']] = $tmpSaldo;
            $i++;
            $a++;
            $produk_id = $val['produk_id'];
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
