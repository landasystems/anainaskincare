<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "stok_keluar".
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
 * @property StokKeluarDet[] $stokKeluarDets
 */
class StokKeluar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stok_keluar';
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
    public function getStokKeluarDets()
    {
        return $this->hasMany(StokKeluarDet::className(), ['stok_keluar_id' => 'id']);
    }
    
    public function behaviors()
         {
             return [
                 [
                     'class' => BlameableBehavior::className(),
                     'createdByAttribute' => 'created_at',
                     'updatedByAttribute' => 'modified_at',
                 ],
                 'timestamp' => [
                     'class' => 'yii\behaviors\TimestampBehavior',
                     'attributes' => [
                         ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'modified_at'],
                         ActiveRecord::EVENT_BEFORE_UPDATE => ['modified_at'],
                     ],
                 ],
             ];
         }
}
