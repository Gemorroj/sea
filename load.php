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
###############Проверка переменных###############
$id = intval($_GET['id']);
###############Получаем инфу о файле###########
$d = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql), 0);

if (file_exists($d)) {
    mysql_query('UPDATE `files` SET `loads` = `loads` + 1, `timeload` = ' . $_SERVER['REQUEST_TIME'] . ' WHERE `id` = ' . $id, $mysql);
    $dir = dirname($_SERVER['PHP_SELF']);
    $dir = ($dir == DIRECTORY_SEPARATOR ? '' : $dir);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $dir . '/' . str_replace('%2F', '/', rawurlencode($d)), true, 301);
} else {
    echo $setup['hackmess'];
}

?>
