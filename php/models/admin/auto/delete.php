<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

function deleteDir($path): bool
{
    if (empty($path)) {
        return false;
    }
    return is_file($path) ?
        @unlink($path) :
        array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
}

$ID = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");
$user = $authorization[1];

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

if((int)$user != 1){
    die();
}

$sql = "DELETE FROM car WHERE id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM cars_photo_files WHERE car_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM cars_other_files WHERE car_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM cars_sts WHERE car_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM cars_sts_files WHERE car_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM cars_pts WHERE car_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM cars_pts_files WHERE car_id = '$ID'";
pg_query($conn, $sql);

$baseDirName = $_SERVER['DOCUMENT_ROOT'] . "/files/auto/" . $ID;
deleteDir($baseDirName);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';