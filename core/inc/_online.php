<?php
$db = Db_Mysql::getInstance();

$db->prepare('REPLACE INTO `online` (`ip`, `time`) VALUES (?, NOW())')->execute(array($_SERVER['REMOTE_ADDR']));
$db->exec('DELETE FROM `online` WHERE `time` < (NOW() - INTERVAL ' . intval(Config::get('online_time')) . ' SECOND)');

$online = $db->query('SELECT COUNT(1) FROM online')->fetchColumn();

if ($online > Config::get('online_max')) {
    $q = $db->prepare('REPLACE INTO `setting` (`name`, `value`) VALUES (?, ?)');
    $q->execute(array('online_max', $online));
    $q->execute(array('online_max_time', $_SERVER['REQUEST_TIME']));
}
