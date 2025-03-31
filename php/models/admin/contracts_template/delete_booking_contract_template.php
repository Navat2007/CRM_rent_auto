<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$sql = "SELECT * FROM contract_templates WHERE section = 'booking'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);
$row = pg_fetch_object($result);

if($row)
{
    $oldpath = $_SERVER['DOCUMENT_ROOT'] . $row->file_url;
    @unlink($oldpath);

    $sql = "DELETE FROM contract_templates WHERE section = 'booking'";
    $sqls[] = $sql;
    pg_query($conn, $sql);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';