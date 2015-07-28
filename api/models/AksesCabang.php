<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_akses_cabang".
 *
 * @property integer $id
 * @property integer $roles_id
 * @property integer $cabang_id
 */
class AksesCabang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_akses_cabang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roles_id', 'cabang_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'roles_id' => 'Roles ID',
            'cabang_id' => 'User ID',
        ];
    }
}
