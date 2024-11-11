<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$base64 = htmlspecialchars($_POST["base64"]);
$mime = htmlspecialchars($_POST["mime"]);
$model = htmlspecialchars($_POST["model"]);
$iamToken = htmlspecialchars($_POST["iamToken"]);
$folderID = htmlspecialchars($_POST["folderID"]);
$url = "https://ocr.api.cloud.yandex.net/ocr/v1/recognizeText";

$message = [
    "mimeType" => $mime,
    "languageCodes" => ["*"],
    "model" => $model,
    "content" => $base64,
];

$content = json_encode($message);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $iamToken,
    'x-folder-id: ' . $folderID,
    'x-data-logging-enabled: true',
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

$response = curl_exec($curl);
$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

$error_msg = "";

if (curl_errno($curl)) {
    $error_msg = curl_error($curl);
}

$params = json_decode($body);

curl_close($curl);

$content = (object)[
    'error' => $error_msg,
    'response' => $response,
    'params' => $params,
    'entry' => $message,
    'access' => (object)[
        'iamToken' => $iamToken,
        'folderID' => $folderID
    ],
];

echo json_encode($content);