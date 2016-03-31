<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "transfer".
 *
 * @property integer $id
 * @property string $nota
 * @property string $tgl_transfer
 * @property integer $gudang_id
 * @property integer $cabang_id
 * @property integer $pengirim_id
 * @property integer $penerima_id
 * @property string $status
 * @property string $tgl_terima
 */
class Transfer extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'transfer';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nota', 'tgl_transfer', 'gudang_id', 'cabang_id', 'pengirim_id'], 'required'],
            [['tgl_transfer', 'tgl_terima'], 'safe'],
            [['gudang_id', 'cabang_id', 'pengirim_id', 'penerima_id'], 'integer'],
            [['status'], 'string'],
            [['nota'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'nota' => 'Nota',
            'tgl_transfer' => 'Tgl Transfer',
            'gudang_id' => 'Gudang ID',
            'cabang_id' => 'Cabang ID',
            'pengirim_id' => 'Pengirim ID',
            'penerima_id' => 'Penerima ID',
            'status' => 'Status',
            'tgl_terima' => 'Tgl Terima',
        ];
    }

    public function getGudang() {
        return $this->hasOne(Cabang::className(), ['id' => 'gudang_id']);
    }

    public function getCabang() {
        return $this->hasOne(Cabang::className(), ['id' => 'cabang_id']);
    }
    
    public function getKonfirmasi() {
        return $this->hasOne(Pengguna::className(), ['id' => 'penerima_id']);
    }

    public function behaviors() {
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
