<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

#region Variables
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

if (!empty($patronym)) {
    $fullName .= ' ' . $patronym;
}

$companies = $_POST["companies"] ?? array();
$contacts = $_POST['contacts'] ?? array();
$avatar = isset($_POST["avatar"]) ? htmlspecialchars($_POST["avatar"]) : null;
$note1 = htmlspecialchars($_POST["note1"]);
$note2 = htmlspecialchars($_POST["note2"]);
$note3 = htmlspecialchars($_POST["note3"]);
$firingDate = htmlspecialchars($_POST["firingDate"]);

$passport_series_number = htmlspecialchars($_POST["passport_series_number"]);
$passport_department_code = htmlspecialchars($_POST["passport_department_code"]);
$passport_issued_by = htmlspecialchars($_POST["passport_issued_by"]);
$passport_date_of_issue = htmlspecialchars($_POST["passport_date_of_issue"]);
$passport_born_place = htmlspecialchars($_POST["passport_born_place"]);
$passport_registration_address = htmlspecialchars($_POST["passport_registration_address"]);
$passport_fact_address = htmlspecialchars($_POST["passport_fact_address"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = null;
#endregion

#region User valid check
$sql = "SELECT * FROM users WHERE id = '$ID'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$admin_row = pg_fetch_object($result);

if ($admin_row->email != $email) {
    $sql = "SELECT * FROM users WHERE email = '$email' AND archive = 0";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if (pg_num_rows($result) > 0) {
        $error = 1;
        $error_text = "Такой email уже существует";
    }
}

if ((int)$authorization[1] !== (int)$ID && (int)$ID === 1) {
    $error = 1;
    $error_text = "Данного администратора нельзя редактировать!";
}
#endregion

if ($error === 0) {
    #region User
    $sql = "UPDATE users SET email = '$email', status = '$active', last_user_id = '$user' WHERE id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);
    #endregion

    #region Password
    if (isset($_POST["password"]) && trim($_POST["password"]) != "") {
        $pwd = trim(htmlspecialchars($_POST["password"]));
        $new_password = password_hash($pwd, PASSWORD_DEFAULT);
        $current_password = $admin_row->password;

        if (password_verify($pwd, $current_password)) {
            $error = "1";
            $error_text = "Пароль совпадает с текущим";
        } else {
            $update_query = "UPDATE users SET password = '$new_password', password_change_date = NOW() WHERE id = '$ID'";
            $sqls[] = $update_query;
            pg_query($conn, $update_query);
        }

    }
    #endregion

    #region User info
    $sql = "SELECT * FROM users_info WHERE user_id = '$ID'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if (pg_num_rows($result) > 0) {
        $sql = "
            UPDATE 
                users_info 
            SET
                user_id = '$ID', 
                user_type = '$position', 
                full_name = '$fullName',
                birth_date = " . (empty($birthday) ? 'NULL' : "'" . $birthday . "'") . ",
                gender = '$gender',
                first_name = '$firstName',
                second_name = '$lastName',
                middle_name = '$patronym',
                phone = '$phone',
                hire_date = " . (empty($hireDate) ? 'NULL' : "'" . $hireDate . "'") . ",
                firing_date = " . (empty($firingDate) ? 'NULL' : "'" . $firingDate . "'") . ",
                acts_on_basis = '$actsOnBasis',
                rate = '$rate',
                inn = '$inn',
                snils = '$snils',
                user_note_1 = '$note1',
                user_note_2 = '$note2',
                user_note_3 = '$note3'
            WHERE 
                user_id = '$ID'";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
    } else {
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
    #endregion

    #region Avatar
    if (isset($_FILES['avatar'])) {
        $baseDirName = $_SERVER['DOCUMENT_ROOT'] . "/files/users/" . $ID . "/avatar";

        if (!file_exists($baseDirName)) {
            $oldmask = umask(0);
            $mkdir_result = mkdir($baseDirName, 0777, true);
            umask($oldmask);
        }

        if($_FILES['avatar']['error'] != UPLOAD_ERR_OK) {
            $error = 1;
            $error_text = "Ошибка загрузки файла";
        }
        else {
            $temp_name = $_FILES['avatar']['tmp_name'];
            $name = $_FILES['avatar']['name'];
            $file_token = time();
            $path = $baseDirName . "/" . $file_token . "_" . $name;

            @unlink($path);

            $sql = "SELECT * FROM users_info WHERE user_id = '$ID'";
            $sqls[] = $sql;
            $result = pg_query($conn, $sql);
            $row = pg_fetch_object($result);

            $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->user_photo_avatar;
            @unlink($oldpath);

            if (copy($temp_name, $path)) {

                $file_to_DB = "/files/users/" . $ID . "/avatar/" . $file_token . "_" . $name;

                $add_sql = "UPDATE 
                                users_info
                            SET
                                user_photo_avatar = '$file_to_DB'
                            WHERE 
                                user_id = '$ID'";
                pg_query($conn, $add_sql);
                $params = $file_to_DB;
            }
        }
    }
    else if(isset($avatar) && empty($avatar)) {
        $sql = "SELECT * FROM users_info WHERE user_id = '$ID'";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);

        $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->user_photo_avatar;
        @unlink($oldpath);

        $add_sql = "UPDATE 
                        users_info
                    SET
                        user_photo_avatar = ''
                    WHERE 
                        user_id = '$ID'";
        pg_query($conn, $add_sql);
    }
    #endregion

    #region Additional contacts
    $sql = "DELETE FROM users_additional_contacts WHERE user_id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    if (count($contacts) > 0){
        for ($i = 0; $i < count($contacts); $i++) {
            $contact = $contacts[$i]['contact'];
            $name = $contacts[$i]['name'];
            $who = $contacts[$i]['who'];

            $sql = "INSERT INTO users_additional_contacts (
                user_id,
                contact,
                name,
                who
            ) 
            VALUES (
                '$ID',
                '$contact',
                '$name',
                '$who'                 
            )";
            $sqls[] = $sql;
            pg_query($conn, $sql);
        }
    }
    #endregion

    #region Passport
    $sql = "DELETE FROM users_passport WHERE user_id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    $sql = "INSERT INTO users_passport (
                user_id,
                series_number,
                department_code,
                issued_by_who,
                issued_date,
                born_place,
                registration_address,
                fact_address            
            ) 
            VALUES (
                '$ID',
                '$passport_series_number',
                '$passport_department_code',
                '$passport_issued_by',                 
                '$passport_date_of_issue',                 
                '$passport_born_place',                 
                '$passport_registration_address',                 
                '$passport_fact_address'              
            )";
    $sqls[] = $sql;
    pg_query($conn, $sql);
    #endregion
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';