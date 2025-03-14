<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$company_id = isset($_POST["company_id"]) ? (int)htmlspecialchars($_POST["company_id"]) : 0;

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        booking.id, booking.start_date, booking.end_date,
        booking.rental_days, booking.accrued_total, booking.paid_total,
        booking.deposit_booking_total, booking.balance,
        car.state_number, dcm.name as model, dcb.name as brand, dcc.name as class,
        ui.full_name as fio
    FROM 
        booking as booking
    INNER JOIN users_info ui on ui.user_id = booking.client_id
    INNER JOIN car car on car.id = booking.car_id
    INNER JOIN directory_car_models dcm on car.directory_car_models_id = dcm.id
    INNER JOIN directory_car_brands dcb on car.directory_car_brands_id = dcb.id
    INNER JOIN directory_car_classes dcc on car.directory_car_classes_id = dcc.id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';