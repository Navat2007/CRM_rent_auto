<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$email = htmlspecialchars($_POST["email"]);
$password = htmlspecialchars($_POST["password"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 1 : 0;
$companies = $_POST["companies"];

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

$sql = "SELECT * FROM users WHERE email = '$email' AND archive = 0";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $error = 1;
    $error_text = "Такой email уже существует";
}

if($error === 0){
    $new_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, password) VALUES ('$email', '$new_password') RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    if(is_array($companies) && count($companies) > 0){
        foreach($companies as $company){
            $company_id = $company['id'];
            $sql = "INSERT INTO users_company (user_id, company_id) VALUES ($lastID, $company_id)";
            $sqls[] = $sql;
            $result = pg_query($conn, $sql);
        }
    }

    $params[] = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';