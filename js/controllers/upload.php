<?php

if (!empty($_FILES)) {

    $tempPath = $_FILES['file']['tmp_name'];
//    $namaFile = explode(".", $_FILES['file']['name']);
//    $jmlArr = count($namaFile);
//    $ekstensi = $namaFile[$jmlArr - 1];
//    $newName = date("h-s") . $namaFile[0] . "." . $ekstensi;
    $newName = $_GET['kode'] . "-" . $_FILES['file']['name'];

    $uploadPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $_GET['folder'] . DIRECTORY_SEPARATOR . $newName;

    move_uploaded_file($tempPath, $uploadPath);

    $answer = array('answer' => 'File transfer completed', 'name' => $newName);
    $json = json_encode($answer);

    echo $json;
} else {

    echo 'No files';
}
?>