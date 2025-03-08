<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';

if(!isset($_GET["Id"])){
    $content = (object)[
        'error' => 'Не указан Id автомобиля',
    ];
    echo json_encode($content);
    die();
}

$id = htmlspecialchars($_GET["Id"]);

$sql = "
    SELECT 
        car.id
    FROM 
        car
    WHERE 
        car.id = '{$id}'";

$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0){
    $sql = "
    SELECT 
        car.id, car.archive, car.created_at, car.updated_at, car.archived_at,
        car.directory_car_classes_id, car.directory_car_brands_id,
        car.directory_car_models_id, car.directory_car_generations_id,
        car.directory_car_configurations_id, car.car_photo_avatar
    FROM 
        car    
    WHERE 
        car.id = '{$id}'";

    $result = pg_query($conn, $sql);
    $row = pg_fetch_object($result);
    $car = (object)[
        'id' => (int)$row->id,
        'archive' => (int)$row->archive,
        'created_at' => $row->created_at,
        'updated_at' => $row->updated_at,
        'archived_at' => $row->archived_at,
        'directory_car_classes_id' => (int)$row->directory_car_classes_id == 0 ? null : (int)$row->directory_car_classes_id,
        'directory_car_brands_id' => (int)$row->directory_car_brands_id == 0 ? null : (int)$row->directory_car_brands_id,
        'directory_car_models_id' => (int)$row->directory_car_models_id == 0 ? null : (int)$row->directory_car_models_id,
        'directory_car_generations_id' => (int)$row->directory_car_generations_id == 0 ? null : (int)$row->directory_car_generations_id,
        'directory_car_configurations_id' => (int)$row->directory_car_configurations_id == 0 ? null : (int)$row->directory_car_configurations_id,
        'data' => (object)[],
        'photo' => (object)[
            'main_photo' => $row->car_photo_avatar != null ? 'https://bee-c.ru' . $row->car_photo_avatar : null,
            'photos' => [],
        ],
        'prices' => (object)[],
    ];

    if($car->directory_car_classes_id != null){
        $sql = "
        SELECT
            *
        FROM
            directory_car_classes
        WHERE
            id = '{$car->directory_car_classes_id}'";
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);
        $car->data->class = htmlspecialchars_decode($row->name);
    }

    if($car->directory_car_brands_id != null){
        $sql = "
        SELECT
            *
        FROM
            directory_car_brands
        WHERE
            id = '{$car->directory_car_brands_id}'";
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);
        $car->data->brand = htmlspecialchars_decode($row->name);
    }

    if($car->directory_car_models_id != null){
        $sql = "
        SELECT
            *
        FROM
            directory_car_models
        WHERE
            id = '{$car->directory_car_models_id}'";
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);
        $car->data->model = htmlspecialchars_decode($row->name);
    }

    if($car->directory_car_generations_id != null){
        $sql = "
        SELECT
            *
        FROM
            directory_car_generations
        WHERE
            id = '{$car->directory_car_generations_id}'";
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);
        $car->data->generation = htmlspecialchars_decode($row->name);
    }

    if($car->directory_car_configurations_id != null){
        $sql = "
        SELECT
            *
        FROM
            directory_car_configurations
        WHERE
            id = '{$car->directory_car_configurations_id}'";
        $result = pg_query($conn, $sql);
        $row = pg_fetch_object($result);
        $car->data->configuration = htmlspecialchars_decode($row->name);
    }

    #region Photos
    $sql = "
    SELECT 
        *
    FROM 
        cars_photo_files
    WHERE 
        car_id = '{$car->id}'";
    $result = pg_query($conn, $sql);

    if(pg_num_rows($result) > 0)
    {
        while ($row = pg_fetch_object($result))
        {
            $photo = (object)[
                'id' => (int)$row->id,
                'photo' => 'https://bee-c.ru' . $row->file_path,
                'file_name' => $row->file_name,
                'file_extension' => $row->file_extension,
            ];

            $car->photo->photos[] = $photo;
        }
    }
    #endregion

    #region Price periods
    $saved_price_periods = array();
    $sql = "
        SELECT 
            dpp.id, dpp.name, dpp.days_from, dpp.days_until, cppd.price
        FROM 
            car_price_per_day as cppd            
        LEFT JOIN 
           directory_price_periods as dpp ON cppd.directory_price_period_id = dpp.id AND dpp.archive = 0
        WHERE cppd.car_id = '{$car->id}'";
    $price_period_result = pg_query($conn, $sql);

    if(pg_num_rows($price_period_result) > 0) {
        while ($price_period_row = pg_fetch_object($price_period_result)) {
            $saved_price_periods[] = (object)[
                'id' => (int)$price_period_row->id,
                'name' => htmlspecialchars_decode($price_period_row->name),
                'days_from' => (int)$price_period_row->days_from === 0 ? null : (int)$price_period_row->days_from,
                'days_until' => (int)$price_period_row->days_until === 0 ? null : (int)$price_period_row->days_until,
                'price' => (int)$price_period_row->price,
            ];
        }
    }

    $car->prices->price_per_day = $saved_price_periods;
    #endregion

    $content = (object)[
        'car' => $car,
    ];
    echo json_encode($content);
}
else{
    $content = (object)[
        'error' => 'Автомобиль не найден',
    ];
    echo json_encode($content);
}