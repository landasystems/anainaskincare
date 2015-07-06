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

    public function process($type, $product_id, $qty, $departement_id, $price = 0, $keterangan = '') {
        $produk = Barang::findOne(['id' => $product_id]);
        $masuk = array();
        $keluar = array();
        $saldo = array();

        if ($type == 'in') {
            $stokProduk = new MStok();
            $stokProduk->produk_id = $product_id;
            $stokProduk->cabang_id = $departement_id;
            $stokProduk->jumlah = $qty;
            $stokProduk->harga = $price;
            $stokProduk->save();
        } else if ($type == 'out') {
            $stokProduk = MStok::find()->
                    where(['produk_id' => $product_id, 'cabang_id' => $departement_id])->
                    orderBy('tanggal ASC')->
                    all();
            $tempQty = $qty;
            $boolStatus = true;
            foreach ($stokProduk as $val) {
                if ($boolStatus) {
                    if ($val->jumlah > $tempQty) {
                        $val->jumlah -= $tempQty;
                        $val->save();

                        $boolStatus = false;
                        $saldo['jumlah'] = $val->jumlah;
                        $saldo['harga'] = $val->harga;

                        $keluar['jumlah'] = $tempQty;
                        $keluar['harga'] = $val->harga;
                    } else {
                        $keluar['jumlah'] = $val->jumlah;
                        $keluar['harga'] = $val->harga;

                        $tempQty -= $val->jumlah;
                        $val->delete();
                    }
                } else {
                    $saldo['jumlah'] = $val->jumlah;
                    $saldo['harga'] = $val->harga;
                }
            }
        }
        KartuStok::process($keterangan, $produk_id, $masuk, $keluar, $saldo, $cabang_id);
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
