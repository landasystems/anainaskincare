<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stok_keluar_det".
 *
 * @property integer $id
 * @property integer $stok_keluar_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 *
 * @property StokKeluar $stokKeluar
 */
class StokKeluarDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stok_keluar_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'unique'],
            [['id', 'stok_keluar_id', 'produk_id', 'jumlah', 'harga'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stok_keluar_id' => 'Stok Keluar ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStokKeluar()
    {
        return $this->hasOne(StokKeluar::className(), ['id' => 'stok_keluar_id']);
    }
}
