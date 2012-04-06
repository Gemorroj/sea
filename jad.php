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

###############Если jad выключен##########
if (!$setup['jad_change']) {
    error('Not found');
}
###############Проверка переменных###############
$id = intval($_GET['id']);
###############Получаем инфу о файле###########
$d = mysql_fetch_row(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));

if (is_file($d[0])) {
    mysql_query('UPDATE `files` SET `loads` = `loads` + 1, `timeload` = ' . $_SERVER['REQUEST_TIME'] . ' WHERE `id` = ' . $id, $mysql);

    include 'moduls/PEAR/pclzip.lib.php';

    $zip = new PclZip($d[0]);
    $content = $zip->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);

    header('Content-type: text/vnd.sun.j2me.app-descriptor');
    header('Content-Disposition: attachment; filename="' . rawurlencode(basename($d[0])) . '.jad";');

    echo trim($content[0]['content']) . "\n" .
        'MIDlet-Jar-Size: ' . filesize($d[0]) . "\n" .
        'MIDlet-Jar-URL: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $d[0];
} else {
    error($setup['hackmess']);
}

?>
