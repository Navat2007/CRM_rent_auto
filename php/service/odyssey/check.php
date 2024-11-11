<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$f = htmlspecialchars($_POST["f"]);
$i = htmlspecialchars($_POST["i"]);
$o = htmlspecialchars($_POST["o"]);
$birthday = htmlspecialchars($_POST["birthday"]);

$url = "https://api.odyssey-search.info/v2.0/name_standart";
$content = "key=8fe1e037f8c61e5f4588930807b3a7af&lastname=$f&firstname=$i&middlename=$o&birthday=$birthday";

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

$testAnswer = json_decode(file_get_contents("test.txt"));

$content = (object)[
    'params' => $params,
    'json_response' => $json_response,
    'message' => $content,
    'test' => $testAnswer,
];

echo json_encode($content);