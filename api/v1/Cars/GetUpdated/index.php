<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';

if(!isset($_GET["DateFrom"])){
    $content = (object)[
        'error' => 'Не указана дата начала',
    ];
    echo json_encode($content);
    die();
}

if(!isset($_GET["DateTo"])){
    $content = (object)[
        'error' => 'Не указана дата окончания',
    ];
    echo json_encode($content);
    die();
}

$date_from = htmlspecialchars($_GET["DateFrom"]);
$date_to = htmlspecialchars($_GET["DateTo"]);

$sql = "
    SELECT 
        car.id
    FROM 
        car
    WHERE 
        car.updated_at >= '{$date_from} 00:00:00' AND car.updated_at <= '{$date_to} 23:59:59'
    ORDER BY 
        car.id";

$result = pg_query($conn, $sql);
$cars = [];

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $cars[] = (int)$row->id;
    }

    pg_free_result($result);
}

$content = (object)[
    'cars' => $cars,
];
echo json_encode($content);