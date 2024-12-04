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
    $sql = "
        SELECT 
            t1.*, t2.firing_date as firing_date, 
            t2.full_name as user_full_name, t2.birth_date as user_birth_date,
            t2.first_name as user_first_name, t2.second_name as user_second_name,
            t2.middle_name as user_middle_name, t2.user_type,
            t4.id as company_id, t4.name as company_name,
            t5.access_directory, t5.access_employers, t5.access_clients, t5.access_auto, t5.access_booking
        FROM 
            users as t1
        LEFT JOIN 
            users_info as t2 on t2.user_id = t1.id
        LEFT JOIN 
            users_company as t3 on t3.user_id = t1.id
        LEFT JOIN 
            company as t4 on t4.id = t3.company_id
        LEFT JOIN 
            users_access as t5 on t1.id = t5.user_id
        WHERE 
            t1.email = '$login' AND t1.archive = 0";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $params->sql = $sql;

    if (pg_num_rows($result) > 0) {
        $row = pg_fetch_object($result);

        if ((int)$row->status == 0 || $row->firing_date != null) {
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
        'company_id' => (int)$row->company_id,
        'company_name' => $row->company_name != null ? htmlspecialchars_decode($row->company_name) : '',
        'user_type' => (int)$row->user_type,
        'user_full_name' => $row->user_full_name != null ? htmlspecialchars_decode($row->user_full_name) : '',
        'user_first_name' => $row->user_first_name != null ? htmlspecialchars_decode($row->user_first_name) : '',
        'user_second_name' => $row->user_second_name != null ? htmlspecialchars_decode($row->user_second_name) : '',
        'user_middle_name' => $row->user_middle_name != null ? htmlspecialchars_decode($row->user_middle_name) : '',
        'user_birth_date' => $row->user_birth_date,
        'token' => $token,
        'token_created_at' => new DateTime(),
        'access' => (object)[
            'directory' => (int)$row->access_directory,
            'employers' => (int)$row->access_employers,
            'clients' => (int)$row->access_clients,
            'auto' => (int)$row->access_auto,
            'booking' => (int)$row->access_booking,
        ]
    ];
}