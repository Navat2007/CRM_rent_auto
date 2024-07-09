<?php
$DB_SERVER = "188.225.46.81";
$DB_PORT = "5432";
$DB_USER = "rent";
$DB_PASSWORD = "yuh8AC_NzZk-dX9g";
$DB_DATABASE = "rentcars";

$conn = pg_connect("host=$DB_SERVER port=$DB_PORT dbname=$DB_DATABASE user=$DB_USER password=$DB_PASSWORD");

if (!$conn) {
    echo "Failed to connect to PostgreSQL: " . pg_last_error();
    exit;
}