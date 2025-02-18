<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");
$name = htmlspecialchars($_POST["name"]);
$limit = htmlspecialchars($_POST["limit"]);
$deposit = htmlspecialchars($_POST["deposit"]);
$cost_extra_mileage = htmlspecialchars($_POST["cost_extra_mileage"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_car_classes WHERE id = '$id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$row = pg_fetch_object($result);

if($row->name != $name)
{
    $sql = "SELECT * FROM directory_car_classes WHERE name = '$name'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Запись с таким названием уже существует";
    }
}

if($error === 0){
    $sql = "
    UPDATE 
        directory_car_classes 
    SET 
        name = '$name', 
        \"limit\" = '$limit', 
        deposit = '$deposit', 
        cost_extra_mileage = '$cost_extra_mileage',
        archive = '$active', 
        last_user_id = '$user' 
    WHERE id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';