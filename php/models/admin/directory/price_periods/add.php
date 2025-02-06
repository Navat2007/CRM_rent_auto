<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$name = htmlspecialchars($_POST["name"]);
$days_from = htmlspecialchars($_POST["days_from"]);
$days_until = $_POST["days_until"] == '' ? NULL : htmlspecialchars($_POST["days_until"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_price_periods WHERE name = '$name'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $error = 1;
    $error_text = "Такой ценовой период уже существует";
}

if($error === 0){
    $sql = "INSERT INTO directory_price_periods (name, days_from, days_until, archive, last_user_id) VALUES ('$name', '$days_from', " . ($days_until == NULL ? 'NULL' : $days_until) .", '$active', '$user') RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';