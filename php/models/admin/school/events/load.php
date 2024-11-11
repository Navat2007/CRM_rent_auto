<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, Authorization, Content-Type, X-Auth-Token');

require $_SERVER['DOCUMENT_ROOT'] . '/php/include.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/auth.php';
require $_SERVER['DOCUMENT_ROOT'] . '/php/params.php';

$sql = "
    SELECT 
        t1.ID, t1.title, t1.sport_type, t1.event_start_month, t1.event_end_month, t1.event_start_year, t1.event_end_year,
        t2.title as sport_type_title
    FROM 
        events as t1
    INNER JOIN 
        events_sport_types as t2 ON t1.sport_type = t2.ID
    WHERE 
        t1.active = 1 
    ORDER BY 
        t1.create_time DESC";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_object($result))
    {
        $params[] = (object)[
            'id' => (int)$row->ID,
            'title' => htmlspecialchars_decode($row->title),
            'sport' => (object)[
                'id' => (int)$row->sport_type,
                'title' => htmlspecialchars_decode($row->sport_type_title),
            ],
            'event_start_month' => (int)$row->event_start_month,
            'event_end_month' => (int)$row->event_end_month,
            'event_start_year' => (int)$row->event_start_year,
            'event_end_year' => (int)$row->event_end_year,
        ];
    }
}

$conn->kill($conn->thread_id);
$conn->close();

require $_SERVER['DOCUMENT_ROOT'] . '/php/answer.php';