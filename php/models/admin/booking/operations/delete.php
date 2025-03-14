<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';
require 'calculate.php';

$ID = htmlspecialchars($_POST["id"]);
$user = $authorization[1];

if((int)$user != 1){
    die();
}

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

$sql = "
        SELECT 
            booking_id
        FROM 
            booking_accruals_and_payments 
        WHERE 
            id = '$ID'";
$result = pg_query($this->conn, $sql);
$row = pg_fetch_object($result);

$sql = "DELETE FROM booking_accruals_and_payments WHERE id = '$ID'";
pg_query($conn, $sql);

(new OperationsCalculate($conn))->calculate($row->booking_id);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';