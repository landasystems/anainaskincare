<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_supplier".
 *
 * @property integer $id
 * @property string $kode
 * @property string $nama
 * @property string $alamat
 * @property string $no_tlp
 * @property string $email
 * @property integer $is_deleted
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_supplier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alamat'], 'string'],
            [['is_deleted'], 'integer'],
            [['kode', 'no_tlp'], 'string', 'max' => 25],
            [['nama', 'email'], 'string', 'max' => 45]
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
            'nama' => 'Nama',
            'alamat' => 'Alamat',
            'no_tlp' => 'No Tlp',
            'email' => 'Email',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
