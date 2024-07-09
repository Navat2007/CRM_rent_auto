<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$search = htmlspecialchars($_POST["search"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "SELECT * FROM users WHERE archive = 0";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $params[] = (object)[
            'id' => (int)$row->id,
            'email' => htmlspecialchars_decode($row->email),
            'status' => (int)$row->active == 1 ? "Активен" : "Отключен",
        ];
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';