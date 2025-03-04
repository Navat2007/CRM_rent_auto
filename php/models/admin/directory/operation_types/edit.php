<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");
$name = htmlspecialchars($_POST["name"]);
$order = htmlspecialchars($_POST["order"]);
$is_income = htmlspecialchars($_POST["is_income"]) === "true" ? 1 : 0;
$used_for = htmlspecialchars($_POST["used_for"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_operation_types WHERE id = '$id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$row = pg_fetch_object($result);

if($row->name != $name)
{
    $sql = "SELECT * FROM directory_operation_types WHERE name = '$name'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Запись с таким названием уже существует";
    }
}

if($error === 0){
    $sql = "
    UPDATE 
        directory_operation_types 
    SET 
        name = '$name', 
        \"order\" = '$order', 
        is_income = '$is_income',
        used_for = '$used_for',
        archive = '$active', 
        last_user_id = '$user'
    WHERE 
        id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';