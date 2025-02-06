<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

#region Functions
function SaveFiles($folder_title, $files_title, $db_title): void
{
    global $conn, $ID, $user, $error, $error_text;

    if (isset($_FILES[$files_title])) {
        $baseDirName = "/files/auto/" . $ID . "/" . $folder_title;

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $baseDirName)) {
            $oldmask = umask(0);
            $mkdir_result = mkdir($_SERVER['DOCUMENT_ROOT'] . $baseDirName, 0777, true);
            umask($oldmask);
        }

        foreach ($_FILES[$files_title]['error'] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $temp_name = $_FILES[$files_title]['tmp_name'][$key];
                $name = $_FILES[$files_title]['name'][$key];
                $file_name = explode('.', $name)[0];
                $file_extension = explode('.', $name)[1];
                $size = $_FILES[$files_title]['size'][$key];
                $file_token = time();
                $path = $_SERVER['DOCUMENT_ROOT'] . $baseDirName . "/" . $file_token . "_" . $name;

                @unlink($path);

                if (copy($temp_name, $path)) {
                    $file_to_DB = $baseDirName . "/" . $file_token . "_" . $name;

                    $sql = "
                    INSERT INTO $db_title (car_id, file_path, file_name, file_extension, file_size, last_user_id) 
                    VALUES ('$ID', '$file_to_DB', '$file_name', '$file_extension', '$size', '$user')";
                    pg_query($conn, $sql);
                }
            } else {
                $error = 1;
                $error_text = $error;
            }
        }
    }
}

function CheckFilesToDelete($files, $table): void
{
    global $conn, $ID, $user, $error, $error_text;

    foreach ($files as $file) {
        if (isset($file["deleted"]) && (int)$file["deleted"] === 1) {
            $fileID = $file['id'];

            $sql = "
            SELECT 
                *
            FROM 
                $table
            WHERE 
                id = '$fileID'";
            $result = pg_query($conn, $sql);
            $row = pg_fetch_object($result);

            $path = $_SERVER['DOCUMENT_ROOT'] . $row->file_path;
            @unlink($path);

            $sql = "DELETE FROM $table WHERE id = '$fileID' AND user_id = '$ID'";
            pg_query($conn, $sql);
        }
    }
}

#endregion

#region Variables
$user = $authorization[1];
$companyId = htmlspecialchars($_POST["companyId"]);
$ID = htmlspecialchars($_POST["id"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$avatar = isset($_POST["avatar"]) ? htmlspecialchars($_POST["avatar"]) : null;
$state_number = htmlspecialchars($_POST["state_number"]);
$directory_car_classes_id = htmlspecialchars($_POST["directory_car_classes_id"]);
$release_year = htmlspecialchars($_POST["release_year"]);
$directory_car_brands_id = htmlspecialchars($_POST["directory_car_brands_id"]);
$directory_car_models_id = htmlspecialchars($_POST["directory_car_models_id"]);
$directory_car_generations_id = htmlspecialchars($_POST["directory_car_generations_id"]);
$directory_car_configurations_id = htmlspecialchars($_POST["directory_car_configurations_id"]);
$date_park_enter = htmlspecialchars($_POST["date_park_enter"]);
$date_park_exit = htmlspecialchars($_POST["date_park_exit"]);
$mileage = htmlspecialchars($_POST["mileage"]);
$directory_car_statuses_id = htmlspecialchars($_POST["directory_car_statuses_id"]);
$body_condition = htmlspecialchars($_POST["body_condition"]);
$interior_condition = htmlspecialchars($_POST["interior_condition"]);
$trunk_condition = htmlspecialchars($_POST["trunk_condition"]);
$need_refuel = htmlspecialchars($_POST["need_refuel"]);
$need_service = htmlspecialchars($_POST["need_service"]) === "true" ? 1 : 0;
$failure = htmlspecialchars($_POST["failure"]);
$critical_failure = htmlspecialchars($_POST["critical_failure"]);
$directory_car_bodies_id = htmlspecialchars($_POST["directory_car_bodies_id"]);
$directory_car_fuel_types_id = htmlspecialchars($_POST["directory_car_fuel_types_id"]);
$directory_car_wheel_drive_id = htmlspecialchars($_POST["directory_car_wheel_drive_id"]);
$directory_car_transmissions_id = htmlspecialchars($_POST["directory_car_transmissions_id"]);
$fuel_tank_capacity = htmlspecialchars($_POST["fuel_tank_capacity"]);
$fuel_consumption = htmlspecialchars($_POST["fuel_consumption"]);
$directory_car_colors_id = htmlspecialchars($_POST["directory_car_colors_id"]);
$seats_count = htmlspecialchars($_POST["seats_count"]);
$additional_parameter_1 = htmlspecialchars($_POST["additional_parameter_1"]);
$additional_parameter_2 = htmlspecialchars($_POST["additional_parameter_2"]);
$additional_parameter_3 = htmlspecialchars($_POST["additional_parameter_3"]);

$characteristic_rear_view_camera = htmlspecialchars($_POST["characteristic_rear_view_camera"]) === "true" ? 1 : 0;
$characteristic_front_parking_sensors = htmlspecialchars($_POST["characteristic_front_parking_sensors"]) === "true" ? 1 : 0;
$characteristic_rear_parking_sensors = htmlspecialchars($_POST["characteristic_rear_parking_sensors"]) === "true" ? 1 : 0;
$characteristic_camera_360 = htmlspecialchars($_POST["characteristic_camera_360"]) === "true" ? 1 : 0;
$characteristic_panorama = htmlspecialchars($_POST["characteristic_panorama"]) === "true" ? 1 : 0;
$characteristic_AUX = htmlspecialchars($_POST["characteristic_AUX"]) === "true" ? 1 : 0;
$characteristic_bluetooth = htmlspecialchars($_POST["characteristic_bluetooth"]) === "true" ? 1 : 0;
$characteristic_carplay = htmlspecialchars($_POST["characteristic_carplay"]) === "true" ? 1 : 0;
$characteristic_android_auto = htmlspecialchars($_POST["characteristic_android_auto"]) === "true" ? 1 : 0;
$characteristic_heated_seats = htmlspecialchars($_POST["characteristic_heated_seats"]) === "true" ? 1 : 0;
$characteristic_heated_windshield = htmlspecialchars($_POST["characteristic_heated_windshield"]) === "true" ? 1 : 0;
$characteristic_driver_assistance = htmlspecialchars($_POST["characteristic_driver_assistance"]) === "true" ? 1 : 0;
$characteristic_railings = htmlspecialchars($_POST["characteristic_railings"]) === "true" ? 1 : 0;
$characteristic_isofix = htmlspecialchars($_POST["characteristic_isofix"]) === "true" ? 1 : 0;
$characteristic_hatch = htmlspecialchars($_POST["characteristic_hatch"]) === "true" ? 1 : 0;
$characteristic_cruise_control = htmlspecialchars($_POST["characteristic_cruise_control"]) === "true" ? 1 : 0;
$characteristic_armrest = htmlspecialchars($_POST["characteristic_armrest"]) === "true" ? 1 : 0;
$characteristic_airbag_switch = htmlspecialchars($_POST["characteristic_airbag_switch"]) === "true" ? 1 : 0;
$characteristic_directory_car_interior_materia_id = htmlspecialchars($_POST["characteristic_directory_car_interior_materia_id"]);
$characteristic_directory_car_colors_id = htmlspecialchars($_POST["characteristic_directory_car_colors_id"]);
$characteristic_additional_parameter_1 = htmlspecialchars($_POST["characteristic_additional_parameter_1"]);
$characteristic_additional_parameter_2 = htmlspecialchars($_POST["characteristic_additional_parameter_2"]);
$characteristic_additional_parameter_3 = htmlspecialchars($_POST["characteristic_additional_parameter_3"]);

$to_directory_car_wheel_size_id = htmlspecialchars($_POST["to_directory_car_wheel_size_id"]);
$to_directory_car_tires_type_id = htmlspecialchars($_POST["to_directory_car_tires_type_id"]);
$to_radio_code = htmlspecialchars($_POST["to_radio_code"]);
$to_secret = htmlspecialchars($_POST["to_secret"]);
$to_beacon_place_1 = htmlspecialchars($_POST["to_beacon_place_1"]);
$to_beacon_place_2 = htmlspecialchars($_POST["to_beacon_place_2"]);

$pts_series = htmlspecialchars($_POST["pts_series"]);
$pts_number = htmlspecialchars($_POST["pts_number"]);
$pts_issued_by_who = htmlspecialchars($_POST["pts_issued_by_who"]);
$pts_issued_date = htmlspecialchars($_POST["pts_issued_date"]);
$pts_vin = htmlspecialchars($_POST["pts_vin"]);
$pts_body_number = htmlspecialchars($_POST["pts_body_number"]);
$pts_purchase_date = htmlspecialchars($_POST["pts_purchase_date"]);
$pts_cost_assessment = htmlspecialchars($_POST["pts_cost_assessment"]);
$pts_cost_purchase = htmlspecialchars($_POST["pts_cost_purchase"]);
$pts_directory_car_pts_ownership_id = htmlspecialchars($_POST["pts_directory_car_pts_ownership_id"]);
$pts_legal_person_id = htmlspecialchars($_POST["pts_legal_person_id"]);

$sts_client_id = htmlspecialchars($_POST["sts_client_id"]);
$sts_legal_person_id = htmlspecialchars($_POST["sts_legal_person_id"]);
$sts_series = htmlspecialchars($_POST["sts_series"]);
$sts_number = htmlspecialchars($_POST["sts_number"]);
$sts_issued_by_who = htmlspecialchars($_POST["sts_issued_by_who"]);
$sts_issued_date = htmlspecialchars($_POST["sts_issued_date"]);
$sts_expire_date = htmlspecialchars($_POST["sts_expire_date"]);

$price_periods = $_POST["price_periods"];

$error = 0;
$error_text = "";
$sqls = array();
$params = null;
#endregion

#region Car valid check
$sql = "SELECT * FROM car WHERE id = '$ID'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$car_row = pg_fetch_object($result);

if ($car_row->state_number != $state_number) {
    $sql = "SELECT * FROM car WHERE state_number = '$state_number' AND archive = 0";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if (pg_num_rows($result) > 0) {
        $error = 1;
        $error_text = "Автомобиль с указанным гос. номером уже существует";
    }
}
#endregion

if ($error === 0) {
    #region Car
    $sql = "
    UPDATE 
        car 
    SET 
        archive = '$active', 
        state_number = '$state_number', 
        release_year = " . (empty($release_year) ? 'NULL' : "'" . $release_year . "'") . ", 
        directory_car_classes_id = '$directory_car_classes_id',
        directory_car_brands_id = '$directory_car_brands_id',
        directory_car_models_id = '$directory_car_models_id',
        directory_car_generations_id = '$directory_car_generations_id',
        directory_car_configurations_id = '$directory_car_configurations_id',
        date_park_enter = " . (empty($date_park_enter) ? 'NULL' : "'" . $date_park_enter . "'") . ",
        date_park_exit = " . (empty($date_park_exit) ? 'NULL' : "'" . $date_park_exit . "'") . ",
        mileage = '$mileage',
        directory_car_statuses_id = '$directory_car_statuses_id',
        body_condition = '$body_condition',
        interior_condition = '$interior_condition',
        trunk_condition = '$trunk_condition',
        need_refuel = '$need_refuel',
        need_service = '$need_service',
        failure = '$failure',
        critical_failure = '$critical_failure',
        directory_car_bodies_id = '$directory_car_bodies_id',
        directory_car_fuel_types_id = '$directory_car_fuel_types_id',
        directory_car_wheel_drive_id = '$directory_car_wheel_drive_id',
        directory_car_transmissions_id = '$directory_car_transmissions_id',
        fuel_tank_capacity = '$fuel_tank_capacity',
        fuel_consumption = '$fuel_consumption',
        directory_car_colors_id = '$directory_car_colors_id',
        seats_count = '$seats_count',
        additional_parameter_1 = '$additional_parameter_1',
        additional_parameter_2 = '$additional_parameter_2',
        additional_parameter_3 = '$additional_parameter_3',
        characteristic_rear_view_camera = '$characteristic_rear_view_camera',
        characteristic_front_parking_sensors = '$characteristic_front_parking_sensors',
        characteristic_rear_parking_sensors = '$characteristic_rear_parking_sensors',
        characteristic_camera_360 = '$characteristic_camera_360',
        characteristic_panorama = '$characteristic_panorama',
        characteristic_AUX = '$characteristic_AUX',
        characteristic_bluetooth = '$characteristic_bluetooth',
        characteristic_carplay = '$characteristic_carplay',
        characteristic_android_auto = '$characteristic_android_auto',
        characteristic_heated_seats = '$characteristic_heated_seats',
        characteristic_heated_windshield = '$characteristic_heated_windshield',
        characteristic_driver_assistance = '$characteristic_driver_assistance',
        characteristic_railings = '$characteristic_railings',
        characteristic_isofix = '$characteristic_isofix',
        characteristic_hatch = '$characteristic_hatch',
        characteristic_cruise_control = '$characteristic_cruise_control',
        characteristic_armrest = '$characteristic_armrest',
        characteristic_airbag_switch = '$characteristic_airbag_switch',
        characteristic_directory_car_interior_materia_id = '$characteristic_directory_car_interior_materia_id',
        characteristic_directory_car_colors_id = '$characteristic_directory_car_colors_id',
        characteristic_additional_parameter_1 = '$characteristic_additional_parameter_1',
        characteristic_additional_parameter_2 = '$characteristic_additional_parameter_2',
        characteristic_additional_parameter_3 = '$characteristic_additional_parameter_3',
        to_directory_car_wheel_size_id = '$to_directory_car_wheel_size_id',
        to_directory_car_tires_type_id = '$to_directory_car_tires_type_id',
        to_radio_code = '$to_radio_code',
        to_secret = '$to_secret',
        to_beacon_place_1 = '$to_beacon_place_1',
        to_beacon_place_2 = '$to_beacon_place_2',
        company_id = '$companyId',
        last_user_id = '$user'
    WHERE id = '$ID'
    ";
    $sqls[] = $sql;
    pg_query($conn, $sql);
    #endregion

    #region Avatar
    if (isset($_FILES['avatar'])) {
        $baseDirName = $_SERVER['DOCUMENT_ROOT'] . "/files/auto/" . $ID . "/avatar";

        if (!file_exists($baseDirName)) {
            $oldmask = umask(0);
            $mkdir_result = mkdir($baseDirName, 0777, true);
            umask($oldmask);
        }

        if ($_FILES['avatar']['error'] != UPLOAD_ERR_OK) {
            $error = 1;
            $error_text = "Ошибка загрузки файла";
        } else {
            $temp_name = $_FILES['avatar']['tmp_name'];
            $name = $_FILES['avatar']['name'];
            $file_token = time();
            $path = $baseDirName . "/" . $file_token . "_" . $name;

            @unlink($path);

            $sql = "SELECT * FROM car WHERE id = '$ID'";
            $sqls[] = $sql;
            $result = pg_query($conn, $sql);
            $row = pg_fetch_object($result);

            $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->car_photo_avatar;
            @unlink($oldpath);

            if (copy($temp_name, $path)) {
                $file_to_DB = "/files/auto/" . $ID . "/avatar/" . $file_token . "_" . $name;

                $add_sql = "UPDATE 
                                car
                            SET
                                car_photo_avatar = '$file_to_DB'
                            WHERE 
                                id = '$ID'";
                pg_query($conn, $add_sql);
                $params = $file_to_DB;
            }
        }
    } else if (empty($avatar)) {
        $sql = "SELECT * FROM car WHERE id = '$ID'";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);

        if ($row) {
            $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->car_photo_avatar;
            @unlink($oldpath);
        }

        $add_sql = "UPDATE 
                        car
                    SET
                        car_photo_avatar = ''
                    WHERE 
                        id = '$ID'";
        pg_query($conn, $add_sql);
    }
    #endregion

    #region Price periods
    $sql = "DELETE FROM car_price_per_day WHERE car_id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    if (count($price_periods) > 0){
        for ($i = 0; $i < count($price_periods); $i++) {
            $directory_price_period_id = $price_periods[$i]['id'];
            $price = $price_periods[$i]['price'];

            if($price != ''){
                $sql = "INSERT INTO car_price_per_day (
                car_id,
                directory_price_period_id,
                price,
                last_user_id
            ) 
            VALUES (
                '$ID',
                '$directory_price_period_id',
                '$price',
                '$user' 
            )";
                $sqls[] = $sql;
                pg_query($conn, $sql);
            }
        }
    }
    #endregion

    #region PTS
    $sql = "DELETE FROM cars_pts WHERE car_id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    if (!empty($pts_number)) {
        $sql = "INSERT INTO cars_pts (
                car_id,
                number,
                series,
                issued_by_who,
                issued_date,
                vin,
                body_number,
                purchase_date,
                cost_assessment,
                cost_purchase,
                directory_car_pts_ownership_id,
                legal_person_id,
                last_user_id
            ) 
            VALUES (
                '$ID',
                '$pts_number',
                '$pts_series',
                '$pts_issued_by_who',                 
                " . (empty($pts_issued_date) ? 'NULL' : "'" . $pts_issued_date . "'") . ",                 
                '$pts_vin',                 
                '$pts_body_number',       
                " . (empty($pts_purchase_date) ? 'NULL' : "'" . $pts_purchase_date . "'") . ",            
                '$pts_cost_assessment',
                '$pts_cost_purchase',
                '$pts_directory_car_pts_ownership_id',
                '$pts_legal_person_id',
                '$user' 
            )";
        $sqls[] = $sql;
        pg_query($conn, $sql);
    }
    #endregion

    #region PTS files
    $folder_title = 'pts';
    $files_title = 'pts_files';
    $files_upload_title = 'pts_upload_files';
    $db_title = 'cars_pts_files';

    if (isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    #endregion

    #region STS
    $sql = "DELETE FROM cars_sts WHERE car_id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    if (!empty($sts_number)) {
        $sql = "INSERT INTO cars_sts (
                car_id,
                client_id,
                legal_person_id,  
                number,
                series,
                issued_by_who,
                issued_date,
                expire_date,
                last_user_id
            ) 
            VALUES (
                '$ID',
                '$sts_client_id',
                '$sts_legal_person_id',
                '$sts_number',
                '$sts_series',
                '$sts_issued_by_who',
                " . (empty($sts_issued_date) ? 'NULL' : "'" . $sts_issued_date . "'") . ",                 
                " . (empty($sts_expire_date) ? 'NULL' : "'" . $sts_expire_date . "'") . ",
                '$user'            
            )";
        $sqls[] = $sql;
        pg_query($conn, $sql);
    }
    #endregion

    #region STS files
    $folder_title = 'sts';
    $files_title = 'sts_files';
    $files_upload_title = 'sts_upload_files';
    $db_title = 'cars_sts_files';

    if (isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    #endregion

    #region Photo files
    $folder_title = 'photo';
    $files_title = 'photo_files';
    $files_upload_title = 'photo_upload_files';
    $db_title = 'cars_photo_files';

    if (isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    #endregion

    #region Other files
    $folder_title = 'other';
    $files_title = 'other_files';
    $files_upload_title = 'other_upload_files';
    $db_title = 'cars_other_files';

    if (isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    #endregion
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';