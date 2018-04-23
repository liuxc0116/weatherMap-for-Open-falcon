<?php
/**
 * Created by PhpStorm.
 * User: liuxc
 * Date: 2018/4/16
 * Time: 下午2:55
 */
require_once "WeatherMap.functions.php";

define("g_falcon_api_name", "api_name");
define("g_falcon_api_passwd", "api_password");
define("g_falcon_api_host", "http://172.16.1.97:8080");
define("g_grafana_graph_host", "http://172.16.1.97:3000");
function get_token()
{
    /*
     *
    {
        "sig": "34d42bbc3d3611e89df882bb1d725923",
        "name": "liuxc",
        "admin": false
    }
     */
    $url = g_falcon_api_host . "/api/v1/user/login";
    $post_body = array(
        "name" => g_falcon_api_name,
        "password" => g_falcon_api_passwd
    );

    $headers = array(
        "Content-Type: application/json",
        'Content-Length: ' . strlen(json_encode($post_body))
    );
    $con = curl_init($url);
    curl_setopt($con, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($con, CURLOPT_POSTFIELDS, json_encode($post_body));
    curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($con, CURLOPT_HEADER, FALSE);
    curl_setopt($con, CURLOPT_NOBODY, FALSE);
    curl_setopt($con, CURLOPT_HTTPHEADER, $headers);
    $body = curl_exec($con);
    if ($body === false) {
        curl_close($con);
        wm_warn("curl error" . json_encode($body) . "\n");
        return;
    }

    wm_debug($body . "\n");
    $arr = json_decode($body, true);
    curl_close($con);
    return $arr["sig"];
}

?>
