<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_user".
 *
 * @property integer $idroles_id
 * @property integer $roles_id
 * @property string $nama
 * @property string $username
 * @property string $password
 * @property integer $is_deleted
 * @property string $created_at
 * @property integer $created_by
 * @property string $modified_at
 * @property integer $modified_by
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['roles_id', 'is_deleted', 'created_by', 'modified_by'], 'integer'],
            [['created_at', 'modified_at', 'settings'], 'safe'],
            [['nama', 'username', 'password'], 'string', 'max' => 255]
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
            'nama' => 'Nama',
            'username' => 'Username',
            'password' => 'Password',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
    }
    
    public function getRoles() {
        return $this->hasOne(Roles::className(), ['id' => 'roles_id']);
    }
}
