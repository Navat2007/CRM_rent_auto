<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$directory = isset($_POST["directory"]) ? htmlspecialchars($_POST["directory"]) : die("Не передан directory");
$name = htmlspecialchars($_POST["name"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM $directory WHERE name = '$name'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $error = 1;
    $error_text = "Запись с таким названием уже существует";
}

if($error === 0){
    $sql = "INSERT INTO $directory (name, archive, last_user_id) VALUES ('$name', '$active', '$user') RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';