<?php

mysql_query("REPLACE INTO `online` (`ip`, `time`) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', NOW());", $mysql);
mysql_query('DELETE FROM `online` WHERE `time` < (NOW() - INTERVAL ' . $setup['online_time'] . ' SECOND)', $mysql);

$online = mysql_result(mysql_query('SELECT COUNT(1) FROM online', $mysql), 0);
if ($online > $setup['online_max']) {
    mysql_query('REPLACE INTO `setting`(`name`, `value`) VALUES("online_max", "' . $online . '");', $mysql);
    mysql_query('REPLACE INTO `setting`(`name`, `value`) VALUES("online_max_time", NOW());', $mysql);
}
