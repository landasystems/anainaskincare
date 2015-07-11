<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=excel-master-user.xls");
?>
<h3>Data Master User</h3>
<br><br>
<table border="1">
    <tr>
        <th>Nama</th>
        <th>Username</th>
        <th>Password</th>
        <th>Roles</th>
    </tr>
    <?php
    foreach ($models as $arr) {
        ?>
        <tr>
            <td><?= $arr['nama'] ?></td>
            <td><?= $arr['username'] ?></td>
            <td><?= $arr['password'] ?></td>
            <td><?= $arr['email'] ?></td>
            <td><?= $arr['roles'] ?></td>

        </tr>
    <?php } ?>
</table>

