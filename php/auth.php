<?php
$server_headers = getallheaders();

if (empty($server_headers['Authorization'])) {
    die("Требуется авторизация");
}

$authorization = explode('&', $server_headers['Authorization']);

$sql = "SELECT * FROM users WHERE id = '$authorization[1]' and token = '$authorization[0]'";
$result = pg_query($conn, $sql);

if (pg_num_rows($result) == 0) {
    $content = (object)[
        'error' => 3,
        'error_text' => "auth",
    ];
    echo json_encode($content);

    die();
}
