<!DOCTYPE html>
<html lang="zh-Cn">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/json/json2/json2.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body {
            margin-left: 10px;
            margin-top: 10px;
        }
        div {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div>
    <label>Step: </label>
    <span>
        <select id="step">
            <option value="10">10s</option>
            <option value="20">20s</option>
            <option value="30">30s</option>
            <option value="60" selected>1m</option>
            <option value="120">2m</option>
            <option value="180">3m</option>
            <option value="240">4m</option>
            <option value="300">5m</option>
            <option value="600">10m</option>
            <option value="1200">20m</option>
            <option value="1800">30m</option>
            <option value="3600">60m</option>
        </select>
    </span>
</div>
<div>
    <label>EndPoint: </label>
    <span>
        <select id="end_point">
            <option>选择</option>
        </select>
    </span>
</div>

<div>
    <label>Port: </label>
    <span>
        <select id="port">
            <option>选择</option>
        </select>
    </span>
</div>
<?php
require_once "lib/falcon.api.php";
$token = get_token();
?>
<script type="text/javascript">
    var headers = {
            "Content-Type": "application/json",
            "Apitoken": '{"name":"<?php echo g_falcon_api_name; ?>","sig":"<?php echo $token; ?>"}'
    };

    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]); return null;
    }

    function fill_endpoint(obj) {
        var options = "<option>选择</option>";
        for(var i=0; i < obj.length; i++) {
            port = obj[i];
            options += "<option value='" + port["id"] + "'>" + port["endpoint"] + "</option>";
        }
        console.log(options);
        $("#end_point").html(options);
    }

    $(document).ready(function() {
        var url = "<?php echo g_falcon_api_host; ?>/api/v1/graph/endpoint?q=.*(pe[0-9]|sw[0-9])";
        $.ajax(url, {
            type: 'get',
            headers: headers,
            success: function (obj) {
                fill_endpoint(obj);
            },
            fail: function (obj) {
                alert('The system is busy, please try again later' + obj);
            }
        });
    });

    function fill_port(obj) {
        var options = "<option>选择</option>";
        for(var i=0; i < obj.length; i++) {
            port = obj[i];
            options += "<option value='" + port["counter"] + "'>" + port["counter"] + "</option>";
        }
        console.log(options);
        $("#port").html(options);
    }

    function get_port(eid) {
        var url = "<?php echo g_falcon_api_host; ?>/api/v1/graph/endpoint_counter?eid=" + eid + "&metricQuery=switch.if.Out/.*ifName=[^(Eth|Vbd)]";
        $.ajax(url, {
            type: 'get',
            headers: headers,
            success: function (obj) {
                fill_port(obj);
            },
            fail: function (obj) {
                alert('The system is busy, please try again later' + obj);
            }
        });
    }

    $("#end_point").change(function () {
        if(this.value != '') {
            var val = this.options[this.selectedIndex].value;
            var action = getUrlParam('action');
            if (action == "node") {
                var endpoint = this.options[this.selectedIndex].innerText;
                var url = "<?php echo g_grafana_graph_host; ?>/dashboard/db/view-host?orgId=1";
                url += "&var-host=" + endpoint;
                window.opener.document.all.node_infourl.value = url;
                //window.opener.document.all.node_hover.value = url;
                window.close();
            }
            get_port(val);
        }
    });

    $("#port").change(function () {
        if (this.value != '') {
            var url = "<?php echo g_grafana_graph_host; ?>/dashboard/db/view-port-bandwidth?orgId=1";
            //&var-hosts=172.16.1.202&
            //&var-ifName=13,ifName%3D10GE1%2F0%2F9
            var val = this.options[this.selectedIndex].value;
            var port_name = val.split('Out/')[1];
            var endpoint = $("#end_point option:selected").text();
            var step = $("#step option:selected").val();
            var ifName = val.split('ifIndex=')[1];
            var ret_val = "falcon@" + endpoint + '@' + step + '@' + port_name;
            window.opener.document.all.link_target.value = ret_val;
            url += "&var-host=" + endpoint;
            url += "&var-port=" + ifName;
            window.opener.document.all.link_infourl.value = url;
            //window.opener.document.all.link_hover.value = url;

            window.close();
        }
    });

</script>

</body>
</html>
