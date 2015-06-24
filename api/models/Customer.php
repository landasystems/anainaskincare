<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_customer".
 *
 * @property integer $id
 * @property string $kode
 * @property string $nama
 * @property string $jenis_kelamin
 * @property string $no_tlp
 * @property string $email
 * @property string $alamat
 * @property integer $is_deleted
 */
class Customer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_customer';
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
            [['nama', 'email'], 'string', 'max' => 45],
            [['jenis_kelamin'], 'string', 'max' => 15]
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
            'jenis_kelamin' => 'Jenis Kelamin',
            'no_tlp' => 'No Tlp',
            'email' => 'Email',
            'alamat' => 'Alamat',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
