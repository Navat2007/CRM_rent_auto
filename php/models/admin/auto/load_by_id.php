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

$sql = "
    SELECT 
        cars.*, 
        cp.number as pts_number, cp.series as pts_series, cp.issued_by_who as pts_issued_by_who,
        cp.issued_date as pts_issued_date, cp.vin as pts_vin, cp.body_number as pts_body_number,
        cp.purchase_date as pts_purchase_date, cp.cost_assessment as pts_cost_assessment,
        cp.cost_purchase as pts_cost_purchase, cp.directory_car_pts_ownership_id as pts_directory_car_pts_ownership_id,
        cp.legal_person_id as pts_legal_person_id,
        cs.client_id as sts_client_id, cs.legal_person_id as sts_legal_person_id, cs.series as sts_series,
        cs.number as sts_number, cs.issued_by_who as sts_issued_by_who, cs.issued_date as sts_issued_date,
        cs.expire_date as sts_expire_date
    FROM 
        car as cars
    LEFT JOIN cars_pts cp ON cars.id = cp.car_id
    LEFT JOIN cars_sts cs ON cars.id = cs.car_id
    WHERE 
        cars.id = $id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $row = pg_fetch_object($result);
    pg_free_result($result);

    $row->additional_parameter_1 = htmlspecialchars_decode($row->additional_parameter_1);
    $row->additional_parameter_2 = htmlspecialchars_decode($row->additional_parameter_2);
    $row->additional_parameter_3 = htmlspecialchars_decode($row->additional_parameter_3);
    $row->archive = (int)$row->archive;
    $row->body_condition = (int)$row->body_condition;
    $row->characteristic_additional_parameter_1 = htmlspecialchars_decode($row->characteristic_additional_parameter_1);
    $row->characteristic_additional_parameter_2 = htmlspecialchars_decode($row->characteristic_additional_parameter_2);
    $row->characteristic_additional_parameter_3 = htmlspecialchars_decode($row->characteristic_additional_parameter_3);
    $row->characteristic_airbag_switch = (int)$row->characteristic_airbag_switch;
    $row->characteristic_android_auto = (int)$row->characteristic_android_auto;
    $row->characteristic_armrest = (int)$row->characteristic_armrest;
    $row->characteristic_aux = (int)$row->characteristic_aux;
    $row->characteristic_bluetooth = (int)$row->characteristic_bluetooth;
    $row->characteristic_camera_360 = (int)$row->characteristic_camera_360;
    $row->characteristic_carplay = (int)$row->characteristic_carplay;
    $row->characteristic_cruise_control = (int)$row->characteristic_cruise_control;
    $row->characteristic_directory_car_colors_id = (int)$row->characteristic_directory_car_colors_id;
    $row->characteristic_directory_car_interior_materia_id = (int)$row->characteristic_directory_car_interior_materia_id;
    $row->characteristic_driver_assistance = (int)$row->characteristic_driver_assistance;
    $row->characteristic_front_parking_sensors = (int)$row->characteristic_front_parking_sensors;
    $row->characteristic_hatch = (int)$row->characteristic_hatch;
    $row->characteristic_heated_seats = (int)$row->characteristic_heated_seats;
    $row->characteristic_heated_windshield = (int)$row->characteristic_heated_windshield;
    $row->characteristic_isofix = (int)$row->characteristic_isofix;
    $row->characteristic_panorama = (int)$row->characteristic_panorama;
    $row->characteristic_railings = (int)$row->characteristic_railings;
    $row->characteristic_rear_parking_sensors = (int)$row->characteristic_rear_parking_sensors;
    $row->characteristic_rear_view_camera = (int)$row->characteristic_rear_view_camera;
    $row->company_id = (int)$row->company_id;
    $row->critical_failure = htmlspecialchars_decode($row->critical_failure);
    $row->directory_car_bodies_id = (int)$row->directory_car_bodies_id;
    $row->directory_car_brands_id = (int)$row->directory_car_brands_id;
    $row->directory_car_classes_id = (int)$row->directory_car_classes_id;
    $row->directory_car_colors_id = (int)$row->directory_car_colors_id;
    $row->directory_car_configurations_id = (int)$row->directory_car_configurations_id;
    $row->directory_car_fuel_types_id = (int)$row->directory_car_fuel_types_id;
    $row->directory_car_generations_id = (int)$row->directory_car_generations_id;
    $row->directory_car_models_id = (int)$row->directory_car_models_id;
    $row->directory_car_statuses_id = (int)$row->directory_car_statuses_id;
    $row->directory_car_transmissions_id = (int)$row->directory_car_transmissions_id;
    $row->directory_car_wheel_drive_id = (int)$row->directory_car_wheel_drive_id;
    $row->failure = htmlspecialchars_decode($row->failure);
    $row->fuel_consumption = htmlspecialchars_decode($row->fuel_consumption);
    $row->fuel_tank_capacity = htmlspecialchars_decode($row->fuel_tank_capacity);
    $row->interior_condition = (int)$row->interior_condition;
    $row->mileage = (int)$row->mileage;
    $row->need_refuel = (int)$row->need_refuel;
    $row->need_service = (int)$row->need_service;
    $row->release_year = (int)$row->release_year;
    $row->seats_count = (int)$row->seats_count;
    $row->state_number = htmlspecialchars_decode($row->state_number);
    $row->to_beacon_place_1 = htmlspecialchars_decode($row->to_beacon_place_1);
    $row->to_beacon_place_2 = htmlspecialchars_decode($row->to_beacon_place_2);
    $row->to_directory_car_tires_type_id = (int)$row->to_directory_car_tires_type_id;
    $row->to_directory_car_wheel_size_id = (int)$row->to_directory_car_wheel_size_id;
    $row->to_radio_code = htmlspecialchars_decode($row->to_radio_code);
    $row->to_secret = htmlspecialchars_decode($row->to_secret);
    $row->trunk_condition = (int)$row->trunk_condition;

    if($row->pts_number != null){
        $row->pts_issued_by_who = htmlspecialchars_decode($row->pts_issued_by_who);
        $row->pts_directory_car_pts_ownership_id = (int)$row->pts_directory_car_pts_ownership_id;
        $row->pts_legal_person_id = (int)$row->pts_legal_person_id;
    }

    if($row->sts_number != null){
        $row->sts_client_id = (int)$row->sts_client_id;
        $row->sts_legal_person_id = (int)$row->sts_legal_person_id;
        $row->sts_issued_by_who = htmlspecialchars_decode($row->sts_issued_by_who);
    }

    #region PTS files
    $sql = "
    SELECT 
        *
    FROM 
        cars_pts_files
    WHERE 
        car_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($pts_files_row = pg_fetch_object($result))
        {
            $row->pts_files[] = $pts_files_row;
        }

        pg_free_result($result);
    }
    #endregion

    #region STS files
    $sql = "
    SELECT 
        *
    FROM 
        cars_sts_files
    WHERE 
        car_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($sts_files_row = pg_fetch_object($result))
        {
            $row->sts_files[] = $sts_files_row;
        }

        pg_free_result($result);
    }
    #endregion

    #region Photo files
    $sql = "
    SELECT 
        *
    FROM 
        cars_photo_files
    WHERE 
        car_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($photo_files_row = pg_fetch_object($result))
        {
            $row->photo_files[] = $photo_files_row;
        }

        pg_free_result($result);
    }
    #endregion

    #region Other files
    $sql = "
    SELECT 
        *
    FROM 
        cars_other_files
    WHERE 
        car_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($other_files_row = pg_fetch_object($result))
        {
            $row->other_files[] = $other_files_row;
        }

        pg_free_result($result);
    }
    #endregion

    $params = $row;
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';