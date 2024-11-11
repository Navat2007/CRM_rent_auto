<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user = $authorization[1];
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

$sql = "SELECT * FROM legal_persons WHERE full_name = '$full_name'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $error = 1;
    $error_text = "Юр лицо с таким полным наименованием уже существует";
}

if($error === 0){
    $sql = "
        INSERT INTO legal_persons (full_name, short_name, registration_date, ogrn, ogrnip, inn, kpp, legal_address, index_legal_address, address, index_address, manager_position, manager_fio, contact_fio, contact_phone, bookkeeper_fio, rs_legal_person, bank, bank_bik, bank_ks, status, last_user_id) 
        VALUES ('$full_name', '$short_name', " . (empty($registration_date) ? 'NULL' : "'" . $registration_date . "'") . ", '$ogrn', '$ogrnip', '$inn', '$kpp', '$legal_address', '$index_legal_address', '$address', '$index_address', '$manager_position', '$manager_fio', '$contact_fio', '$contact_phone', '$bookkeeper_fio', '$rs_legal_person', '$bank', '$bank_bik', '$bank_ks', '$active', $user) 
        RETURNING id";
    $sqls[] = $sql;
    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $lastID = $row->id;

    $params[] = (object)[
        'id' => $lastID,
    ];
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';