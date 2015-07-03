<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stok_masuk_det".
 *
 * @property integer $id
 * @property integer $stok_masuk_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 *
 * @property StokMasuk $stokMasuk
 */
class StokMasukDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stok_masuk_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'stok_masuk_id', 'produk_id', 'jumlah', 'harga'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stok_masuk_id' => 'Stok Masuk ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStokMasuk()
    {
        return $this->hasOne(StokMasuk::className(), ['id' => 'stok_masuk_id']);
    }
}
