<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$company_id = isset($_POST["company_id"]) ? (int)htmlspecialchars($_POST["company_id"]) : 0;

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        t1.id, t1.archive, t1.price,
        t2.name as classes, t3.name as service
    FROM 
        directory_car_classes_service_price as t1
    INNER JOIN directory_car_classes as t2 ON t1.directory_car_classes_id = t2.id
    INNER JOIN directory_services as t3 ON t1.directory_services_id = t3.id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $row->id = (int)$row->id;
        $row->price = (int)$row->price;
        $row->service = htmlspecialchars_decode($row->service);
        $row->classes = htmlspecialchars_decode($row->classes);
        $row->archive = (int)$row->archive == 0 ? 'Активен' : 'В архиве';

        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';