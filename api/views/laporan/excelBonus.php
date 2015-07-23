<?php
header("HTTP/1.1 200 OK");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

// The optional second 'replace' parameter indicates whether the header
// should replace a previous similar header, or add a second header of
// the same type. By default it will replace, but if you pass in FALSE
// as the second argument you can force multiple headers of the same type.
header("Cache-Control: private", false);

header("Content-type: application/vnd-ms-excel");

// $strFileName is, of course, the filename of the file being downloaded. 
// This won't have to be the same name as the actual file.
header("Content-Disposition: attachment; filename=excel-master-barang.xls"); 

header("Content-Transfer-Encoding: binary");
?>
<center>
    <h4>LAPORAN BONUS KARYAWAN ANAINA SKIN CARE</h4>
    <h5>CABANG : <?php echo $data['cabang'] ?></h5>
    <h5>PERIODE : <?php echo $data['start'] ?> s/d <?php echo $data['end'] ?> </h5>
</center>
<table class="table table-bordered ">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kode Transaksi</th>
            <th>Produk</th>
            <th>Jabatan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($detail as $val) {
            ?>
            <tr>
                <td colspan="4"><b><?php echo $val['title']['nama'] ?></b></td>
                <td><b><?php echo $val['title']['sub_total'] ?></b></td>
            </tr>
            <?php
            foreach ($val['body'] as $vl) {
                ?>
                <tr>
                    <td><?php echo $vl['tanggal']?></td>
                    <td><?php echo $vl['kode']?></td>
                    <td><?php echo $vl['produk']?></td>
                    <td><?php echo $vl['jabatan']?></td>
                    <td><?php echo $vl['fee']?></td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right"><b>Total</b></td>
            <td><b><?php // echo $detail['total']?></b></td>
        </tr>
    </tfoot>
</table>