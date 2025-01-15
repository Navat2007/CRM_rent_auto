<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$f = htmlspecialchars($_POST["f"]);
$i = htmlspecialchars($_POST["i"]);
$o = htmlspecialchars($_POST["o"]);
$birthday = explode('.', htmlspecialchars($_POST["birthday"]));

$url = "https://api.himera-search.info/2.0/name_standart";
$content = "key=b29eca2977887cdc9ea101889f96d54b&lastname=$f&firstname=$i&middlename=$o&day=$birthday[0]&month=$birthday[1]&year=$birthday[2]";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
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