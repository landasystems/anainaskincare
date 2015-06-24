<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_produk".
 *
 * @property integer $id
 * @property string $kode
 * @property string $type
 * @property string $nama
 * @property integer $kategori_id
 * @property integer $satuan_id
 * @property string $keterangan
 * @property integer $harga_beli_terakhir
 * @property integer $harga_jual
 * @property integer $diskon
 * @property integer $minimum_stok
 * @property integer $fee_terapis
 * @property integer $fee_dokter
 * @property string $foto
 * @property integer $is_deleted
 * @property string $created_at
 * @property integer $created_by
 * @property string $modified_at
 * @property integer $modified_by
 */
class Barang extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'm_produk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kategori_id', 'satuan_id', 'harga_beli_terakhir', 'harga_jual', 'diskon', 'minimum_stok', 'fee_terapis', 'fee_dokter', 'is_deleted', 'created_by', 'modified_by'], 'integer'],
            [['keterangan'], 'string'],
            [['created_at', 'modified_at'], 'safe'],
            [['kode', 'type'], 'string', 'max' => 25],
            [['nama'], 'string', 'max' => 45],
            [['foto'], 'string', 'max' => 255]
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
            'type' => 'jasa | barang',
            'nama' => 'Nama',
            'kategori_id' => 'Kategori ID',
            'satuan_id' => 'Satuan ID',
            'keterangan' => 'Keterangan',
            'harga_beli_terakhir' => 'Harga Beli Terakhir',
            'harga_jual' => 'Harga Jual',
            'diskon' => 'Diskon',
            'minimum_stok' => 'Minimum Stok',
            'fee_terapis' => 'Fee Terapis',
            'fee_dokter' => 'Fee Dokter',
            'foto' => 'Foto',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'modified_at' => 'Modified At',
            'modified_by' => 'Modified By',
        ];
    }
}