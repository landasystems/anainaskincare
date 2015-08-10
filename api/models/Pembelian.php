<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "pembelian".
 *
 * @property integer $id
 * @property string $kode
 * @property integer $cabang_id
 * @property integer $supplier_id
 * @property string $tanggal
 * @property string $keterangan
 * @property integer $total
 * @property integer $cash
 * @property integer $credit
 * @property string $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $modified_at
 * @property integer $modified_by
 *
 * @property PembelianDet $id0
 * @property Pinjaman $id1
 */
class Pembelian extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'pembelian';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cabang_id', 'supplier_id', 'total', 'cash', 'credit', 'created_at', 'created_by', 'modified_at', 'modified_by'], 'integer'],
            [['tanggal'], 'safe'],
            [['keterangan'], 'string'],
            [['kode'], 'string', 'max' => 25],
            [['status'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'cabang_id' => 'Cabang ID',
            'supplier_id' => 'Supplier ID',
            'tanggal' => 'Tanggal',
            'keterangan' => 'Keterangan',
            'total' => 'Total',
            'cash' => 'Cash',
            'credit' => 'Credit',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembelianDet() {
        return $this->hasOne(PembelianDet::className(), ['pembelian_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPinjaman() {
        return $this->hasOne(Pinjaman::className(), ['pembelian_id' => 'id']);
    }
    
     public function behaviors()
         {
             return [
                 [
                     'class' => BlameableBehavior::className(),
                     'createdByAttribute' => 'created_by',
                     'updatedByAttribute' => 'modified_by',
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
