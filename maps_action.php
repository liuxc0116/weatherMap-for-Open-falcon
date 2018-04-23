<?php
    require_once "lib/mysql.class.php";
    $raw_data = file_get_contents("php://input");;

    $arr = json_decode($raw_data, true);
    $action = $arr["action"];

    if ($action == "add") {
        $con = new mysql();
        $maps = $arr["data"];
        foreach ($maps as $map) {
            $sql = "insert into map(config_name) values('" . $map . "')";
            if ($con->query($sql) != true) {
                header( 'HTTP/1.1 500 Internal Server Error');
            }
        }
        $con->close();
    } elseif ($action == "del") {
        $con = new mysql();
        $id = $arr["data"];
        $sql = "delete from map where id=" . $id;
        if ($con->query($sql) != true) {
            header( 'HTTP/1.1 500 Internal Server Error');
        }
        $con->close();
    } else {
        header( 'HTTP/1.1 400 Bad Request');
    }
?>
