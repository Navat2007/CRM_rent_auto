<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$ID = htmlspecialchars($_POST["id"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

if((int)$ID === 1){
    $error = 1;
    $error_text = "Данного администратора нельзя удалять!";
}

if($error === 0){
    $sql = "UPDATE users SET archive = '1' WHERE id = '$ID'";
    pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';