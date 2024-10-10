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
        ui.full_name, ui.birth_date, ui.inn, ui.phone,
        dat.name as advertising_type
    FROM 
        users as users
    LEFT JOIN 
        users_company as uc on users.id = uc.user_id
    LEFT JOIN 
        users_info as ui on users.id = ui.user_id
    LEFT JOIN 
        directory_advertising_types as dat on dat.id = ui.directory_advertising_type_id
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
            'full_name' => $row->full_name == null ? "Не заполнено" : htmlspecialchars_decode($row->full_name),
            'advertising_type' => $row->advertising_type == null ? "Не заполнено" : htmlspecialchars_decode($row->advertising_type),
            'birth_date' => $row->birth_date,
            'inn' => $row->inn,
            'email' => htmlspecialchars_decode($row->email),
            'phone' => htmlspecialchars_decode($row->phone),
            'status' => (int)$row->archive == 1 ? "В архиве" : ((int)$row->status == 1 ? "Активен" : "Отключен"),
        ];
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';