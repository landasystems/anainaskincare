<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_stok".
 *
 * @property integer $id
 * @property integer $cabang_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 * @property string $tanggal
 */
class MStok extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'm_stok';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cabang_id', 'produk_id', 'jumlah', 'harga'], 'integer'],
            [['tanggal'], 'safe']
        ];
    }

    public static function process($type, $date, $kode, $product_id, $qty, $departement_id, $price = 0, $keterangan = '') {
        $produk = Barang::findOne(['id' => $product_id]);
        $masuk = array();
        $keluar = array();
        $saldo = array();
        $masuk['jumlah'] = 0;
        $masuk['harga'] = 0;
        $keluar['jumlah'] = 0;
        $keluar['harga'] = 0;

        if ($type == 'in') {
            $stokProduk = new MStok();
            $stokProduk->kode = $kode;
            $stokProduk->produk_id = $product_id;
            $stokProduk->cabang_id = $departement_id;
            $stokProduk->jumlah = $qty;
            $stokProduk->harga = $price;
            $stokProduk->tanggal = $date . " " . date("H:i:s");
            $stokProduk->save();

            $boolStatus = true;
            $masuk['jumlah'] = $qty;
            $masuk['harga'] = $price;
            $stokProduk = KartuStok::findOne(['produk_id' => $product_id, 'cabang_id' => $departement_id]);
            if (!empty($stokProduk)) {
                $saldo = json_decode($stokProduk->saldo, true);
                $saldo[] = array('jumlah' => $masuk['jumlah'], 'harga' => $masuk['harga']);
            } else {
                $saldo[] = array('jumlah' => $masuk['jumlah'], 'harga' => $masuk['harga']);
            }
        } else if ($type == 'out') {
            $stokProduk = MStok::find()->
                    where(['produk_id' => $product_id, 'cabang_id' => $departement_id])->
                    orderBy('tanggal ASC')->
                    all();
            $tempQty = $qty;
            $boolStatus = true;
            $keluar['jumlah'] = $qty;
            $keluar['harga'] = $price;
//            foreach ($stokProduk as $val) {
//                if ($boolStatus) {
//                    if ($val->jumlah > $tempQty) {
//                        $val->jumlah -= $tempQty;
//                        $val->save();
//
//                        $boolStatus = false;
//                        $saldo[] = array('jumlah' => $val->jumlah, 'harga' => $val->harga);
//
//                        $keluar['jumlah'] = $tempQty;
//                        $keluar['harga'] = $val->harga;
//                    } else {
//                        $keluar['jumlah'] = $val->jumlah;
//                        $keluar['harga'] = $val->harga;
//
//                        $tempQty -= $val->jumlah;
////                        $val->delete();
//                    }
//                } else {
//                    $saldo[] = array('jumlah' => $val->jumlah, 'harga' => $val->harga);
//                }
//            }
        }
        $kartuStok = new KartuStok();
        $update = $kartuStok->process($keterangan, $date, $kode, $product_id, $masuk, $keluar, $saldo, $departement_id);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'cabang_id' => 'Cabang ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'tanggal' => 'Tanggal',
        ];
    }

}
