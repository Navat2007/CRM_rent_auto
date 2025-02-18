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
    $row->deposit = (float)$row->deposit;
    $row->mileage_start = (float)$row->mileage_start;
    $row->mileage_end = (float)$row->mileage_end;
    $row->rental_days = (int)$row->rental_days;
    $row->directory_territory_car_use_id = (int)$row->directory_territory_car_use_id;
    $row->address_give_out = $row->address_give_out == null ? null : htmlspecialchars_decode($row->address_give_out);
    $row->address_take_back = $row->address_take_back == null ? null : htmlspecialchars_decode($row->address_take_back);
    $row->note_rental_cost = $row->note_rental_cost == null ? null : htmlspecialchars_decode($row->note_rental_cost);

    $params = $row;

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';