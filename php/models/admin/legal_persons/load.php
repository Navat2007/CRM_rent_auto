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
        id, full_name, is_lessor, ogrn, inn, status, archive, updated_at
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
        $params[] = (object)[
            'id' => (int)$row->id,
            'full_name' => htmlspecialchars_decode($row->full_name),
            'is_lessor' => (int)$row->is_lessor == 1 ? 'Да' : '',
            'ogrn' => htmlspecialchars_decode($row->ogrn),
            'inn' => htmlspecialchars_decode($row->inn),
            'status' => (int)$row->archive == 1 ? 'В архиве' : ((int)$row->status == 1 ? 'Активен' : 'Отключен'),
            'archive' => (int)$row->archive,
            'updated_at' => $row->updated_at,
        ];
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';