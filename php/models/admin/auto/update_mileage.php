<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$ID = htmlspecialchars($_POST["id"]);
$mileage = htmlspecialchars($_POST["mileage"]);

$sql = "
    UPDATE 
        car 
    SET 
        mileage = '$mileage',
        last_user_id = '$user'
    WHERE id = '$ID'
    ";
$sqls[] = $sql;
pg_query($conn, $sql);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';