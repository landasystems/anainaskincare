<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=roles-excel.xls");
?>

<table>
    <tr>
        <th>Nama</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td><?=$arr['nama']?></td>
        </tr>
    <?php } ?>
</table>

