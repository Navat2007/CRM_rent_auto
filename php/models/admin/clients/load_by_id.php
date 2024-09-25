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
        users.email, users.status, users.archive,
        uc.company_id, ui.full_name, ui.first_name, ui.second_name, ui.middle_name,
        ui.birth_date, ui.phone, ui.legal_person_id, ui.directory_advertising_type_id,
        ui.gender, ui.inn, ui.snils, ui.client_note_1, ui.client_note_2, ui.client_note_3,
        ui.user_photo_avatar, ui.bank_card, ui.note, ui.negative, ui.debtor, ui.verification_failure,
        up.born_place as passport_born_place, up.fact_address as passport_fact_address, up.department_code as passport_department_code, 
        up.series_number as passport_series_number, up.issued_by_who as passport_issued_by,
        up.issued_date as passport_date_of_issue, up.registration_address as passport_registration_address,
        udl.series_number as dl_series_number, udl.issued_date as dl_issued_date,
        udl.issued_by_who as dl_issued_by_who, udl.expire_date as dl_expire_date
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

    #region Additional contacts
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
    #endregion

    #region Passport files
    $sql = "
    SELECT 
        *
    FROM 
        users_passport_files
    WHERE 
        user_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($passport_files_row = pg_fetch_object($result))
        {
            $row->passport_files[] = $passport_files_row;
        }

        pg_free_result($result);
    }
    #endregion

    #region Driver license files
    $sql = "
    SELECT 
        *
    FROM 
        users_driving_license_files
    WHERE 
        user_id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while($driving_license_files_row = pg_fetch_object($result))
        {
            $row->dl_files[] = $driving_license_files_row;
        }

        pg_free_result($result);
    }
    #endregion

    #region Other files
    $sql = "
    SELECT 
        *
    FROM 
        users_other_files
    WHERE 
        user_id = '$id'";
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