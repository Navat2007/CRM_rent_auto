<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$address_string = htmlspecialchars($_POST["address_string"]);

$token = "5e41e2cf830f27fb760384c894cfe9a9c7fef0d7";
$url = "https://dadata.ru/api/suggest/address/";
$message = [
    "query" => $address_string
];

$content = json_encode($message);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/application/json',
    'Authorization: Token ' . $token,
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);
$params = json_decode($json_response);
curl_close($curl);

$content = (object)[
    'params' => $params,
    'json_response' => $json_response,
    'message' => $content,
];

echo json_encode($content);