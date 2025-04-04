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
        users.id, users.email, users.status, users.archive,
        ui.first_name, ui.second_name, ui.middle_name,
        ui.full_name, ui.birth_date, ui.phone, ui.gender, ui.user_photo_avatar as avatar,
        up.fact_address, up.registration_address, up.issued_by_who, up.issued_date, up.series_number
    FROM 
        users as users
    LEFT JOIN 
        users_company as uc on users.id = uc.user_id
    LEFT JOIN 
        users_passport as up on users.id = up.user_id
    LEFT JOIN 
        users_info as ui on users.id = ui.user_id
    WHERE 
        users.id != 1";

if($company_id != 0){
    $sql .= " AND uc.company_id = '{$company_id}'";
}

$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $params[] = (object)[
            'id' => (int)$row->id,
            'avatar' => $row->avatar == null ? "" : ("https://" . $_SERVER['HTTP_HOST'] . $row->avatar),
            'full_name' => $row->full_name == null ? "" : htmlspecialchars_decode($row->full_name),
            'first_name' => $row->first_name == null ? "" : htmlspecialchars_decode($row->first_name),
            'second_name' => $row->second_name == null ? "" : htmlspecialchars_decode($row->second_name),
            'middle_name' => $row->middle_name == null ? "" : htmlspecialchars_decode($row->middle_name),
            'birth_date' => $row->birth_date,
            'email' => htmlspecialchars_decode($row->email),
            'phone' => htmlspecialchars_decode($row->phone),
            'status' => (int)$row->archive == 1 ? "В архиве" : ((int)$row->status == 1 ? "Активен" : "Отключен"),
            'gender' => (int)$row->gender == 0 ? "Мужской" : "Женский",
            'fact_address' => $row->fact_address == null ? "" : htmlspecialchars_decode($row->fact_address),
            'registration_address' => $row->registration_address == null ? "" : htmlspecialchars_decode($row->registration_address),
            'issued_by_who' => $row->issued_by_who == null ? "" : htmlspecialchars_decode($row->issued_by_who),
            'series_number' => $row->issued_by_who == null ? "" : htmlspecialchars_decode($row->series_number),
            'issued_date' => $row->issued_date == null ? "" : $row->issued_date,
        ];
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';