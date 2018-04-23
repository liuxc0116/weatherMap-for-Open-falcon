<?php
/**
 * Created by PhpStorm.
 * User: liuxc
 * Date: 2018/3/30
 * Time: 下午5:28
 */

require_once "lib/mysql.class.php";
require_once "lib/Weathermap.class.php";
$conf_dir = 'configs/';
$output_dir = 'output/';
$con = new mysql();
$sql = "select config_name from map";
$query = $con->query($sql);
while ($rw = $con->fetch_array($query)) {
    $tmp = explode('.conf', $rw['config_name']);
    $img_name = $tmp[0] . '.png';
    $thumb_name = $tmp[0] . '.thumb.png';
    $html_name = $tmp[0] . '.html';

    draw_map($conf_dir. $rw['config_name'], $output_dir. $img_name, $html_name, $output_dir. $thumb_name);
}

$con->close();

function draw_map($configfile, $imagefile, $htmlfile, $thumbnailfile, $dumpafter=0, $dumpstats=0, $randomdata=0, $dumpconfig='') {
    $map=new Weathermap;
    $rrdtool="/usr/bin/rrdtool";
    $map->rrdtool = $rrdtool;
    $map->context="cli";
    $options_output = array();
    $defines = array();
    if ($map->ReadConfig($configfile)) {
        // allow command-lines to override the config file, but provide a default if neither are present
        if ($imagefile == '')
        {
            if ($map->imageoutputfile == '') { $imagefile="weathermap.png"; }
            else { $imagefile=$map->imageoutputfile; }
        }

        if ($htmlfile == '')
        {
            if ($map->htmloutputfile != '') { $htmlfile = $map->htmloutputfile; }
        }

        // feed in any command-line defaults, so that they appear as if SET lines in the config

        // XXX FIXME
        foreach ($defines as $hintname=>$hint)
        {
            $map->add_hint($hintname,$hint);
        }

        // now stuff in all the others, that we got from getopts
        foreach ($options_output as $key=>$value)
        {
            // $map->$key = $value;
            $map->add_hint($key,$value);
        }

        if ( (isset($options_output['sizedebug']) && ! $options_output['sizedebug']) || (!isset($options_output['sizedebug'])) )
        {
            if ($randomdata == 1) { $map->RandomData(); }
            else { $map->ReadData(); }
        }

        # exit();

        if ($imagefile != '')
        {
            $map->DrawMap($imagefile, $thumbnailfile);
            $map->imagefile=$imagefile;
        }

        if ($htmlfile != '')
        {
            wm_debug("Writing HTML to $htmlfile\n");

            $fd=fopen($htmlfile, 'w');
            fwrite($fd,
                '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>');
            if($map->htmlstylesheet != '') fwrite($fd,'<link rel="stylesheet" type="text/css" href="'.$map->htmlstylesheet.'" />');
            fwrite($fd,'<meta http-equiv="refresh" content="300" /><title>' . $map->ProcessString($map->title, $map) . '</title></head><body>');

            if ($map->htmlstyle == "overlib")
            {
                fwrite($fd,
                    "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden; z-index:1000;\"></div>\n");
                fwrite($fd,
                    "<script type=\"text/javascript\" src=\"overlib.js\"><!-- overLIB (c) Erik Bosrup --></script> \n");
            }

            fwrite($fd, $map->MakeHTML());
            fwrite($fd,
                '<hr /><span id="byline">Network Map created with <a href="http://www.network-weathermap.com/?vs='
                . '' . '">PHP Network Weathermap v' . ''
                . '</a></span></body></html>');
            fclose ($fd);
        }

        if ($dumpconfig != '')
            $map->WriteConfig($dumpconfig);

        if ($dumpstats != '')
            $map->DumpStats();

        if ($map->dataoutputfile != '') {
            $map->WriteDataFile($map->dataoutputfile);
        }

        if ($dumpafter == 1)
            print_r ($map);
    }
    else { die ("\n\nCould not read Weathermap config file. No output produced. Maybe try --help?\n"); }

}

function my_assert_handler($file, $line, $code)
{
    echo "Assertion Failed:
        File $file
        Line $line
        Code $code";
    debug_print_backtrace();
    exit();
}
