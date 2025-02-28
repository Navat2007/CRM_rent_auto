<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$ID = htmlspecialchars($_POST["id"]);
$company_id = isset($_POST["company_id"]) ? (int)htmlspecialchars($_POST["company_id"]) : 0;

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        baap.*, dot.name as directory_operation_types_name,
        dpt.name as directory_payment_types_name
    FROM 
        booking_accruals_and_payments as baap
    LEFT JOIN directory_operation_types as dot ON baap.directory_operation_types_id = dot.id
    LEFT JOIN directory_payment_types as dpt ON baap.directory_payment_types_id = dpt.id    
    WHERE baap.booking_id = '$ID' ORDER BY baap.operation_datetime DESC";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';