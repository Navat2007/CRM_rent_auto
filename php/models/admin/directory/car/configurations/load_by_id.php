<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';

$id = isset($_POST["id"]) ? (int)htmlspecialchars($_POST["id"]) : die("Не передан ID");

$error = 0;
$error_text = "";
$sqls = array();
$params = array();

$sql = "
    SELECT 
        t1.id, t1.name, t1.archive, 
        t2.id as model, 
        t3.id as brand 
    FROM 
        directory_car_configurations as t1
    INNER JOIN directory_car_models as t2 ON t1.directory_car_models_id = t2.id
    INNER JOIN directory_car_brands as t3 ON t2.directory_car_brands_id = t3.id
    WHERE t1.id = $id";
$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    $row = pg_fetch_object($result);

    $row->id = (int)$row->id;
    $row->name = htmlspecialchars_decode($row->name);
    $row->model = (int)$row->model;
    $row->brand = (int)$row->brand;
    $row->active = (int)$row->archive == 0;

    $params = $row;

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';