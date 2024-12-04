<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "SELECT 
        t1.*
    FROM 
        directory_car_classes_service_price as t1
    WHERE t1.id = $id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $row = pg_fetch_object($result);

    $row->id = (int)$row->id;
    $row->price = (int)$row->price;
    $row->directory_car_classes_id = (int)$row->directory_car_classes_id;
    $row->directory_services_id = (int)$row->directory_services_id;
    $row->active = (int)$row->archive == 0;

    $params = $row;

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';