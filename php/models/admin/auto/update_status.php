<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$ID = htmlspecialchars($_POST["id"]);
$place = htmlspecialchars($_POST["place"]);
$status = htmlspecialchars($_POST["status"]);

$sql = "
    UPDATE 
        car 
    SET 
        $place = '$status',
        last_user_id = '$user'
    WHERE id = '$ID'
    ";
$sqls[] = $sql;
pg_query($conn, $sql);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';