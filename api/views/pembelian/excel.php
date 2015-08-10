<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-Penjualan.xls");
use app\models\Cabang;
?>
<h3>Data Transaksi Pembelian</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Cabang</th>
        <th>Supplier</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Total</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        $findCabang = Cabang::findOne($arr['cabang_id']);
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kode']?></td>
            <td><?=$findCabang['nama']?></td>
            <td><?=$arr['nama_supplier']?></td>
            <td><?=$arr['tanggal']?></td>
            <td><?=$arr['keterangan']?></td>
            <td>&nbsp;<?=$arr['total']?></td>
            
        </tr>
    <?php } ?>
</table>

