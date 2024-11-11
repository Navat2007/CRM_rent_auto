<?php
$clientToken = htmlspecialchars($_POST['clientToken']);
$refreshToken = htmlspecialchars($_POST['refreshToken']);
$serviceToken = htmlspecialchars($_POST['serviceToken']);
$url = htmlspecialchars($_POST['url']);

$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => array(
        'ekis-per-token:  ' . $clientToken,
        'ekis-srv-token:  ' . $serviceToken,
    ),
));

$response = curl_exec($myCurl);
curl_close($myCurl);


$content = (object)[

    'input_params' => (object)[
        'POST' => $_POST,
        'GET' => $_GET,
        'FILES' => $_FILES,
    ],
    'response' => $response

];

echo json_encode($content);
