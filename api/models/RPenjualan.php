<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_penjualan".
 *
 * @property integer $id
 * @property string $kode
 * @property integer $penjualan_id
 * @property string $tanggal
 * @property string $keterangan
 * @property integer $total
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $modified_at
 * @property integer $modified_by
 *
 * @property RPenjualanDet[] $rPenjualanDets
 */
class RPenjualan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_penjualan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['penjualan_id', 'total', 'created_at', 'created_by', 'modified_at', 'modified_by'], 'integer'],
            [['tanggal'], 'safe'],
            [['keterangan'], 'string'],
            [['kode'], 'string', 'max' => 25]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'penjualan_id' => 'Penjualan ID',
            'tanggal' => 'Tanggal',
            'keterangan' => 'Keterangan',
            'total' => 'Total',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRPenjualanDets()
    {
        return $this->hasMany(RPenjualanDet::className(), ['r_penjualan_id' => 'id']);
    }
}
