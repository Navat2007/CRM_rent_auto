<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$oauth = "y0_AgAAAAAt2zBlAATuwQAAAAETYQ7dAADHbUZLOvhFU4a729COg6PJMM7DOg";
$url = "https://iam.api.cloud.yandex.net/iam/v1/tokens";

$message = [
    "yandexPassportOauthToken" => $oauth
];

$content = json_encode($message);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($content)
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$json_response = curl_exec($curl);

$params = json_decode($json_response);

curl_close($curl);

$content = (object)[
    'params' => $params,
];

echo json_encode($content);