<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");
$directory_car_classes_id = htmlspecialchars($_POST["directory_car_classes_id"]);
$directory_services_id = htmlspecialchars($_POST["directory_services_id"]);
$price = htmlspecialchars($_POST["price"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_car_classes_service_price WHERE id = '$id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$row = pg_fetch_object($result);

if($row->directory_car_classes_id != $directory_car_classes_id || $row->directory_services_id != $directory_services_id)
{
    $sql = "SELECT * FROM directory_car_classes_service_price WHERE directory_car_classes_id = '$directory_car_classes_id' AND directory_services_id = '$directory_services_id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Запись с такой услугой для данного класса уже существует";
    }
}

if($error === 0){
    $sql = "
    UPDATE 
        directory_car_classes_service_price 
    SET 
        directory_car_classes_id = '$directory_car_classes_id', 
        directory_services_id = '$directory_services_id', 
        price = '$price', 
        archive = '$active', 
        last_user_id = '$user' 
    WHERE 
        id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';