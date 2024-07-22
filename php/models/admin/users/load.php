<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$search = isset($_POST["search"]) ? htmlspecialchars($_POST["search"]) : '';
$company_id = isset($_POST["company_id"]) ? (int)htmlspecialchars($_POST["company_id"]) : 0;

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        users.* 
    FROM 
        users as users
    LEFT JOIN 
        users_company as uc on users.id = uc.user_id
    WHERE 
        users.archive = 0 AND users.email LIKE '%{$search}%'";

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
            'email' => htmlspecialchars_decode($row->email),
            'status' => (int)$row->status == 0 ? "Активен" : "Отключен",
        ];
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';