<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "hutang".
 *
 * @property integer $id
 * @property integer $pembelian_id
 * @property integer $debet
 * @property integer $credit
 * @property string $status
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $modified_at
 * @property integer $modified_by
 */
class Hutang extends \yii\db\ActiveRecord {

    public $sumDebet, $sumCredit;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'hutang';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'pembelian_id', 'debet', 'credit', 'created_at', 'created_by', 'modified_at', 'modified_by'], 'integer'],
            [['status'], 'string', 'max' => 20]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'pembelian_id' => 'Pembelian ID',
            'debet' => 'Debet',
            'credit' => 'Credit',
            'status' => 'lunas | belum lunas',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
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
