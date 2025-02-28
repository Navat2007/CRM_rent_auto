<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

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

if ($error === 0) {
    $sql = "
        INSERT INTO booking_accruals_and_payments 
        (
            booking_id,
            operation_datetime,
            directory_operation_types_id,
            directory_payment_types_id,
            period_from,
            period_to,
            directory_services_id,
            directory_services_name,
            tariff,
            quantity,
            accrued,
            paid,
            is_income,
            create_user_id,
            last_user_id
        ) 
        VALUES (
                '$booking_id', 
                '$date', 
                '$directory_operation_types_id', 
                '$directory_payment_types_id', 
                " . (empty($period_from) ? 'NULL' : "'" . $period_from . "'") . ", 
                " . (empty($period_to) ? 'NULL' : "'" . $period_to . "'") . ", 
                '$directory_services_id', 
                '$directory_services_name',
                '$tariff',
                '$quantity',
                '$accrued',
                '$paid',
                '$is_income',
                $user,
                $user) 
        RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params[] = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';