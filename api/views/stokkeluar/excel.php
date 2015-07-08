<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-Transaksi-StokKeluar.xls");
?>
<h3>Data Master Stok Keluar</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Cabang</th>
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
            <td><?=$arr['tanggal']?></td>
            <td><?=$arr['keterangan']?></td>
            <td>&nbsp;<?=$arr['total']?></td>
            
        </tr>
    <?php } ?>
</table>

