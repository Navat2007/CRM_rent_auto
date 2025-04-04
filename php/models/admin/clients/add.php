<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

#region Functions
function SaveFiles($folder_title, $files_title, $db_title): void
{
    global $conn, $ID, $user, $error, $error_text;

    if (isset($_FILES[$files_title])) {
        $baseDirName =  "/files/users/" . $ID . "/" . $folder_title;

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $baseDirName)) {
            $oldmask = umask(0);
            $mkdir_result = mkdir($_SERVER['DOCUMENT_ROOT'] . $baseDirName, 0777, true);
            umask($oldmask);
        }

        foreach($_FILES[$files_title]['error'] as $key => $error)
        {
            if ($error == UPLOAD_ERR_OK)
            {
                $temp_name = $_FILES[$files_title]['tmp_name'][$key];
                $name = $_FILES[$files_title]['name'][$key];
                $file_name = explode('.', $name)[0];
                $file_extension = explode('.', $name)[1];
                $size = $_FILES[$files_title]['size'][$key];
                $file_token = time();
                $path = $_SERVER['DOCUMENT_ROOT'] . $baseDirName . "/" . $file_token . "_" . $name;

                @unlink($path);

                if(copy($temp_name, $path))
                {
                    $file_to_DB = $baseDirName . "/" . $file_token . "_" . $name;

                    $sql = "
                    INSERT INTO $db_title (user_id, file_path, file_name, file_extension, file_size, last_user_id) 
                    VALUES ('$ID', '$file_to_DB', '$file_name', '$file_extension', '$size', '$user')";
                    pg_query($conn, $sql);
                }
            }
            else
            {
                $error = 1;
                $error_text = $error;
            }
        }
    }
}

function CheckFilesToDelete($files, $table): void {
    global $conn, $ID, $user, $error, $error_text;

    foreach ($files as $file) {
        if(isset($file["deleted"]) && (int)$file["deleted"] === 1) {
            $fileID = $file['id'];

            $sql = "
            SELECT 
                *
            FROM 
                $table
            WHERE 
                id = '$fileID'";
            $result = pg_query($conn, $sql);
            $row = pg_fetch_object($result);

            $path = $_SERVER['DOCUMENT_ROOT'] . $row->file_path;
            @unlink($path);

            $sql = "DELETE FROM $table WHERE id = '$fileID' AND user_id = '$ID'";
            pg_query($conn, $sql);
        }
    }
}
#endregion

#region Variables
$user = $authorization[1];
$active = htmlspecialchars($_POST["active"]) === "true" ? 1 : 0;
$birthday = htmlspecialchars($_POST["birthday"]);
$companyId = htmlspecialchars($_POST["companyId"]);
$email = htmlspecialchars($_POST["email"]);
$firstName = htmlspecialchars($_POST["firstName"]);
$gender = htmlspecialchars($_POST["gender"]);
$inn = htmlspecialchars($_POST["inn"]);
$lastName = htmlspecialchars($_POST["lastName"]);
$patronym = htmlspecialchars($_POST["patronym"]);
$phone = htmlspecialchars($_POST["phone"]);
$advertising = htmlspecialchars($_POST["advertising"]);
$legalPerson = htmlspecialchars($_POST["legalPerson"]);
$snils = htmlspecialchars($_POST["snils"]);
$fullName = $lastName . ' ' . $firstName;

if (!empty($patronym)) {
    $fullName .= ' ' . $patronym;
}

$companies = $_POST["companies"] ?? array();
$contacts = $_POST['contacts'] ?? array();
$avatar = isset($_POST["avatar"]) ? htmlspecialchars($_POST["avatar"]) : null;
$bank_card = htmlspecialchars($_POST["bank_card"]);
$note = htmlspecialchars($_POST["note"]);
$note1 = htmlspecialchars($_POST["note1"]);
$note2 = htmlspecialchars($_POST["note2"]);
$note3 = htmlspecialchars($_POST["note3"]);

$negative = htmlspecialchars($_POST["negative"]) === "true" ? 1 : 0;
$debtor = htmlspecialchars($_POST["debtor"]) === "true" ? 1 : 0;
$verification_failure = htmlspecialchars($_POST["verification_failure"]) === "true" ? 1 : 0;

$passport_series_number = htmlspecialchars($_POST["passport_series_number"]);
$passport_department_code = htmlspecialchars($_POST["passport_department_code"]);
$passport_issued_by = htmlspecialchars($_POST["passport_issued_by"]);
$passport_date_of_issue = htmlspecialchars($_POST["passport_date_of_issue"]);
$passport_born_place = htmlspecialchars($_POST["passport_born_place"]);
$passport_registration_address = htmlspecialchars($_POST["passport_registration_address"]);
$passport_fact_address = htmlspecialchars($_POST["passport_fact_address"]);

$dl_series_number = htmlspecialchars($_POST["dl_series_number"]);
$dl_issued_by_who = htmlspecialchars($_POST["dl_issued_by_who"]);
$dl_issued_date = htmlspecialchars($_POST["dl_issued_date"]);
$dl_expire_date = htmlspecialchars($_POST["dl_expire_date"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = null;
#endregion

#region User valid check
if(isset($_POST["email"]) && $email != "")
{
    $sql = "SELECT * FROM users WHERE email = '$email' AND archive = 0";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        $error = 1;
        $error_text = "Такой email уже существует";
    }
}
#endregion

if ($error === 0) {
    #region User
    $sql = "INSERT INTO users (email, status, is_employee, last_user_id) VALUES ('$email', '$active', 'false', $user) RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $ID = $row->id;

    $sql = "INSERT INTO users_company (user_id, company_id) VALUES ('$ID', '$companyId')";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    $params = (object)[
        'id' => $ID,
    ];
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
                full_name = '$fullName',
                bank_card = '$bank_card',
                negative = '$negative',
                debtor = '$debtor',
                verification_failure = '$verification_failure',
                birth_date = " . (empty($birthday) ? 'NULL' : "'" . $birthday . "'") . ",
                gender = '$gender',
                first_name = '$firstName',
                second_name = '$lastName',
                middle_name = '$patronym',
                phone = '$phone',
                directory_advertising_type_id = '$advertising',
                legal_person_id = '$legalPerson',
                inn = '$inn',
                snils = '$snils',
                note = '$note',
                client_note_1 = '$note1',
                client_note_2 = '$note2',
                client_note_3 = '$note3'
            WHERE 
                user_id = '$ID'";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
    } else {
        $sql = "INSERT INTO users_info (
                user_id, 
                bank_card, 
                full_name,
                birth_date,
                gender,
                first_name,
                second_name,
                middle_name,
                phone,
                inn,
                snils,
                directory_advertising_type_id,
                legal_person_id,
                note,
                client_note_1,
                client_note_2,
                client_note_3,
                negative,
                debtor,
                verification_failure
            ) 
            VALUES (
                '$ID', 
                '$bank_card', 
                '$fullName',
                " . (empty($birthday) ? 'NULL' : "'" . $birthday . "'") . ",
                '$gender',
                '$firstName',
                '$lastName',
                '$patronym',
                '$phone',
                '$inn',
                '$snils',
                '$advertising',
                '$legalPerson',
                '$note',
                '$note1',
                '$note2',
                '$note3',
                '$negative',
                '$debtor',
                '$verification_failure'
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

        if($row)
        {
            $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->user_photo_avatar;
            @unlink($oldpath);
        }

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

    if(!empty($passport_series_number)){
        $sql = "INSERT INTO users_passport (
                user_id,
                series_number,
                department_code,
                issued_by_who,
                issued_date,
                born_place,
                registration_address,
                fact_address,
                last_user_id
            ) 
            VALUES (
                '$ID',
                '$passport_series_number',
                '$passport_department_code',
                '$passport_issued_by',                 
                " . (empty($passport_date_of_issue) ? 'NULL' : "'" . $passport_date_of_issue . "'") . ",                 
                '$passport_born_place',                 
                '$passport_registration_address',                 
                '$passport_fact_address',
                '$user' 
            )";
        $sqls[] = $sql;
        pg_query($conn, $sql);
    }
    #endregion

    #region Passport files
    $folder_title = 'passport';
    $files_title = 'passport_files';
    $files_upload_title = 'passport_upload_files';
    $files_ocr_upload_title = 'passport_ocr_upload_files';
    $db_title = 'users_passport_files';

    if(isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    SaveFiles($folder_title, $files_ocr_upload_title, $db_title);
    #endregion

    #region Driver license
    $sql = "DELETE FROM users_driving_license WHERE user_id = '$ID'";
    $sqls[] = $sql;
    pg_query($conn, $sql);

    if(!empty($dl_series_number)) {
        $sql = "INSERT INTO users_driving_license (
                user_id,
                series_number,
                issued_by_who,
                issued_date,
                expire_date,
                last_user_id
            ) 
            VALUES (
                '$ID',
                '$dl_series_number',
                '$dl_issued_by_who',
                " . (empty($dl_issued_date) ? 'NULL' : "'" . $dl_issued_date . "'") . ",                 
                " . (empty($dl_expire_date) ? 'NULL' : "'" . $dl_expire_date . "'") . ",
                '$user'            
            )";
        $sqls[] = $sql;
        pg_query($conn, $sql);
    }
    #endregion

    #region Driver license files
    $folder_title = 'dl';
    $files_title = 'dl_files';
    $files_upload_title = 'dl_upload_files';
    $files_ocr_upload_title = 'dl_ocr_upload_files';
    $db_title = 'users_driving_license_files';

    if(isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    SaveFiles($folder_title, $files_ocr_upload_title, $db_title);
    #endregion

    #region Other files
    $folder_title = 'other';
    $files_title = 'other_files';
    $files_upload_title = 'other_upload_files';
    $db_title = 'users_other_files';

    if(isset($_POST[$files_title]) && count($_POST[$files_title]) > 0) {
        CheckFilesToDelete($_POST[$files_title], $db_title);
    }

    SaveFiles($folder_title, $files_upload_title, $db_title);
    #endregion
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';