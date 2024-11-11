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

$ID = htmlspecialchars($_POST["id"]);
$user = $authorization[1];

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

if((int)$user != 1){
    die();
}

if((int)$ID === 1){
    $error = 1;
    $error_text = "Данного администратора нельзя удалять!";
    die();
}

$sql = "DELETE FROM users WHERE id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_access WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_additional_contacts WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_company WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_driving_license WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_driving_license_files WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_info WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_other_files WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_passport WHERE user_id = '$ID'";
pg_query($conn, $sql);

$sql = "DELETE FROM users_passport_files WHERE user_id = '$ID'";
pg_query($conn, $sql);

$baseDirName = $_SERVER['DOCUMENT_ROOT'] . "/files/users/" . $ID;
deleteDir($baseDirName);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';