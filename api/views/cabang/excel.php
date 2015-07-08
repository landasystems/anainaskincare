<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-cabang.xls");
?>
<h3>Data Master Cabang</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>No. Telp</th>
        <th>Email</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kode']?></td>
            <td><?=$arr['nama']?></td>
            <td><?=$arr['alamat']?></td>
            <td>&nbsp;<?=$arr['no_tlp']?></td>
            <td><?=$arr['email']?></td>
            
        </tr>
    <?php } ?>
</table>

