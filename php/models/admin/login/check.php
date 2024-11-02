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

if (isset($login) && isset($password)) {
    $sql = "SELECT * FROM accounts WHERE archive = '0' AND (email = '$login' OR login = '$login')";
    $sqls[] = $sql;
    $result = mysqli_query($conn, $sql);
    $params->sql = $sql;

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_object($result);

        if (password_verify($password, $row->pwd)) {
            $params = get_all_info($row, $conn);
        } else {
            $error = 1;
            $error_text = "Ошибка, логин или пароль введен неверно";
        }
    } else {
        $key = get_key();
        $eo_id = check_ekis($login, $password, $key);

        if ($eo_id > 0) {
            $sql = "SELECT * FROM accounts WHERE ekis = '$eo_id' AND archive = '0'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_object($result);
                $params = get_all_info($row, $conn);
            } else {
                $data = get_ekis_data($key, $eo_id);

                $org_name = $data->full_name;
                $org_short_name = $data->short_name;
                $fio = $data->director;
                $email = $data->email;
                $phone = $data->public_phone;
                $active = 1;
                $msrd = (int)$data->mrsd;
                $address = $data->legal_address;

                $new_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "SELECT * FROM schools WHERE ekis = '$eo_id' AND org_name = '$org_name'";
                $result = mysqli_query($conn, $sql);
                $schoolID = 0;

                if (mysqli_num_rows($result) > 0) {
                    $schoolID = mysqli_fetch_object($result)->ID;
                } else {
                    $sql = "INSERT INTO schools (org_name, org_short_name, fio, phone, dir_phone, dir_email, active, number, msrd, ekis, meshID, sport_school, address, okrug, okrug_short, raion, archive, hsk_score) 
                VALUES ('$org_name', '$org_short_name', '$fio', '$phone', '', '', '$active', '', '$msrd', '$eo_id', '', '0', '$address', '', '', '', '', '0')";
                    mysqli_query($conn, $sql);
                    $schoolID = mysqli_insert_id($conn);
                }

                $sql = "INSERT INTO accounts (login, email, pwd, org_name, org_short_name, fio, phone, active, ekis, msrd, address, schoolID) VALUES ('$login', '$email', '$new_password', '$org_name', '$org_short_name', '$fio', '$phone', '$active', '$eo_id', '$msrd', '$address', '$schoolID')";
                mysqli_query($conn, $sql);
                $lastID = mysqli_insert_id($conn);

                $sql = "SELECT * FROM accounts WHERE ID = '$lastID'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_object($result);

                $params = get_all_info($row, $conn);
            }
        } else {
            $error = 1;
            $error_text .= "Ошибка, логин или пароль введен неверно";
        }
    }
} else {
    $error = 1;
    $error_text = "Ошибка, логин или пароль введен неверно";
}

$content = (object)[
    'input_params' => (object)[
        'login' => $login,
    ],
    'error' => $error,
    'error_text' => $error_text,
    'sql' => $sqls,
    'params' => $params,
];
echo json_encode($content);

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

    $sql = "UPDATE accounts SET token = '$token' WHERE ID = '$row->ID'";
    mysqli_query($conn, $sql);

    $roleTitle = "";

    switch ($row->role) {
        case "school":
            $roleTitle = "Представитель школы";
            break;
        case "admin":
            $roleTitle = "Администратор";
            break;
        case "superadmin":
            $roleTitle = "Главный администратор";
            break;
    }

    $school = null;
    $sql = "SELECT * FROM schools WHERE ID = '$row->schoolID'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $school = mysqli_fetch_object($result);

        foreach ($school as $key => $value) {
            $school->$key = htmlspecialchars_decode($value);
        }
    }

    return (object)[
        'id' => (int)$row->ID,
        'login' => $row->login,
        'email' => $row->email,
        'phone' => $row->phone,
        'photo' => $row->photo,
        'fio' => $row->fio,
        'create_time' => $row->create_time,
        'role' => $row->role,
        'role_title' => $roleTitle,
        'org_name' => $row->org_name,
        'org_short_name' => $row->org_short_name,
        'ekis' => $row->ekis,
        'address' => $row->address,
        'mrsd' => get_mrsd($conn, $row->ID, $row->msrd),
        'roles' => get_roles($conn, $row->ID, $row->role),
        'schoolID' => (int)$row->schoolID,
        'school' => $school,
        'token' => $token,
        'token_created_at' => new DateTime(),
    ];
}

function get_key()
{
    $log = 'patriot';
    $pass = '0ddB5K_7';

    $url = "https://api-st.educom.ru/v1/auth/createSession";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $log . ":" . $pass);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($res);
    return $result->id;
}

function check_ekis($login, $password, $key)
{
    global $params;

    $url = "https://api-st.educom.ru/v1/users/auth";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_USERPWD, $login . ":" . $password);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-TOKEN: ' . $key,
        'Content-Type: application/json'));
    $res = curl_exec($ch);
    curl_close($ch);
    header('Content-Type: application/json');
    $result = json_decode($res);

    $params->ekis = $result;

    $eo_id = $result->data->eo_id;

    if (count($result->errors) > 0)
        return 0;
    else
        return $eo_id;
}

function get_ekis_data($key, $eo_id)
{
    $url = "https://api-st.educom.ru/v1/eduorg";
    $body['filters'][] = array('arhiv' => array("true" => '0'), 'status_id' => array("true" => '1'), 'eo_id' => array("true" => $eo_id));
    //$body['top'] = 100;
    $body['skip'] = 0;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-API-TOKEN: ' . $key,
        'Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $res = curl_exec($ch);
    curl_close($ch);
    header('Content-Type: application/json');
    $result = json_decode($res);

    return $result->data[0];
}

function get_mrsd($conn, $adminID, $msrd)
{
    $sql = "SELECT * FROM admin_msrd WHERE adminID = '$adminID'";
    $result = mysqli_query($conn, $sql);

    $mrsd = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_object($result)) {
            $mrsd[] = (int)$row->msrd;
        }
    } else
        $mrsd[] = $msrd;

    return $mrsd;
}

function get_roles($conn, $userID, $role)
{
    $sql1 = "
    SELECT 
        ar.*, ard.title as roleTitle 
    FROM 
        admin_role as ar
    INNER JOIN 
        admin_role_dic as ard ON ar.roleID = ard.ID
    WHERE
        ar.userID = '$userID'";
    $result1 = mysqli_query($conn, $sql1);

    $roles = array();

    $roles[] = (object)[
        'ID' => $role == "superadmin" ? 1 : 0,
        'title' => $role == "superadmin" ? "Главный администратор" : "Администратор",
    ];

    if (mysqli_num_rows($result1) > 0) {
        while ($row1 = mysqli_fetch_object($result1)) {
            $roles[] = (object)[
                'ID' => (int)$row1->ID,
                'title' => $row1->roleTitle,
            ];
        }
    }

    return $roles;
}