<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$user_id = htmlspecialchars($_POST["user_id"]);

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        t1.*, u.email as init_email, ui.full_name as init_full_name
    FROM 
        himera_results as t1
    INNER JOIN 
        users as u ON t1.last_user_id = u.id
    INNER JOIN 
        users_info as ui ON ui.user_id = u.id
    WHERE 
        t1.user_id = '$user_id'";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';