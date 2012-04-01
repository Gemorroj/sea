<?php
// mod Gemorroj

if (!extension_loaded('ffmpeg')) {
    header('Content-Type: image/png');
    readfile(dirname(__FILE__) . '/moduls/marker.png');
    exit;
}

require 'moduls/config.php';
define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));

$id = intval($_GET['id']);
$frame = $i = $_GET['frame'] ? abs($_GET['frame']) : $setup['ffmpeg_frame'] + 1;

$pic = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql), 0);
$prev_pic = str_replace('/', '--', mb_substr(strstr($pic, '/'), 1));
$location = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif';

if (substr($pic, 0, 1) != '.' && !is_file($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame.  '.gif')) {
    $mov = new ffmpeg_movie($pic, false);
    if (!$mov) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'dis/load.png', true, 301);
        exit;
    }

    while (!$fr = $mov->getFrame($i)) {
        $i--;
        if ($i < 0) {
            exit;
        }
    }

    $tmp = DIR . '/cache/' . mt_rand(1000, 999999) . '.tmp';
    imagegif($fr->toGDImage(), $tmp);
    img_resize($tmp, $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif', 0, 0, $setup['marker']);
    unlink($tmp);
}

header('Location: ' . $location, true, 301);

?>
