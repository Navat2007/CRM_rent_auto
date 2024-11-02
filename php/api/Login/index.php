<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('memory_limit', '2048M');

require_once('vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$base_url_web = "https://test-center.educom.ru/";
$base_url_api = "https://test-api-uas.educom.ru/";
$client_id = "39e036e0e9dcb8";
$key = "@UNkhFpGOiavwgU1E";

if (strpos($_SERVER["HTTP_HOST"], "admin.patriot-sport") !== false) {
    $base_url_web = "https://center.educom.ru/";
    $base_url_api = "https://api-uas.educom.ru/";
    $client_id = "686f4dc440c7be";
    $key = "E4MfGRrhrSuxTvDWGr";
}

$code = htmlspecialchars($_GET['code']);

$jwt = JWT::encode([
    'clientId' => $client_id,
    'nbf' => 0,
    'exp' => strtotime('+28800 seconds'),
    'jti' => 'UUIDv4',
], $key, 'HS256');

function getClientToken()
{
    global $base_url_web, $base_url_api, $client_id, $key, $code;

    $params = array(
        'client_id' => $client_id,
        'client_secret' => $key,
        'grant_type' => "authorization_code",
        'code' => $code,
        'redirect_uri' => "https://admin.patriot-sport.ru/admin/api/Login/",
    );
    $url = $base_url_web . 'oauth/token';

    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params)
    ));

    $response = curl_exec($myCurl);

    curl_close($myCurl);

    return $response;
}

function getServiceToken()
{
    global $base_url_web, $base_url_api, $client_id, $key, $jwt;

    $params = array(
        'CLIENT_ID' => $client_id,
        'TOKEN' => $jwt,
    );
    $url = $base_url_api . 'v1/service/sessionCreate';

    $myCurl = curl_init();
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization:  Bearer ' . $jwt,
        ),
        CURLOPT_POSTFIELDS => http_build_query($params),
    ));

    $response = curl_exec($myCurl);
    curl_close($myCurl);

    return $response;
}

$clientData = getClientToken();
$serviceData = getServiceToken();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Авторизация через ЕКИС</title>

    <script src="sweetalert2.all.min.js"></script>
    <script src="/js_libs/js.cookie.js"></script>
</head>
<body>
<script>
    class User {
        static prop_init() {
            User.ID = 0;
            User.login = "";
            User.email = "";
            User.phone = "";
            User.photo = "";
            User.school_name = "";
            User.fio = "";
            User.create_time = "";
            User.role = "";
            User.msrd = 0;
            User.schoolID = 0;
        }
    }

    const url = '<?= $base_url_api ?>';
    let clientData = {};
    let serviceData = {};

    //console.log("JWT: ", '<?= $jwt ?>');

    try {
        //console.log("Client token: ", '<?= $clientData ?>');
        clientData = JSON.parse('<?= $clientData ?>');
    } catch (e) {
        console.log(e);
        clientData = {status: false};
    }

    try {
        //console.log("Service token: ", '<?= $serviceData ?>');
        serviceData = JSON.parse('<?= $serviceData ?>');
    } catch (e) {
        console.log(e);
        serviceData = {status: false};
    }

    User.prop_init();

    //console.log(clientData);
    //console.log(serviceData);
    //console.log('<?= $base_url_web . 'oauth/token' ?>');
    //console.log('<?= $client_id ?>');
    //console.log('<?= $code ?>');

    Swal.fire({
        title: 'Пожалуйста подождите, идет авторизация...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        showConfirmButton: false,
    });
    Swal.showLoading();

    setTimeout(async () => {

        if (clientData.status === false || serviceData.status === false) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                text: 'Не удалось авторизоваться! Попробуйте еще раз.',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.replace(window.location.origin + `/admin/`);
                }
            });
        } else {
            const clientToken = clientData.data.accessToken;
            const clientRefreshToken = clientData.data.refreshToken;
            const serviceToken = serviceData.data.token;

            let form = new FormData();
            form.append('clientToken', clientToken);
            form.append('serviceToken', serviceToken);
            form.append('refreshToken', clientRefreshToken);
            form.append('url', url + 'v1/user/auth/me');

            let response = await fetch('getPersonData.php', {
                method: 'POST',
                body: form
            });
            let result = await response.json();
            const authResult = JSON.parse(result.response);

            //console.log(result);
            //console.log(authResult);

            form = new FormData();
            form.append('ekisID', authResult.data.role.ekis_id);
            form.append('ekisEmail', authResult.data.person.email);

            response = await fetch('/admin/models/login/check.php', {
                method: 'POST',
                body: form
            });
            result = await response.json();

            //console.log(result);

            if (result[0] != 0) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    text: 'Не удалось авторизоваться! Попробуйте еще раз.',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.replace(window.location.origin + `/admin/`);
                    }
                });
            } else {
                let item = result[2];

                User.ID = parseInt(item.ID);
                User.login = (item.login);
                User.email = (item.email);
                User.phone = (item.phone);
                User.photo = (item.photo);
                User.school_name = (item.school_name);
                User.fio = (item.fio);
                User.create_time = (item.create_time);
                User.role = (item.role);
                User.msrd = item.role == 'superadmin' ? 0 : item.mrsd;
                User.dir_fio = (item.dir_fio);
                User.dir_phone = (item.dir_phone);
                User.dir_email = (item.dir_email);
                User.org_name = (item.org_name);
                User.org_short_name = (item.org_short_name);
                User.ekis = (item.ekis);
                User.address = (item.address);
                User.schoolID = (item.schoolID);

                let expire_days = 14;

                Cookies.set('userID', User.ID, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('login', User.login, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('email', User.email, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('phone', User.phone, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('photo', User.photo, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('school_name', User.school_name, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('fio', User.fio, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('create_time', User.create_time, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('role', User.role, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('mrsd', JSON.stringify(User.msrd), {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('dir_fio', User.dir_fio, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('dir_phone', User.dir_phone, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('dir_email', User.dir_email, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('org_name', User.org_name, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('org_short_name', User.org_short_name, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('ekis', User.ekis, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('address', User.address, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });
                Cookies.set('schoolID', User.schoolID, {
                    expires: expire_days,
                    sameSite: 'strict',
                    secure: true
                });

                Swal.close();

                window.location.replace(window.location.origin + "/admin/");
            }
        }

    }, 2000);
</script>
</body>
</html>