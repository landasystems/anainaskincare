<?php

use yii\db\Query;
use yii\web\Session;

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-Penjualan.xls");

$filter = $_SESSION['filter'];

$start = date("Y-m-d", strtotime($filter['tanggal']['startDate']));
$end = date("Y-m-d", strtotime($filter['tanggal']['endDate']));

if (isset($filter['cabang'])) {
    $selCabang = \app\models\Cabang::findOne(['id' => $filter['cabang']]);
    $cabang = $selCabang['nama'];
    $selCabang['alamat'] = '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['alamat'] . '</h5>';
    $selCabang['no_tlp'] = '<h5 style="margin: 0px; font-weight: normal">' . $selCabang['no_tlp'] . '</h5>';
} else {
    $cabang = 'SEMUA CABANG';
    $selCabang['alamat'] = '';
    $selCabang['no_tlp'] = '';
}
if (isset($filter['tanggal'])) {
    $start = date("d-m-Y", strtotime($start));
    $end = date("d-m-Y", strtotime($end));
    if ($start == $end)
        $tgl = 'TANGGAL : ' . $start;
    else
        $tgl = 'TANGGAL : ' . $start . ' - ' . $end;
}else {
    $tgl = '';
}

if (isset($filter['jenis']) and ! empty($filter['jenis'])) {
    $detail['kategori'] = 'KATEGORI : ' . strtoupper($filter['jenis']) . '<br>';
} else {
    $detail['kategori'] = '';
}

if (isset($filter['kategori'])) {
    $kategori_id = array();
    foreach ($filter['kategori'] as $val) {
        if (isset($filter['jenis']) and ! empty($filter['jenis'])) {
            if ($filter['jenis'] == $val['jenis'])
                $kategori_id[] = $val['id'];
        } else {
            $kategori_id[] = $val['id'];
        }
    }

    $kategori = \app\models\Kategori::findAll(['id' => $kategori_id]);
    $nmKategori = array();
    foreach ($kategori as $val) {
        $nmKategori[] = strtoupper($val['nama']);
    }
    $nama = join(',', $nmKategori);
    $ktg = empty($nmKategori) ? '' : 'SUB KATEGORI : <b>' . $nama . '</b>';
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
        <th colspan="3" align="center"><b>PEMBAYARAN</b></th>
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
        <td align="center"><b>CASH</b></td>
        <td align="center"><b>DEBIT</b></td>
        <td align="center"><b>KREDIT</b></td>
    </tr>
    <?php
    $data = array();
    $total = 0;
    $totalDiskon = 0;
    $totalCash = 0;
    $totalCredit = 0;
    $totalAtm = 0;

    $query = $_SESSION['query'];

    $command = $query->createCommand();
    $models = $command->queryAll();

    foreach ($models as $key => $val) {
        $subTotal = ($val['harga'] * $val['jumlah']) - ($val['diskon'] * $val['jumlah']);
        $totalDiskon += $val['diskon'];
        $data[$val['id_penjualan']]['tanggal'] = date("d-m-Y", strtotime($val['tanggal']));
        $data[$val['id_penjualan']]['kode'] = $val['kode'];
        $data[$val['id_penjualan']]['cash'] = $val['cash'];
        $data[$val['id_penjualan']]['credit'] = $val['credit'];
        $data[$val['id_penjualan']]['atm'] = $val['atm'];
        $data[$val['id_penjualan']]['customer'] = $val['customer'];
        $data[$val['id_penjualan']]['kasir'] = empty($val['kasir']) ? '-' : $val['kasir'];
        $data[$val['id_penjualan']]['produk'] = isset($data[$val['id_penjualan']]['produk']) ? $data[$val['id_penjualan']]['produk'] . '<br>' . strtoupper($val['produk']) : strtoupper($val['produk']);
        $data[$val['id_penjualan']]['jumlah'] = isset($data[$val['id_penjualan']]['jumlah']) ? $data[$val['id_penjualan']]['jumlah'] . '<br>' . $val['jumlah'] : $val['jumlah'];
        $data[$val['id_penjualan']]['harga'] = isset($data[$val['id_penjualan']]['harga']) ? $data[$val['id_penjualan']]['harga'] . '<br>' . $val['harga'] : $val['harga'];
        $data[$val['id_penjualan']]['diskon'] = isset($data[$val['id_penjualan']]['diskon']) ? $data[$val['id_penjualan']]['diskon'] . '<br>' . $val['diskon'] : $val['diskon'];
        $data[$val['id_penjualan']]['sub_total'] = isset($data[$val['id_penjualan']]['sub_total']) ? $data[$val['id_penjualan']]['sub_total'] . '<br>' . $subTotal : $subTotal;
        $total += $subTotal;
    }

    foreach ($data as $value) {
        $totalCash += $value['cash'];
        $totalAtm += $value['atm'];
        $totalCredit += $value['credit'];

        echo '<tr>';
        echo '<td valign="top">' . $value['tanggal'] . '</td>';
        echo '<td valign="top">' . $value['kode'] . '</td>';
        echo '<td valign="top">' . $value['cash'] . '</td>
              <td valign="top">' . $value['atm'] . '</td>
              <td valign="top">' . $value['credit'] . '</td>';
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
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"><?php echo $totalCash ?></td>
        <td align="right"><?php echo $totalAtm ?></td>
        <td align="right"><?php echo $totalCredit ?></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"><?= $totalDiskon ?></td>
        <td align="right"><?php echo $total ?></td>
    </tr>
</table>

