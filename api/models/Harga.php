<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "harga_beli".
 *
 * @property integer $id
 * @property integer $cabang_id
 * @property integer $produk_id
 * @property integer $harga
 */
class Harga extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'harga';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cabang_id', 'produk_id', 'harga_beli', 'harga_jual'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'cabang_id' => 'Cabang ID',
            'produk_id' => 'Produk ID',
            'harga_beli' => 'Harga Beli',
            'harga_jual' => 'Harga Jual',
        ];
    }

}
