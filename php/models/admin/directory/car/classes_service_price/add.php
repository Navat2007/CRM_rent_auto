<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$directory_car_classes_id = htmlspecialchars($_POST["directory_car_classes_id"]);
$directory_services_id = htmlspecialchars($_POST["directory_services_id"]);
$price = htmlspecialchars($_POST["price"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_car_classes_service_price WHERE directory_car_classes_id = '$directory_car_classes_id' AND directory_services_id = '$directory_services_id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $error = 1;
    $error_text = "Запись с такой услугой для данного класса уже существует";
}

if($error === 0){
    $sql = "INSERT INTO directory_car_classes_service_price (directory_car_classes_id, directory_services_id, price, archive, last_user_id) VALUES ('$directory_car_classes_id', '$directory_services_id', '$price', '$active', '$user') RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';