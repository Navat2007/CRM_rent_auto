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
        users.email, users.status,
        uc.company_id, ui.full_name, ui.first_name, ui.second_name, ui.middle_name,
        ui.rate, ui.acts_on_basis, ui.birth_date, ui.hire_date, ui.phone,
        ui.gender, ui.user_type, ui.inn, ui.snils, ui.user_note_1, ui.user_note_2, ui.user_note_3,
        ui.user_photo_avatar,
        up.born_place, up.fact_address, up.department_code, up.series_number, up.issued_by_who,
        up.issued_date, up.registration_address,
        udl.series_number as dl_series_number, udl.issued_date as dl_issued_date,
        udl.issued_by_who as dl_issued_by_who, udl.expite_date as dl_expite_date
    FROM 
        users as users
    LEFT JOIN 
        users_company as uc ON users.id = uc.user_id
    LEFT JOIN
        users_info as ui ON users.id = ui.user_id
    LEFT JOIN 
        users_passport as up ON users.id = up.user_id
    LEFT JOIN
        users_driving_license as udl ON users.id = udl.user_id
    WHERE 
        users.id = $id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $row = pg_fetch_object($result);
    pg_free_result($result);

    $sql = "
    SELECT 
        *
    FROM 
        users_additional_contacts
    WHERE 
        user_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($contact_row = pg_fetch_object($result))
        {
            $row->contacts[] = $contact_row;
        }

        pg_free_result($result);
    }

    $params = $row;
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';