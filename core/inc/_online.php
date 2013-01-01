<?php
$mysqldb = MysqlDb::getInstance();

$mysqldb->prepare('REPLACE INTO `online` (`ip`, `time`) VALUES (?, NOW())')->execute(array($_SERVER['REMOTE_ADDR']));
$mysqldb->exec('DELETE FROM `online` WHERE `time` < (NOW() - INTERVAL ' . intval($setup['online_time']) . ' SECOND)');

$online = $mysqldb->query('SELECT COUNT(1) FROM online')->fetchColumn();

if ($online > $setup['online_max']) {
    $q = $mysqldb->prepare('REPLACE INTO `setting` (`name`, `value`) VALUES (?, ?)');
    $q->execute(array('online_max', $online));
    $q->execute(array('online_max_time', $_SERVER['REQUEST_TIME']));
}
