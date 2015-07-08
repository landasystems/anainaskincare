<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_penjualan_det".
 *
 * @property integer $id
 * @property integer $r_penjualan_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 * @property integer $sub_total
 *
 * @property RPenjualan $rPenjualan
 */
class RPenjualanDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_penjualan_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['r_penjualan_id', 'produk_id', 'jumlah_retur', 'harga','diskon', 'sub_total'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'r_penjualan_id' => 'R Penjualan ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'sub_total' => 'Sub Total',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRPenjualan()
    {
        return $this->hasOne(RPenjualan::className(), ['id' => 'r_penjualan_id']);
    }
}
