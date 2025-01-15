<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$user_id = htmlspecialchars($_POST["user_id"]);
$scoring = htmlspecialchars($_POST["scoring"]);
$url = htmlspecialchars($_POST["url"]);
$f = htmlspecialchars($_POST["f"]);
$i = htmlspecialchars($_POST["i"]);
$o = htmlspecialchars($_POST["o"]);
$birthday = htmlspecialchars($_POST["birthday"]);

if($error === 0){
    $sql = "
        INSERT INTO himera_results (user_id, url, scoring_overall_indicator, lastname, firstname, middlename, birthday, last_user_id) 
        VALUES ('$user_id', '$url', '$scoring', '$f', '$i', '$o', '$birthday', $user) 
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