<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>maps view</title>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/json/json2/json2.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="row">
        <?php
        require_once "lib/mysql.class.php";
        $output_dir = 'output/';
        $con = new mysql();
        $sql = "select config_name from map";
        $query = $con->query($sql);
        while ($rw = $con->fetch_array($query)) {
            $tmp = explode('.conf', $rw['config_name']);
            $img_name = $output_dir . $tmp[0] . '.png';
            $thumb_name = $output_dir. $tmp[0] . '.thumb.png';
            $html_name = $tmp[0] . '.html';
            echo "<div class='col-md-2'><div style=''>" .$tmp[0]. "</div><div><a target='body' href='" . $html_name . "' ><img src='" .$thumb_name. "'> </a></div></div>";
        }

        $con->close();
        ?>
</div>

</body>
</html>
