<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_stok".
 *
 * @property integer $id
 * @property integer $cabang_id
 * @property integer $produk_id
 * @property integer $jumlah
 * @property integer $harga
 * @property string $tanggal
 */
class MStok extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_stok';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cabang_id', 'produk_id', 'jumlah', 'harga'], 'integer'],
            [['tanggal'], 'safe']
        ];
    }
    
    public function updateStok(){
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cabang_id' => 'Cabang ID',
            'produk_id' => 'Produk ID',
            'jumlah' => 'Jumlah',
            'harga' => 'Harga',
            'tanggal' => 'Tanggal',
        ];
    }
}
