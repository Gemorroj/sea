<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#                  Автор  :  Sea                      #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#           По всем вопросам пишите в ICQ.            #
#-----------------------------------------------------#

// mod Gemorroj

require 'moduls/config.php';

define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));

$id = intval($_REQUEST['id']);
$resize = true;
$marker = $setup['marker'];


if (isset($_POST['size'])) {
    list($w, $h) = explode('x', $_POST['size']);
    $w = abs($w);
    $h = abs($h);
} else {
    $w = abs(@$_REQUEST['W']);
    $h = abs(@$_REQUEST['H']);
}

if (!$w || !$h) {
    $resize = false;
    list($w, $h) = explode('*', $setup['prev_size']);
} else if ($marker) {
    $marker = ($marker == 2 ? 0 : 1);
}

$pic = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql), 0);
$prev_pic = str_replace('/', '--', mb_substr(strstr($pic, '/'), 1));

if ($resize) {
    $prev_pic = $w . 'x' . $h . '_' . $prev_pic;
    mysql_query('UPDATE `files` SET `loads`=`loads`+1, `timeload`=' . $_SERVER['REQUEST_TIME'] . ' WHERE `id`=' . $id, $mysql);
}

$location = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $setup['picpath'] . '/' . $prev_pic . '.gif';


if (!file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
    if (!img_resize($pic, $setup['picpath'] . '/' . $prev_pic . '.gif', $w, $h, $marker)) {
        error('Error');
    }
}

header('Location: ' . $location, true, 301);

?>
