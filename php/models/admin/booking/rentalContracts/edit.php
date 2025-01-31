<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$ID = htmlspecialchars($_POST["id"]);
$companyId = htmlspecialchars($_POST["companyId"]);
$carId = htmlspecialchars($_POST["carId"]);
$clientId = htmlspecialchars($_POST["clientId"]);
$directory_territory_car_use_id = htmlspecialchars($_POST["directory_territory_car_use_id"]);
$address_give_out = htmlspecialchars($_POST["address_give_out"]);
$address_take_back = htmlspecialchars($_POST["address_take_back"]);
$start_date = htmlspecialchars($_POST["start_date"]);
$end_date = htmlspecialchars($_POST["end_date"]);

$sql = "SELECT * FROM booking WHERE car_id = '$carId' AND start_date >= '$start_date' AND end_date <= '$end_date'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    //$error = 1;
    //$error_text = "Данный автомобиль уже забронирован на данный период";
}

if($error === 0){
    $sql = "
    UPDATE 
        booking 
    SET 
        car_id = '$carId', 
        client_id = '$clientId', 
        directory_territory_car_use_id = '$directory_territory_car_use_id',
        address_give_out = '$address_give_out', 
        address_take_back = '$address_take_back',         
        start_date = " . (empty($start_date) ? 'NULL' : "'" . $start_date . "'") . ", 
        end_date = " . (empty($end_date) ? 'NULL' : "'" . $end_date . "'") . ",
        last_user_id = '$user'
    WHERE 
        id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';