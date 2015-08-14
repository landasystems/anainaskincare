<?php

use yii\db\Query;
use yii\web\Session;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-Penjualan.xls");
if (isset($filter['cabang_id'])) {
    $selCabang = \app\models\Cabang::findOne(['id' => $filter['cabang_id']]);
    $cabang = $selCabang['nama'];
} else {
    $cabang = 'SEMUA CABANG';
}
if (isset($filter['tanggal'])) {
    $value = explode(' - ', $filter['tanggal']);
    $start = date("d-m-Y", strtotime($value[0]));
    $end = date("d-m-Y", strtotime($value[1]));
    if ($start == $end)
        $tgl = 'TANGGAL : ' . $start;
    else
        $tgl = 'TANGGAL : ' . $start . ' - ' . $end;
}else {
    $tgl = '';
}
if (isset($filter['m_produk.kategori_id'])) {
    $kategori = \app\models\Kategori::findOne(['id' => $filter['m_produk.kategori_id']]);
    $ktg = 'KATEGORI PRODUK : <b>' . strtoupper($kategori['nama']) . '</b>';
} else {
    $ktg = '';
}
?>
<style>
    .tabel{
        border-collapse: collapse;
    }

    .tabel th, td{
        border: 1px solid  #333;
    }
</style>
<div align="LEFT">
    <h3 style="margin: 0px">RUMAH CANTIK ANAINA</h3>
    <h5 style="margin: 0px; font-weight: normal">CABANG : <?php echo $cabang ?></h5>
    <?php
    if (isset($filter['cabang_id'])) {
        echo '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['alamat'] . '</h5>
                <h5 style="margin: 0px; font-weight: normal">' . $selCabang['no_tlp'] . '</h5>';
    }
    ?>
</div>
<br>
<center>
    <h3 style="margin: 0px;">LAPORAN NOTA PENJUALAN</h3>
    <p style="margin: 0px;"><?php echo $tgl ?></p>
    <p style="margin: 0px;"><?php echo $ktg ?></p>
</center>
<br>
<table width="100%" class="tabel">
    <tr>
        <td colspan="2" align="center"><b>FAKTUR</b></td>
        <td rowspan="2" align="center"><b>CUSTOMER</b></td>
        <td rowspan="2" align="center"><b>KASIR</b></td>
        <td rowspan="2" align="center"><b>NAMA BARANG</b></td>
        <td rowspan="2" align="center"><b>JUMLAH</b></td>
        <td rowspan="2" align="center"><b>HARGA @</b></td>
        <td rowspan="2" align="center"><b>DISKON @</b></td>
        <td rowspan="2" align="center"><b>SUB TOTAL</b></td>
    </tr>
    <tr>
        <td align="center"><b>TANGGAL</b></td>
        <td align="center"><b>NO</b></td>
    </tr>
    <?php
    $query = new Query;
    $query->from('penjualan')
            ->join('LEFT JOIN', 'penjualan_det', 'penjualan_det.penjualan_id = penjualan.id')
            ->join('LEFT JOIN', 'm_cabang', 'penjualan.cabang_id = m_cabang.id')
            ->join('LEFT JOIN', 'm_customer', 'penjualan.customer_id = m_customer.id')
            ->join('LEFT JOIN', 'm_produk', 'penjualan_det.produk_id = m_produk.id')
            ->join('LEFT JOIN', 'm_user', 'penjualan.created_by = m_user.id')
            ->orderBy('penjualan.kode DESC')
            ->select('m_user.nama as kasir, penjualan.id as id_penjualan, penjualan.tanggal, penjualan.kode, m_customer.nama as customer, m_produk.nama as produk, penjualan_det.jumlah, penjualan_det.harga, penjualan_det.diskon, penjualan_det.sub_total')
            ->where('m_produk.type = "Barang"');

    foreach ($filter as $key => $val) {
        if ($key == 'tanggal') {
            $value = explode(' - ', $val);
            $start = date("Y-m-d", strtotime($value[0]));
            $end = date("Y-m-d", strtotime($value[1]));
            $query->andFilterWhere(['between', 'tanggal', $start, $end]);
        } else {
            $query->andFilterWhere(['like', $key, $val]);
        }
    }

    $command = $query->createCommand();
    $models = $command->queryAll();
    $data = array();
    $total = 0;

    foreach ($models as $key => $val) {
        $subTotal = ($val['harga'] * $val['jumlah']) - ($val['diskon'] * $val['jumlah']);
        $total += $subTotal;
        $data[$val['id_penjualan']]['tanggal'] = date("d-m-Y", strtotime($val['tanggal']));
        $data[$val['id_penjualan']]['kode'] = $val['kode'];
        $data[$val['id_penjualan']]['customer'] = $val['customer'];
        $data[$val['id_penjualan']]['kasir'] = empty($val['kasir']) ? '-' : $val['kasir'];
        $data[$val['id_penjualan']]['produk'] = isset($data[$val['id_penjualan']]['produk']) ? $data[$val['id_penjualan']]['produk'] . '<br>' . strtoupper($val['produk']) : strtoupper($val['produk']);
        $data[$val['id_penjualan']]['jumlah'] = isset($data[$val['id_penjualan']]['jumlah']) ? $data[$val['id_penjualan']]['jumlah'] . '<br>' . $val['jumlah'] : $val['jumlah'];
        $data[$val['id_penjualan']]['harga'] = isset($data[$val['id_penjualan']]['harga']) ? $data[$val['id_penjualan']]['harga'] . '<br>' . $val['harga'] : $val['harga'];
        $data[$val['id_penjualan']]['diskon'] = isset($data[$val['id_penjualan']]['diskon']) ? $data[$val['id_penjualan']]['diskon'] . '<br>' . $val['diskon'] : $val['diskon'];
        $data[$val['id_penjualan']]['sub_total'] = isset($data[$val['id_penjualan']]['sub_total']) ? $data[$val['id_penjualan']]['sub_total'] . '<br>' . $subTotal : $subTotal;
    }

    foreach ($data as $value) {
        echo '<tr>';
        echo '<td valign="top">' . $value['tanggal'] . '</td>';
        echo '<td valign="top">' . $value['kode'] . '</td>';
        echo '<td valign="top">' . $value['customer'] . '</td>';
        echo '<td valign="top">' . $value['kasir'] . '</td>';
        echo '<td>' . $value['produk'] . '</td>';
        echo '<td align="right">' . $value['jumlah'] . '</td>';
        echo '<td align="right">' . $value['harga'] . '</td>';
        echo '<td align="right">' . $value['diskon'] . '</td>';
        echo '<td align="right">' . $value['sub_total'] . '</td>';
        echo '</tr>';
    }
    ?>
    <tr>
        <td colspan="8" align="right"><b>TOTAL</b></td>
        <td align="right"><?php echo $total ?></td>
    </tr>
</table>

