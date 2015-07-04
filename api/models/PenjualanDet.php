<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "penjualan_det".
 *
 * @property integer $id
 * @property integer $penjualan_id
 * @property integer $produk_id
 * @property string $type
 * @property integer $jumlah
 * @property integer $harga
 * @property integer $diskon
 * @property string $sub_total
 * @property integer $pegawai_terapis_id
 * @property integer $pegawai_dokter_id
 * @property integer $fee_terapis
 * @property integer $fee_dokter
 *
 * @property Penjualan $penjualan
 */
class PenjualanDet extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'penjualan_det';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['penjualan_id', 'produk_id', 'jumlah', 'harga', 'diskon', 'sub_total', 'pegawai_terapis_id', 'pegawai_dokter_id', 'fee_terapis', 'fee_dokter'], 'integer'],
            [['id'], 'unique'],
            [['type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'penjualan_id' => 'Penjualan ID',
            'produk_id' => 'Produk ID',
            'type' => 'Type',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'diskon' => 'Diskon',
            'sub_total' => 'Sub Total',
            'pegawai_terapis_id' => 'Pegawai Terapis ID',
            'pegawai_dokter_id' => 'Pegawai Dokter ID',
            'fee_terapis' => 'Fee Terapis',
            'fee_dokter' => 'Fee Dokter',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualan() {
        return $this->hasOne(Penjualan::className(), ['id' => 'penjualan_id']);
    }

}
