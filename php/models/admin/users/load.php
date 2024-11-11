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
        users.*, ui.full_name, ui.firing_date, dp.name as position_name
    FROM 
        users as users
    LEFT JOIN 
        users_company as uc on users.id = uc.user_id
    LEFT JOIN 
        users_info as ui on users.id = ui.user_id
    LEFT JOIN 
        directory_position as dp on dp.id = ui.user_type
    WHERE 
        users.is_employee = true";

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
            'position' => (int)$row->id == 1 ? 'Разработчик' : ($row->position_name == null ? "Не найдена" : htmlspecialchars_decode($row->position_name)),
            'firing_date' => $row->firing_date,
            'email' => htmlspecialchars_decode($row->email),
            'status' => (int)$row->archive == 1 ? "В архиве" : ((int)$row->status == 1 ? "Активен" : "Отключен"),
        ];
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';