<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>MAP列表</title>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <script src="http://libs.baidu.com/json/json2/json2.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="">
    <table class="table">
        <thead>
        <tr>
            <th>文件名</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
        require_once "lib/mysql.class.php";
        $con = new mysql();
        $sql = "select id, config_name from map";
        $query = $con->query($sql);
        $alldata = array();
        while ($rw = $con->fetch_array($query)) {
            $alldata[] = $rw["config_name"];
            echo "<tr><td><a href='editor.php?mapname=" . $rw["config_name"] . "'>" . $rw["config_name"]. "</a></td><td><a class='del_map' pri-data='" . $rw['id'] . "' href='javascript:void(0);'>删除</a></td></tr>";
        }
        $con->close();
        ?>
        </tbody>
    </table>
</div>
<button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
    添加MAP
</button>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">添加MAP</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>选项</th>
                        <th>文件名</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $weathermap_confdir = realpath(dirname(__FILE__).'/configs');
                    if( is_dir($weathermap_confdir)) {
                        $n=0;
                        $dh = opendir($weathermap_confdir);
                        if($dh) {
                            $i = 0; $skipped = 0;

                            while($file = readdir($dh))
                            {
                                $realfile = $weathermap_confdir.'/'.$file;

                                // skip .-prefixed files like .htaccess, since it seems
                                // that otherwise people will add them as map config files.
                                // and the index.php too - for the same reason
                                if (in_array($file, $alldata)) {
                                    continue;
                                }
                                if( substr($file,0,1) != '.' && $file != "index.php") {

                                    if(is_file($realfile))
                                    {
                                        echo '<tr><td><input type="checkbox" name="cbconf" value="' . $file .'"/></td><td>' . "$file" . '</td></tr>';
                                    }
                                }
                            }
                            closedir($dh);
                        }
                        else {
                            print "<tr><td>Can't open $weathermap_confdir to read - you should set it to be readable by the webserver.</td></tr>";
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" id="add_map" class="btn btn-primary">添加</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
       $("#add_map").click(function () {
           var maps = [];
           $.each($('input:checkbox:checked'),function(){
               maps.push($(this).val());
           });
           var post_data = {};
           post_data["action"] = "add";
           post_data["data"] = maps;
           var url = "maps_action.php";
           $.ajax(url,{
               type: 'POST',
               data: JSON.stringify(post_data),
               success: function (data) {
                   window.location.href = window.location.href;
               },
               fail: function () {
                    alert("error");
               }
           });
       });

       $(".del_map").click(function () {
            var id = $(this).attr('pri-data');
            var post_data = {};
            post_data["action"] = "del";
            post_data["data"] = id;
            var url = "maps_action.php";
            $.ajax(url,{
                type: 'POST',
                data: JSON.stringify(post_data),
                success: function (data) {
                    window.location.href = window.location.href;
                },
                fail: function () {
                    alert("error");
                }
            });
        });
    });
</script>
</body>
</html>
