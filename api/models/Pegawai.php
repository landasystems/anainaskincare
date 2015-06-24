<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_pegawai".
 *
 * @property integer $id
 * @property string $kode
 * @property string $nama
 * @property string $jenis_kelamin
 * @property string $no_tlp
 * @property string $email
 * @property string $alamat
 * @property string $jabatan
 * @property integer $klinik_id
 * @property integer $is_deleted
 */
class Pegawai extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_pegawai';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alamat'], 'string'],
            [['klinik_id', 'is_deleted'], 'integer'],
            [['kode', 'no_tlp'], 'string', 'max' => 25],
            [['nama', 'email'], 'string', 'max' => 45],
            [['jenis_kelamin'], 'string', 'max' => 15],
            [['jabatan'], 'string', 'max' => 20]
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
            'jabatan' => 'terapis | dokter',
            'klinik_id' => 'Klinik ID',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
