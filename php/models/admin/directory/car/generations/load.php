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
        t1.id, t1.name, t1.archive, 
        t2.name as model, 
        t3.name as brand 
    FROM 
        directory_car_generations as t1
    INNER JOIN directory_car_models as t2 ON t1.directory_car_models_id = t2.id
    INNER JOIN directory_car_brands as t3 ON t2.directory_car_brands_id = t3.id
    ";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $row->id = (int)$row->id;
        $row->name = htmlspecialchars_decode($row->name);
        $row->model = htmlspecialchars_decode($row->model);
        $row->brand = htmlspecialchars_decode($row->brand);
        $row->archive = (int)$row->archive == 0 ? 'Активен' : 'В архиве';

        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';