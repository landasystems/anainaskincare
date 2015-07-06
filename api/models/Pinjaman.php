<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pinjaman".
 *
 * @property integer $id
 * @property integer $penjualan_id
 * @property string $debet
 * @property string $credit
 * @property string $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $modified_at
 * @property integer $modified_by
 */
class Pinjaman extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pinjaman';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['penjualan_id', 'debet', 'credit', 'created_at', 'created_by', 'modified_at', 'modified_by'], 'integer'],
            [['status'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'penjualan_id' => 'Penjualan ID',
            'debet' => 'Debet',
            'credit' => 'Credit',
            'status' => 'lunas | belum lunas',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
    }
}
