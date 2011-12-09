<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#             	 Автор  :  Sea                        #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#   		По всем вопросам пишите в ICQ.        #
#-----------------------------------------------------#

// mod Gemorroj

//error_reporting(0);
// данные для соединения с БД
$mysql = mysql_connect('localhost', 'root', '') or die('Could not connect');
mysql_select_db('sea', $mysql) or die('Could not db');
mysql_set_charset('utf8', $mysql);


$setting = mysql_query('SELECT * FROM `setting`', $mysql);
$setup = array();
while ($set = mysql_fetch_assoc($setting)) {
    $setup[$set['name']] = $set['value'];
}


define('DIR', dirname(__FILE__));
set_include_path(
    get_include_path() . PATH_SEPARATOR .
    DIR . DIRECTORY_SEPARATOR . 'PEAR'
);



require_once DIR . '/functions.php';


// Подключаем модуль партнерки
require DIR . '/../partner/inc.php';
?>
