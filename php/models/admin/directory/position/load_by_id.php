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

$sql = "SELECT * FROM directory_position WHERE id = $id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $row = pg_fetch_object($result);
    $params = (object)[
        'id' => (int)$row->id,
        'name' => htmlspecialchars_decode($row->name),
        'active' => (int)$row->archive == 0,
    ];

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';