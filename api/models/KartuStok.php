<?php

namespace app\models;

use Yii;

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
            [['produk_id', 'cabang_id', 'jumlah_masuk', 'harga_masuk', 'jumlah_keluar', 'harga_keluar', 'jumlah_saldo', 'harga_saldo', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['keterangan'], 'string', 'max' => 255]
        ];
    }

    public function process($keterangan, $produk_id, $masuk, $keluar, $saldo, $cabang_id) {
        $sv = new KartuStok();
        $sv->produk_id = $produk_id;
        $sv->cabang_id = $cabang_id;
        $sv->jumlah_masuk = $masuk['jumlah'];
        $sv->harga_masuk = $masuk['harga'];
        $sv->jumlah_keluar = $keluar['jumlah'];
        $sv->harga_keluar = $keluar['harga'];
        $sv->jumlah_saldo = $saldo['jumlah'];
        $sv->harga_saldo = $saldo['harga'];
        $sv->save();
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
            'jumlah_saldo' => 'Jumlah Saldo',
            'harga_saldo' => 'Harga Saldo',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

}
