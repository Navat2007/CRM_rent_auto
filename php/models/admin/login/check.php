<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';

$login = htmlspecialchars($_POST["login"]);
$password = htmlspecialchars($_POST["password"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = (object)[];

if (strlen($login) > 72) {
    $error = 1;
    $error_text = "Ошибка, логин или пароль введен неверно";
    die();
}
if (strlen($password) > 72) {
    $error = 1;
    $error_text = "Ошибка, логин или пароль введен неверно";
    die();
}

if (!empty($login) && !empty($password)) {
    $sql = "SELECT * FROM users WHERE email = '$login'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $params->sql = $sql;

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result);

        if ((int)$row->status == 1) {
            $error = 1;
            $error_text = "Ошибка, данный пользователь не может войти в систему";
        }
        else if (password_verify($password, $row->password)) {
            $params = get_all_info($row, $conn);
        } else {
            $error = 1;
            $error_text = "Ошибка, логин или пароль введен неверно";
        }
    }
    else {
        $error = 1;
        $error_text = "Ошибка, логин или пароль введен неверно";
    }

    pg_free_result($result);
} else {
    $error = 1;
    $error_text = "Ошибка, логин или пароль введен неверно";
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';

function gen_token(): string
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

function get_all_info($row, $conn): object
{
    $token = gen_token();

    $sql = "UPDATE users SET token = '$token', token_created_at = now() WHERE id = '$row->id'";
    pg_query($conn, $sql);

    return (object)[
        'id' => (int)$row->id,
        'email' => $row->email,
        'token' => $token,
        'token_created_at' => new DateTime(),
    ];
}