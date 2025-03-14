<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$company_id = isset($_POST["company_id"]) ? (int)htmlspecialchars($_POST["company_id"]) : 0;
$car_id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID автомобиля");

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        booking.id, booking.start_date, booking.end_date, booking.client_id,
        ui.full_name as fio, ui.phone
    FROM 
        booking as booking
    INNER JOIN users_info ui on ui.user_id = booking.client_id
    WHERE 
        booking.car_id = '$car_id' AND booking.car_issued = 1 AND booking.car_returned = 0
    ORDER BY booking.start_date";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $params = $row = pg_fetch_object($result);
    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';