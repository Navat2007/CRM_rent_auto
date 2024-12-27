<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

function getContracts($car_id): array
{
    global $conn, $sqls, $year, $month, $day_in_month;
    $data = array();

    $sql = "
    SELECT 
        booking.id, booking.start_date, booking.end_date, booking.client_id,
        ui.full_name as fio, ui.phone, ui.user_photo_avatar as avatar, u.email
    FROM 
        booking as booking
    INNER JOIN users u on u.id = booking.client_id
    INNER JOIN users_info ui on ui.user_id = booking.client_id
    WHERE 
        car_id = '$car_id' AND (        
            (booking.start_date >= '$year-$month-01' AND booking.start_date <= '$year-$month-$day_in_month')
            OR
            (booking.end_date >= '$year-$month-01' AND booking.end_date <= '$year-$month-$day_in_month')        
        )
    ";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if (pg_num_rows($result) > 0) {
        while ($row = pg_fetch_object($result)) {
            $row->id = (int)$row->id;
            $row->client_id = (int)$row->client_id;
            $row->avatar = $row->avatar == null ? "" : ("https://" . $_SERVER['HTTP_HOST'] . $row->avatar);
            $row->fio = htmlspecialchars_decode($row->fio);

            $data[] = $row;
        }

        pg_free_result($result);
    }

    return $data;
}

$company_id = isset($_POST["company_id"]) ? (int)htmlspecialchars($_POST["company_id"]) : 0;
$year = isset($_POST["year"]) ? (int)htmlspecialchars($_POST["year"]) : Date('Y');
$month = isset($_POST["month"]) ? (int)htmlspecialchars($_POST["month"]) : Date('m');
$day_in_month = isset($_POST["day_in_month"]) ? (int)htmlspecialchars($_POST["day_in_month"]) : 31;

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        car.id, car.state_number, dcm.name as model, dcb.name as brand, dcc.name as class
    FROM 
        car as car
    INNER JOIN directory_car_models dcm on car.directory_car_models_id = dcm.id
    INNER JOIN directory_car_brands dcb on car.directory_car_brands_id = dcb.id
    INNER JOIN directory_car_classes dcc on car.directory_car_classes_id = dcc.id
    ORDER BY car.directory_car_brands_id ASC, car.directory_car_models_id ASC
    ";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if (pg_num_rows($result) > 0) {
    while ($row = pg_fetch_object($result)) {
        $row->contracts = getContracts($row->id);
        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';