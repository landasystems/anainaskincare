<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-laporan-barang.xls");
?>
<h3>Data Master Barang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Kategori</th>
        <th>Satuan</th>
        <th>Tipe</th>
        <th>Keterangan</th>
        <th>Harga Jual</th>
        <th>Diskon</th>
        <th>Minimum Stok</th>
        <th>Fee Terapis</th>
        <th>Fee Dokter</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kode']?></td>
            <td><?=$arr['nama']?></td>
            <td><?=$arr['kategori']?></td>
            <td><?=$arr['satuan']?></td>
            <td><?=$arr['type']?></td>
            <td><?=$arr['keterangan']?></td>
            <td style="text-align: right">&nbsp;<?=$arr['harga_jual']?></td>
            <td>&nbsp;<?=$arr['diskon']?></td>
            <td>&nbsp;<?=$arr['minimum_stok']?></td>
            <td style="text-align: right">&nbsp;<?=$arr['fee_terapis']?></td>
            <td style="text-align: right">&nbsp;<?=$arr['fee_dokter']?></td>
            
        </tr>
    <?php } ?>
</table>

