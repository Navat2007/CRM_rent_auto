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

$sql = "SELECT * FROM booking WHERE id = $id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $row = pg_fetch_object($result);

    $row->id = (int)$row->id;
    $row->carId = (int)$row->car_id;
    $row->clientId = (int)$row->client_id;
    $row->directory_territory_car_use_id = (int)$row->directory_territory_car_use_id;

    $params = $row;

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';