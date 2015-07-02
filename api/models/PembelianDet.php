<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pembelian_det".
 *
 * @property integer $id
 * @property integer $pembelian_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 * @property integer $diskon
 * @property integer $sub_total
 *
 * @property Pembelian $pembelian
 */
class PembelianDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pembelian_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembelian_id', 'produk_id', 'jumlah', 'harga', 'diskon', 'sub_total'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pembelian_id' => 'Pembelian ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'diskon' => 'Diskon',
            'sub_total' => 'Sub Total',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembelian()
    {
        return $this->hasOne(Pembelian::className(), ['id' => 'pembelian_id']);
    }
}
