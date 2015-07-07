<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "r_pembelian".
 *
 * @property integer $id
 * @property string $kode
 * @property integer $pembelian_id
 * @property string $tanggal
 * @property string $keterangan
 * @property integer $total
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $modified_at
 * @property integer $modified_by
 *
 * @property RPembelianDet[] $rPembelianDets
 */
class RPembelian extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'r_pembelian';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembelian_id', 'total', 'created_at', 'created_by', 'modified_at', 'modified_by'], 'integer'],
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
            'pembelian_id' => 'Pembelian ID',
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
    public function getRPembelianDets()
    {
        return $this->hasMany(RPembelianDet::className(), ['r_pembelian_id' => 'id']);
    }
}
