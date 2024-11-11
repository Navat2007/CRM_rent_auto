<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
$ID = htmlspecialchars($_POST["id"]);
$full_name = htmlspecialchars($_POST["full_name"]);
$short_name = htmlspecialchars($_POST["short_name"]);
$registration_date = htmlspecialchars($_POST["registration_date"]);
$ogrn = htmlspecialchars($_POST["ogrn"]);
$ogrnip = htmlspecialchars($_POST["ogrnip"]);
$inn = htmlspecialchars($_POST["inn"]);
$kpp = htmlspecialchars($_POST["kpp"]);
$legal_address = htmlspecialchars($_POST["legal_address"]);
$index_legal_address = htmlspecialchars($_POST["index_legal_address"]);
$address = htmlspecialchars($_POST["address"]);
$index_address = htmlspecialchars($_POST["index_address"]);
$manager_position = htmlspecialchars($_POST["manager_position"]);
$manager_fio = htmlspecialchars($_POST["manager_fio"]);
$contact_fio = htmlspecialchars($_POST["contact_fio"]);
$contact_phone = htmlspecialchars($_POST["contact_phone"]);
$bookkeeper_fio = htmlspecialchars($_POST["bookkeeper_fio"]);
$rs_legal_person = htmlspecialchars($_POST["rs_legal_person"]);
$bank = htmlspecialchars($_POST["bank"]);
$bank_bik = htmlspecialchars($_POST["bank_bik"]);
$bank_ks = htmlspecialchars($_POST["bank_ks"]);
$active = htmlspecialchars($_POST["active"]) === "true" ? 1 : 0;

$sql = "SELECT * FROM legal_persons WHERE id = '$ID'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$person_row = pg_fetch_object($result);

if($person_row->full_name != $full_name)
{
    $sql = "SELECT * FROM legal_persons WHERE full_name = '$full_name'";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);

    if (pg_num_rows($result) > 0) {
        $error = 1;
        $error_text = "Юр лицо с таким полным наименованием уже существует";
    }
}

if($error === 0){

    $sql = "
        UPDATE 
            legal_persons 
        SET 
            full_name = '$full_name', 
            short_name = '$short_name', 
            registration_date = " . (empty($registration_date) ? 'NULL' : "'" . $registration_date . "'") . ", 
            ogrn = '$ogrn', 
            ogrnip = '$ogrnip', 
            inn = '$inn', 
            kpp = '$kpp', 
            legal_address = '$legal_address', 
            index_legal_address = '$index_legal_address', 
            address = '$address', 
            index_address = '$index_address', 
            manager_position = '$manager_position', 
            manager_fio = '$manager_fio', 
            contact_fio = '$contact_fio', 
            contact_phone = '$contact_phone', 
            bookkeeper_fio = '$bookkeeper_fio', 
            rs_legal_person = '$rs_legal_person', 
            bank = '$bank', 
            bank_bik = '$bank_bik', 
            bank_ks = '$bank_ks', 
            status = '$active', 
            last_user_id = '$user'
        WHERE id = '$ID'";

    $sqls[] = $sql;
    pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';