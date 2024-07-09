<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$login = htmlspecialchars($_POST["login"]);
$password = htmlspecialchars($_POST["password"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

$sql = "SELECT * FROM admins WHERE login = '$login' AND archive = 0";
$sqls[] = $sql;
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0)
{
    $error = 1;
    $error_text = "Такой логин уже существует";
}

if($error === 0){
    $new_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (login, password) VALUES ('$login', '$new_password')";
    $sqls[] = $sql;
    mysqli_query($conn, $sql);
    $lastID = mysqli_insert_id($conn);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';