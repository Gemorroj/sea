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

require 'moduls/config.php';
require 'moduls/header.php';
###############Если статистика выключена###############
if (!$setup['stat_change']) {
	error('Not found');
}

$title .= $_SESSION['language']['statistics'];

#######################################################

$files = mysql_fetch_row(mysql_query('SELECT COUNT(`id`), SUM(`loads`), SUM(`size`) FROM `files` WHERE `dir` = "0" AND `hidden` = "0"', $mysql));


$new_all_files = mysql_fetch_row(mysql_query('SELECT COUNT(`id`) FROM `files` WHERE `timeupload` > ' . ($_SERVER['REQUEST_TIME'] - (86400 * $setup['day_new'])) . ' AND `hidden` = "0"', $mysql));

echo '<div class="mblock">' . $_SESSION['language']['statistics'] . '</div>
<div class="row">
' . $_SESSION['language']['all files'] . ': <strong>' . intval($files[0]) . '</strong><br/>
' . $_SESSION['language']['total new files'] . ': <strong>' . intval($new_all_files[0]) . '</strong><br/>
' . $_SESSION['language']['total volume'] . ': <strong>' . size($files[2]) . '</strong><br/>
' . $_SESSION['language']['total downloads'] . ': <strong>' . $files[1] . '</strong><br/>
' . $_SESSION['language']['maximum online'] . ': <strong>' . intval($setup['online_max']) . '</strong> (' . tm(strtotime($setup['online_max_time'])) . ')<br/>
</div>
<div class="iblock">
- <a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/>
- <a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a></div>';

require 'moduls/foot.php';

?>
