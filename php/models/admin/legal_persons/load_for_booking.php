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
        *
    FROM 
        legal_persons 
    WHERE 
        full_name LIKE '%{$search}%'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $row->id = (int)$row->id;
        $row->is_lessor = (int)$row->is_lessor;
        $row->full_name = htmlspecialchars_decode($row->full_name);
        $row->archive = (int)$row->archive == 0 ? 'Активен' : 'В архиве';
        $row->status = (int)$row->archive == 1 ? 'В архиве' : ((int)$row->status == 1 ? 'Активен' : 'Отключен');

        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';