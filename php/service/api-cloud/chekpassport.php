<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

function get_result($url): bool|string
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_TIMEOUT,120); // Таймаут необходим, поскольку 30 секунд может не хватить и вы не получите ответ
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

$user = $authorization[1];
$user_id = isset($_POST["user_id"]) ? (int)htmlspecialchars($_POST["user_id"]) : die("Не передан user ID");
$seria = isset($_POST["seria"]) ? htmlspecialchars($_POST["seria"]) : die("Не передана серия");
$nomer = isset($_POST["nomer"]) ? htmlspecialchars($_POST["nomer"]) : die("Не передан номер");

$token = "b6d4c26c1ebecf3b837efe56637d01f4";
$type = 'chekpassport';
$typeDB = 'mvd_chekpassport';
$data = [
    'type' => $type,
    'token' => $token,
    'seria' => $seria,
    'nomer' => $nomer,
];
$url = 'https://api-cloud.ru/api/mvd.php?' . http_build_query($data);

$result = get_result($url);
$paramsJSON = json_decode($result, true);

if(isset($paramsJSON['status']) && (int)$paramsJSON['status'] == 404){
    $error = 1;
    $error_text = $paramsJSON['message'];
}

if(isset($paramsJSON['error']) && (int)$paramsJSON['error'] != 0){
    $error = 1;
    $error_text = $paramsJSON['message'];
}

if($error === 0){
    $sql = "
    INSERT INTO 
        api_cloud_results (user_id, last_user_id, request_type, request_parameters, response) 
    VALUES 
        ('$user_id', '$user', '$typeDB', 'Серия: $seria, Номер: $nomer', '$result')";
    $sqls[] = $sql;
    pg_query($conn, $sql);
}

$paramsJSON['url'] = $url;
$paramsJSON['request'] = "Серия: $seria, Номер: $nomer";
$params = $paramsJSON;

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';