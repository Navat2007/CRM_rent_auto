<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

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

    $sql = "INSERT INTO users (email, password, status, is_employee, last_user_id) VALUES ('$email', '$new_password', '$active', 'true', $user) RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $sql = "INSERT INTO users_company (user_id, company_id) VALUES ('$lastID', '$companyId')";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

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
                '$lastID', 
                '$position', 
                '$fullName',
                " . (empty($birthday) ? 'NULL' : "'" . $birthday . "'") . ",
                '$gender',
                '$firstName',
                '$lastName',
                '$patronym',
                '$phone',
                " . (empty($hireDate) ? 'NULL' : "'" . $hireDate . "'") . ",
                '$actsOnBasis',
                '$rate',
                '$inn',
                '$snils'
            )";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(is_array($companies) && count($companies) > 0){
        foreach($companies as $company){
            $company_id = $company['id'];
            $sql = "INSERT INTO users_company (user_id, company_id) VALUES ($lastID, $company_id)";
            $sqls[] = $sql;
            $result = pg_query($conn, $sql);
        }
    }

    $params = (object)[
        'id' => $lastID,
    ];

    if($result)
        pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';