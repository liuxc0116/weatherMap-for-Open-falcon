#Weathermap for Open Falcon
在Weathermap 0.98的基础上增加了支持open falcon api当数据源, 增加了一些必要的视图界面——添加map到数据库，查看map，创建map, 修改map等

使用poller.php生成入库map的缩略图，展示图，html展示页面等

下面是一些页面的展示

![listMap](https://raw.githubusercontent.com/liuxc0116/public/master/weathermap/weathermap_list.png)

![addMap](https://raw.githubusercontent.com/liuxc0116/public/master/weathermap/wearthermap_add_map.png)

![listSmallPng](https://raw.githubusercontent.com/liuxc0116/public/master/weathermap/weathermap_list-png.png)

![showFullPng](https://raw.githubusercontent.com/liuxc0116/public/master/weathermap/weathermap_full_png.png)

![createMap](https://raw.githubusercontent.com/liuxc0116/public/master/weathermap/weathermap_create_map.png)

在此，感谢Howard Jones (howie@thingy.com)开发了Weathermap
Weathermap官方的[github](https://github.com/howardjones/network-weathermap)地址

安装方法

```
git clone https://github.com/liuxc0116/weatherMap-for-Open-falcon
cd weatherMap-for-Open-falcon
make release
```

添加数据库
`mysql -uroot -p < weathermap.sql`

修改数据库配置

```
vim lib/config.php
<?php
  define('g_dbhost' , '127.0.0.1');
  define('g_dbuser' , 'root');
  define('g_dbpassword' , '');
  define('g_dbname' , 'weathermap');
  define('g_dbcharset' , 'utf8');
?>
```

修改open falcon api配置

```
vim lib/falcon.api.php
define("g_falcon_api_name", "falcon_api_name");
define("g_falcon_api_passwd", "falcon_api_password");
define("g_falcon_api_host", "http://yourhost:8080");
define("g_grafana_graph_host", "http://yourhost:3000");
```


