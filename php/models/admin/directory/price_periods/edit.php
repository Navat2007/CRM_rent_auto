<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$id = htmlspecialchars($_POST["id"]);
$name = htmlspecialchars($_POST["name"]);
$days_from = htmlspecialchars($_POST["days_from"]);
$days_until = $_POST["days_until"] == '' ? NULL : htmlspecialchars($_POST["days_until"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 0 : 1;

$sql = "SELECT * FROM directory_price_periods WHERE id = '$id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$row = pg_fetch_object($result);

if($row->name != $name)
{
    $sql = "SELECT * FROM directory_price_periods WHERE name = '$name'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Такой ценовой период уже существует";
    }
}

if($error === 0){
    $set = "
        name = '$name', 
        days_from = '$days_from',
        archive = '$active',
        last_user_id = '$user'    
    ";

    if($days_until != NULL)
    {
        $set .= ", days_until = '$days_until'";
    }
    else{
        $set .= ", days_until = NULL";
    }

    $sql = "
    UPDATE 
        directory_price_periods 
    SET 
        $set  
    WHERE id = '$id'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';