<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_pembelian_det".
 *
 * @property integer $id
 * @property integer $r_pembelian_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 * @property integer $sub_total
 *
 * @property RPembelian $rPembelian
 */
class RPembelianDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_pembelian_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['r_pembelian_id', 'produk_id', 'jumlah', 'harga', 'sub_total'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'r_pembelian_id' => 'R Pembelian ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'sub_total' => 'Sub Total',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRPembelian()
    {
        return $this->hasOne(RPembelian::className(), ['id' => 'r_pembelian_id']);
    }
}
