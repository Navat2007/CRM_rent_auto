<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");
$name = htmlspecialchars($_POST["name"]);
$brand_id = isset($_POST["brandId"]) ? (int)htmlspecialchars($_POST["brandId"]) : die("Не передан ID марки");
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_car_models WHERE id = '$id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$row = pg_fetch_object($result);

if($row->name != $name)
{
    $sql = "SELECT * FROM directory_car_models WHERE name = '$name' AND directory_car_brands_id = '$brand_id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Запись с таким названием для этой марки уже существует";
    }
}

if($error === 0){
    $sql = "UPDATE directory_car_models SET name = '$name', directory_car_brands_id = '$brand_id', archive = '$active', last_user_id = '$user' WHERE id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';