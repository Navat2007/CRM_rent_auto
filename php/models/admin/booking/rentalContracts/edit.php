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
$userId = isset($_POST["userId"]) ? htmlspecialchars($_POST["userId"]) : null;
$legal_person_Id = htmlspecialchars($_POST["legal_person_Id"]);
$directory_territory_car_use_id = htmlspecialchars($_POST["directory_territory_car_use_id"]);
$address_give_out = htmlspecialchars($_POST["address_give_out"]);
$address_take_back = htmlspecialchars($_POST["address_take_back"]);
$start_date = htmlspecialchars($_POST["start_date"]);
$end_date = htmlspecialchars($_POST["end_date"]);
$deposit = htmlspecialchars($_POST["deposit"]);
$car_issued = htmlspecialchars($_POST["car_issued"]) === "true" ? 1 : 0;
$car_returned = htmlspecialchars($_POST["car_returned"]) === "true" ? 1 : 0;
$rental_days = htmlspecialchars($_POST["rental_days"]);
$rental_rate = htmlspecialchars($_POST["rental_rate"]);
$rental_cost = htmlspecialchars($_POST["rental_cost"]);
$note_rental_cost = htmlspecialchars($_POST["note_rental_cost"]);
$mileage_start = htmlspecialchars($_POST["mileage_start"]);
$mileage_end = htmlspecialchars($_POST["mileage_end"]);

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
        user_id = " . (empty($userId) ? 'NULL' : "'" . $userId . "'") . ", 
        legal_person_id = '$legal_person_Id',
        directory_territory_car_use_id = '$directory_territory_car_use_id',
        address_give_out = '$address_give_out', 
        address_take_back = '$address_take_back',         
        start_date = " . (empty($start_date) ? 'NULL' : "'" . $start_date . "'") . ", 
        end_date = " . (empty($end_date) ? 'NULL' : "'" . $end_date . "'") . ",
        deposit = '$deposit',
        car_issued = '$car_issued',
        car_returned = '$car_returned',
        rental_days = '$rental_days',
        rental_rate = '$rental_rate',
        rental_cost = '$rental_cost',
        note_rental_cost = '$note_rental_cost',
        mileage_start = '$mileage_start',
        mileage_end = '$mileage_end',
        last_user_id = '$user'
    WHERE 
        id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';