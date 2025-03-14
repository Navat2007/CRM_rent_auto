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
        car.id, car.state_number, car.archive,
        car.need_refuel, car.need_service, car.trunk_condition,
        car.body_condition, car.interior_condition,
        car.failure, car.critical_failure,
        brand.name as brand, model.name as model, status.name as status, 
        class.name as class, color.name as color
    FROM 
        car
    LEFT JOIN 
        directory_car_brands as brand ON brand.id = car.directory_car_brands_id
    LEFT JOIN 
        directory_car_models as model ON model.id = car.directory_car_models_id
    LEFT JOIN 
        directory_car_classes as class ON class.id = car.directory_car_classes_id
    LEFT JOIN 
        directory_car_colors as color ON color.id = car.directory_car_colors_id
    LEFT JOIN 
        directory_car_statuses as status ON status.id = car.directory_car_statuses_id";

if($company_id != 0){
    $sql .= " AND car.company_id = '{$company_id}'";
}

$sql .= " ORDER BY car.id";

$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $row->id = (int)$row->id;
        $row->need_refuel = (int)$row->need_refuel;
        $row->need_service = (int)$row->need_service;
        $row->trunk_condition = (int)$row->trunk_condition;
        $row->body_condition = (int)$row->body_condition;
        $row->interior_condition = (int)$row->interior_condition;
        $row->brand = $row->brand == null ? "Не указано" : htmlspecialchars_decode($row->brand);
        $row->model = $row->model == null ? "Не указано" : htmlspecialchars_decode($row->model);
        $row->status = htmlspecialchars_decode($row->status);
        $row->state_number = htmlspecialchars_decode($row->state_number);
        $row->has_failure = $row->failure != "" || $row->critical_failure != "";
        $row->failure = htmlspecialchars_decode($row->failure);
        $row->critical_failure = htmlspecialchars_decode($row->critical_failure);
        $row->archive = (int)$row->archive == 0 ? 'Активен' : 'В архиве';

        $sql = "
        SELECT 
            booking.id, booking.start_date, booking.end_date, booking.client_id,
            ui.full_name as fio, ui.phone
        FROM 
            booking as booking
        INNER JOIN users_info ui on ui.user_id = booking.client_id
        WHERE 
            booking.car_id = '$row->id' AND booking.car_issued = 1 AND booking.car_returned = 0
        ORDER BY booking.start_date";
        $sqls[] = $sql;
        $result_booking = pg_query($conn, $sql);

        if(pg_num_rows($result_booking) > 0)
        {
            $row_booking = pg_fetch_object($result_booking);
            $row->booking = $row_booking;
        }
        else{
            $row->booking = null;
        }

        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';