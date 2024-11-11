<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$directory = isset($_POST["directory"]) ? htmlspecialchars($_POST["directory"]) : die("Не передан directory");
$ID = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");
$user = $authorization[1];

if((int)$user != 1){
    die();
}

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

$sql = "DELETE FROM $directory WHERE id = '$ID'";
pg_query($conn, $sql);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';