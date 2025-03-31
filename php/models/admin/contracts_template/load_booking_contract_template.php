<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        *
    FROM 
        contract_templates
    WHERE 
        section = 'booking'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $params = pg_fetch_object($result);
    pg_free_result($result);
}
else
    $params = '';

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';