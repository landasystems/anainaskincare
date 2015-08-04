<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-Piutang.xls");
?>
<h3>Data Transaksi Hutang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Cabang</th>
        <th>Customer</th>
        <th>Tanggal</th>
        <th>Status</th>
        <th>Total</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kode']?></td>
            <td><?=$arr['klinik']?></td>
            <td><?=$arr['nama_supplier']?></td>
            <td><?=$arr['tanggal']?></td>
            <td><?=$arr['status']?></td>
            <td>&nbsp;<?=$arr['total']?></td>
            
        </tr>
    <?php } ?>
</table>

