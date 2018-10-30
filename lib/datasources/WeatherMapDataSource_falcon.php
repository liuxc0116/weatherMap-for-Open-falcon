<?php
// Sample Pluggable datasource for PHP Weathermap 0.9
// - read a pair of values from a database, and return it
require_once dirname(__FILE__). "/../falcon.api.php";
class WeatherMapDataSource_falcon extends WeatherMapDataSource {

    function Init(&$map)
    {
        if(! function_exists("curl_init") ) return FALSE;
        if(! function_exists("curl_exec") ) return FALSE;

        return(TRUE);
    }

    function Recognise($targetstring)
    {
        if(preg_match("/^falcon@(.*)@(.*)@(.*)$/",$targetstring, $matches))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function ReadData($targetstring, &$map, &$item)
    {
        $data[IN] = NULL;
        $data[OUT] = NULL;
        $data_time = 0;

        if(preg_match("/^falcon@(.*)@(.*)@(.*)$/",$targetstring,$matches))
        {
            $host = $matches[1];
            $step = (int)$matches[2];
            $counter = $matches[3];
            $data_time = time();
            $start = $data_time - 2 * $step;
            $end = $data_time;
            $url = g_falcon_api_host . '/api/v1/graph/history';
            $in_port = "switch.if.In/" . $counter;
            $out_port = "switch.if.Out/" . $counter;
            wm_warn($url . "\n");
            $post_data = array(
                "step" => $step,
                "start_time" => $start,
                "end_time" =>$end,
                "consol_fun" => "AVERAGE",
                "hostnames" => array(
                    $host
                ),
                "counters" => array(
                    $in_port,
                    $out_port
                )
            );
            $data_string =  json_encode($post_data);
            $headers = array(
                "Content-Type: application/json",
                'Apitoken: {"name":"' . g_falcon_api_name . '","sig":"'. get_token().'"}',
                'Content-Length: ' . strlen($data_string)
            );
            wm_warn($data_string . "\n");
            $con = curl_init($url);
            curl_setopt($con, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($con, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($con, CURLOPT_HEADER, FALSE);
            curl_setopt($con, CURLOPT_NOBODY, FALSE);
            curl_setopt($con, CURLOPT_HTTPHEADER, $headers);
            $body = curl_exec($con);
            if ($body === false) {
                wm_warn("curl error \n");
                curl_close($con);
                return;
            }
            wm_debug($body. "\n");
            $arr = json_decode($body, true);
            foreach ($arr as $value) {
                $vals = $value["Values"];
                $len1 = sizeof($vals);
                $val = NULL;
                for ($i = $len1 - 1; $i >= 0; $i--) {
                    $val = $vals[$i]['value'];
                    if ($val != NULL) {
                        break;
                    }
                }

                if ($value["counter"] == $in_port) {
                    $data[IN] = $val;
                } elseif ($value["counter"] == $out_port){
                    $data[OUT] = $val;
                }
            }
            curl_close($con);
        }

        wm_debug ("URL ReadData: Returning (".($data[IN]===NULL?'NULL':$data[IN]).",".($data[OUT]===NULL?'NULL':$data[IN]).",$data_time)\n");

        return( array($data[IN], $data[OUT], $data_time) );
    }
}

// vim:ts=4:sw=4:
