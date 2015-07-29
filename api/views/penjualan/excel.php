<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-Penjualan.xls");
?>
<h3>Penjualan</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Cabang</th>
        <th>Customer</th>
        <th>Tanggal</th>
        <th>Keterangan</th>
        <th>Total</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kode']?></td>
            <td><?=$arr['cabang']?></td>
            <td><?=$arr['customer']?></td>
            <td><?=$arr['tanggal']?></td>
            <td><?=$arr['keterangan']?></td>
            <td>&nbsp;<?=$arr['total']?></td>
            
        </tr>
    <?php } ?>
</table>

