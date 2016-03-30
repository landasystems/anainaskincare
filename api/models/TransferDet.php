<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transfer_det".
 *
 * @property integer $id
 * @property integer $transfer_id
 * @property integer $id_barang
 * @property integer $qty
 * @property integer $harga
 */
class TransferDet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'transfer_det';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['transfer_id', 'id_barang', 'qty', 'harga'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transfer_id' => 'Transfer ID',
            'id_barang' => 'Id Barang',
            'qty' => 'Qty',
            'harga' => 'Harga',
        ];
    }
}
