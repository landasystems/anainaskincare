<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paket_det".
 *
 * @property integer $id
 * @property integer $barang_id
 * @property integer $paket_id
 * @property integer $harga_jual
 */
class PaketDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paket_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['barang_id', 'paket_id', 'harga_jual'], 'required'],
            [['barang_id', 'paket_id', 'harga_jual'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'barang_id' => 'Barang ID',
            'paket_id' => 'Paket ID',
            'harga_jual' => 'Harga Jual',
        ];
    }
}
