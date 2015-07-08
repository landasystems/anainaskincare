<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=roles-excel.xls");
$i=0;
?>
<table>
    <tr>
        <th>#</th>
        <th>Nama Hak Akses</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        $i++;
        ?>
        <tr>
            <td><?=$i?></td>
            <td><?=$arr['nama']?></td>
        </tr>
    <?php } ?>
</table>

