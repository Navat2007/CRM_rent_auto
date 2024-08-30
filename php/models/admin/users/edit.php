<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$ID = htmlspecialchars($_POST["id"]);
$user = $authorization[1];
$active = htmlspecialchars($_POST["active"]) === "true" ? 1 : 0;
$actsOnBasis = htmlspecialchars($_POST["actsOnBasis"]);
$birthday = htmlspecialchars($_POST["birthday"]);
$companyId = htmlspecialchars($_POST["companyId"]);
$email = htmlspecialchars($_POST["email"]);
$firstName = htmlspecialchars($_POST["firstName"]);
$gender = htmlspecialchars($_POST["gender"]);
$hireDate = htmlspecialchars($_POST["hireDate"]);
$inn = htmlspecialchars($_POST["inn"]);
$lastName = htmlspecialchars($_POST["lastName"]);
$password = htmlspecialchars($_POST["password"]);
$patronym = htmlspecialchars($_POST["patronym"]);
$phone = htmlspecialchars($_POST["phone"]);
$position = htmlspecialchars($_POST["position"]);
$rate = htmlspecialchars($_POST["rate"]);
$snils = htmlspecialchars($_POST["snils"]);
$fullName = $lastName . ' ' . $firstName;

if(!empty($patronym))
{
    $fullName .= ' '. $patronym;
}

$companies = $_POST["companies"] ?? array();

$error = 0;
$error_text = "";
$sqls = array();
$params = null;

$sql = "SELECT * FROM users WHERE id = '$ID'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$admin_row =pg_fetch_object($result);

if($admin_row->email != $email){
    $sql = "SELECT * FROM users WHERE email = '$email' AND archive = 0";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Такой email уже существует";
    }
}

if((int)$authorization[1] !== (int)$ID && (int)$ID === 1){
    $error = 1;
    $error_text = "Данного администратора нельзя редактировать!";
}

if($error === 0){
    $sql = "UPDATE users SET email = '$email', status = '$active', last_user_id = '$user' WHERE id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    if(isset($_POST["password"]) && trim($_POST["password"]) != "")
    {
        $pwd = trim(htmlspecialchars($_POST["password"]));
        $new_password = password_hash($pwd, PASSWORD_DEFAULT);
        $current_password = $admin_row->password;

        if(password_verify($pwd, $current_password))
        {
            $error = "1";
            $error_text = "Пароль совпадает с текущим";
        }
        else
        {
            $update_query = "UPDATE users SET password = '$new_password', password_change_date = NOW() WHERE id = '$ID'";
            $sqls[] = $update_query;
            pg_query($conn, $update_query);
        }

    }

    $sql = "SELECT * FROM users_info WHERE user_id = '$ID'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $sql = "
            UPDATE 
                users_info 
            SET
                user_id = '$ID', 
                user_type = '$position', 
                full_name = '$fullName',
                birth_date = '$birthday',
                gender = '$gender',
                first_name = '$firstName',
                second_name = '$lastName',
                middle_name = '$patronym',
                phone = '$phone',
                hire_date = '$hireDate',
                acts_on_basis = '$actsOnBasis',
                rate = '$rate',
                inn = '$inn',
                snils = '$snils'
            WHERE 
                user_id = '$ID'";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
    }
    else{
        $sql = "INSERT INTO users_info (
                user_id, 
                user_type, 
                full_name,
                birth_date,
                gender,
                first_name,
                second_name,
                middle_name,
                phone,
                hire_date,
                acts_on_basis,
                rate,
                inn,
                snils
            ) 
            VALUES (
                '$ID', 
                '$position', 
                '$fullName',
                '$birthday',
                '$gender',
                '$firstName',
                '$lastName',
                '$patronym',
                '$phone',
                '$hireDate',
                '$actsOnBasis',
                '$rate',
                '$inn',
                '$snils'
            )";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
    }
}

pg_free_result($result);

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';