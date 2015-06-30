<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_kategori".
 *
 * @property integer $id
 * @property string $kode
 * @property string $nama
 * @property integer $is_deleted
 */
class Kategori extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'm_kategori';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['kode','nama'], 'unique'],
            [['is_deleted'], 'integer'],
            [['kode'], 'string', 'max' => 25],
            [['nama'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'kode' => 'Kode',
            'nama' => 'Nama',
            'is_deleted' => 'Is Deleted',
        ];
    }

}
