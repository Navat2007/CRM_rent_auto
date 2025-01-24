<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$companyId = htmlspecialchars($_POST["companyId"]);
$carId = htmlspecialchars($_POST["carId"]);
$clientId = htmlspecialchars($_POST["clientId"]);
$directory_territory_car_use_id = htmlspecialchars($_POST["directory_territory_car_use_id"]);
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
        INSERT INTO booking (car_id, client_id, directory_territory_car_use_id, start_date, end_date, last_user_id) 
        VALUES ('$carId', '$clientId', '$directory_territory_car_use_id', " . (empty($start_date) ? 'NULL' : "'" . $start_date . "'") . ", " . (empty($end_date) ? 'NULL' : "'" . $end_date . "'") . ", $user) 
        RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params[] = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';