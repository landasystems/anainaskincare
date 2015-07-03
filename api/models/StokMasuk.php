<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stok_masuk".
 *
 * @property integer $id
 * @property string $kode
 * @property integer $cabang_id
 * @property string $tanggal
 * @property string $keterangan
 * @property integer $total
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $modified_at
 * @property integer $modified_by
 *
 * @property StokMasukDet[] $stokMasukDets
 */
class StokMasuk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stok_masuk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cabang_id', 'total', 'created_at', 'created_by', 'modified_at', 'modified_by'], 'integer'],
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
            'cabang_id' => 'Cabang ID',
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
    public function getStokMasukDets()
    {
        return $this->hasMany(StokMasukDet::className(), ['stok_masuk_id' => 'id']);
    }
}
