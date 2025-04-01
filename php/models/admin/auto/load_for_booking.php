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
        car.id, car.state_number, car.car_photo_avatar as avatar, car.archive, car.mileage, car.release_year,
        car.fuel_tank_capacity, brand.name as brand, model.name as model, class.name as class, class.cost_extra_mileage, 
        car.directory_car_classes_id as class_id,
        generation.name as generation, configuration.name as configuration, fuel_type.name as fuel_type,
        tires_type.name as tires_type, colors.name as color, status.name as status,
        sts.series as sts_series, sts.number as sts_number, pts.vin, pts.cost_assessment, pts.body_number
    FROM 
        car
    LEFT JOIN 
        cars_sts as sts ON sts.car_id = car.id
    LEFT JOIN 
        cars_pts as pts ON pts.car_id = car.id
    LEFT JOIN 
        directory_car_brands as brand ON brand.id = car.directory_car_brands_id
    LEFT JOIN 
        directory_car_models as model ON model.id = car.directory_car_models_id
    LEFT JOIN 
        directory_car_classes as class ON class.id = car.directory_car_classes_id
    LEFT JOIN 
        directory_car_generations as generation ON generation.id = car.directory_car_generations_id
    LEFT JOIN 
        directory_car_configurations as configuration ON configuration.id = car.directory_car_configurations_id
    LEFT JOIN 
        directory_car_fuel_types as fuel_type ON fuel_type.id = car.directory_car_fuel_types_id
    LEFT JOIN 
        directory_car_tires_type as tires_type ON tires_type.id = car.to_directory_car_tires_type_id
    LEFT JOIN 
        directory_car_colors as colors ON colors.id = car.directory_car_colors_id
    LEFT JOIN 
        directory_car_statuses as status ON status.id = car.directory_car_statuses_id";

if($company_id != 0){
    $sql .= " AND car.company_id = '{$company_id}'";
}

$sqls[] = $sql;
$result = pg_query($conn, $sql);

if(pg_num_rows($result) > 0)
{
    while ($row = pg_fetch_object($result))
    {
        $row->id = (int)$row->id;
        $row->mileage = (float)$row->mileage;
        $row->avatar = $row->avatar == null ? "" : ("https://" . $_SERVER['HTTP_HOST'] . $row->avatar);
        $row->brand = $row->brand == null ? "" : htmlspecialchars_decode($row->brand);
        $row->model = $row->model == null ? "" : htmlspecialchars_decode($row->model);
        $row->class = $row->class == null ? "" : htmlspecialchars_decode($row->class);
        $row->class_id = $row->class_id == null ? "" : (int)$row->class_id;
        $row->generation = $row->generation == null ? "" : htmlspecialchars_decode($row->generation);
        $row->configuration = $row->configuration == null ? "" : htmlspecialchars_decode($row->configuration);
        $row->fuel_type = $row->fuel_type == null ? "" : htmlspecialchars_decode($row->fuel_type);
        $row->tires_type = $row->tires_type == null ? "" : htmlspecialchars_decode($row->tires_type);
        $row->cost_assessment = $row->cost_assessment == null ? "" : htmlspecialchars_decode($row->cost_assessment);
        $row->sts_number = $row->sts_number == null ? "" : htmlspecialchars_decode($row->sts_number);
        $row->sts_series = $row->sts_series == null ? "" : htmlspecialchars_decode($row->sts_series);
        $row->body_number = $row->body_number == null ? "" : htmlspecialchars_decode($row->body_number);
        $row->status = htmlspecialchars_decode($row->status);
        $row->state_number = htmlspecialchars_decode($row->state_number);
        $row->archive = (int)$row->archive == 0 ? 'Активен' : 'В архиве';

        #region Price periods
        $sql = "
        SELECT 
            dpp.id, dpp.name, dpp.days_from, dpp.days_until, cppd.price
        FROM 
            directory_price_periods as dpp
        LEFT JOIN 
            car_price_per_day as cppd ON cppd.directory_price_period_id = dpp.id AND cppd.car_id = '$row->id'
        WHERE dpp.archive = 0";
        $sqls[] = $sql;
        $price_period_result = pg_query($conn, $sql);

        if(pg_num_rows($price_period_result) > 0) {
            while ($price_period_row = pg_fetch_object($price_period_result)) {
                $row->saved_price_periods[] = (object)[
                    'id' => (int)$price_period_row->id,
                    'name' => htmlspecialchars_decode($price_period_row->name),
                    'days_from' => (int)$price_period_row->days_from,
                    'days_until' => (int)$price_period_row->days_until,
                    'price' => (int)$price_period_row->price,
                ];
            }
        }
        #endregion

        $params[] = $row;
    }

    pg_free_result($result);
}

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';