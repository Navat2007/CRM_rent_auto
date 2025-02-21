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

$sql = "SELECT * FROM directory_car_classes_service_price WHERE directory_car_classes_id = '$directory_car_classes_id' AND directory_services_id = '$directory_services_id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $sql = "
    UPDATE 
        directory_car_classes_service_price 
    SET 
        directory_car_classes_id = '$directory_car_classes_id', 
        directory_services_id = '$directory_services_id', 
        price = '$price', 
        last_user_id = '$user' 
    WHERE 
        directory_car_classes_id = '$directory_car_classes_id' AND directory_services_id = '$directory_services_id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
}
else{
    $sql = "INSERT INTO directory_car_classes_service_price (directory_car_classes_id, directory_services_id, price, last_user_id) VALUES ('$directory_car_classes_id', '$directory_services_id', '$price', '$user') RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';