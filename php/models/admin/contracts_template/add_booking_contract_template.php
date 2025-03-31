<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];

if (isset($_FILES['file'])) {
    $baseDirName = $_SERVER['DOCUMENT_ROOT'] . "/files/contracts_template";

    if (!file_exists($baseDirName)) {
        $oldmask = umask(0);
        $mkdir_result = mkdir($baseDirName, 0777, true);
        umask($oldmask);
    }

    if($_FILES['file']['error'] != UPLOAD_ERR_OK) {
        $error = 1;
        $error_text = "Ошибка загрузки файла";
    }
    else {
        $temp_name = $_FILES['file']['tmp_name'];
        $name = $_FILES['file']['name'];
        $file_name = explode('.', $name)[0];
        $file_extension = explode('.', $name)[1];
        $file_token = time();
        $path = $baseDirName . "/" . $file_token . "_" . $name;

        @unlink($path);

        $sql = "SELECT * FROM contract_templates WHERE section = 'booking'";
        $sqls[] = $sql;
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);

        if($row)
        {
            $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->file_url;
            @unlink($oldpath);
        }

        if (copy($temp_name, $path)) {
            $file_to_DB = "/files/contracts_template/" . $file_token . "_" . $name;

            $add_sql = "
            INSERT INTO contract_templates (
                section,
                file_name,
                file_url,
                file_ext,
                last_user_id
            ) 
            VALUES (
                'booking',
                '$file_name',
                '$file_to_DB',
                '$file_extension',
                '$user'
            )";
            pg_query($conn, $add_sql);
            $params = $file_to_DB;
        }
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';