<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-customer.xls");
?>
<h3>Data Master Customer</h3>
<br><br>
<table border="1">
    <tr>
        <th>Kode</th>
        <th>Nama</th>
        <th>Jenis Kelamin</th>
        <th>No. Telp</th>
        <th>Email</th>
        <th>Alamat</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td>&nbsp;<?= $arr['kode'] ?></td>
            <td><?= $arr['nama'] ?></td>
            <td><?= $arr['jenis_kelamin'] ?></td>
            <td>&nbsp;<?= $arr['no_tlp'] ?></td>
            <td><?= $arr['email'] ?></td>
            <td><?= $arr['alamat'] ?></td>

        </tr>
    <?php } ?>
</table>

