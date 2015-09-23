<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-Piutang.xls");
?>
<h3>Data Transaksi Piutang</h3>
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
            <td><?=$arr['customer']?></td>
            <td><?=$arr['tanggal']?></td>
            <td><?=$arr['status']?></td>
            <td style="text-align: right;">&nbsp;<?=$arr['debet']?></td>
            
        </tr>
    <?php } ?>
</table>

