<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

ini_set('display_errors',1);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';
require 'calculate.php';

$ID = htmlspecialchars($_POST["id"]);
$user = $authorization[1];
$companyId = htmlspecialchars($_POST["companyId"]);
$booking_id = htmlspecialchars($_POST["booking_id"]);
$date = htmlspecialchars($_POST["date"]);
$directory_operation_types_id = htmlspecialchars($_POST["directory_operation_types_id"]);
$directory_payment_types_id = htmlspecialchars($_POST["directory_payment_types_id"]);
$period_from = htmlspecialchars($_POST["period_from"]);
$period_to = htmlspecialchars($_POST["period_to"]);
$directory_services_id = htmlspecialchars($_POST["directory_services_id"]);
$directory_services_name = htmlspecialchars($_POST["directory_services_name"]);
$tariff = htmlspecialchars($_POST["tariff"]);
$quantity = htmlspecialchars($_POST["quantity"]);
$accrued = htmlspecialchars($_POST["accrued"]);
$paid = htmlspecialchars($_POST["paid"]);
$is_income = htmlspecialchars($_POST["is_income"]) === "true" ? 1 : 0;

if($error === 0){
    $sql = "
    UPDATE 
        booking_accruals_and_payments 
    SET 
        operation_datetime = '$date',
        directory_operation_types_id = '$directory_operation_types_id',
        directory_payment_types_id = '$directory_payment_types_id',
        period_from = " . (empty($period_from) ? 'NULL' : "'" . $period_from . "'") . ", 
        period_to = " . (empty($period_to) ? 'NULL' : "'" . $period_to . "'") . ",
        directory_services_id = '$directory_services_id',
        directory_services_name = '$directory_services_name',
        tariff = '$tariff',
        quantity = '$quantity',
        accrued = '$accrued',
        paid = '$paid',
        is_income = '$is_income',
        last_user_id = '$user'
    WHERE 
        id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    (new OperationsCalculate($conn))->calculate($booking_id);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';