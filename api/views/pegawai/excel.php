<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-karyawan.xls");
?>
<h3>Data Master Karyawan</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>No. Telp</th>
        <th>Email</th>
        <th>Alamat</th>
        <th>Jabatan</th>
        <th>Office Place</th>
        <th>Cabang</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        
        ?>
        <tr>
            <td>&nbsp;<?=$arr['kode']?></td>
            <td><?=$arr['nama']?></td>
            <td><?=$arr['jenis_kelamin']?></td>
            <td>&nbsp;<?=$arr['no_tlp']?></td>
            <td><?=$arr['email']?></td>
            <td><?=$arr['alamat']?></td>
            <td><?=$arr['jabatan']?></td>
            <td><?=$arr['office_place']?></td>
            <td><?=$arr['cabang']?></td>
            
        </tr>
    <?php } ?>
</table>

