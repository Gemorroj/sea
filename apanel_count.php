<?php
// mod Gemorroj


//error_reporting(0);
set_time_limit(99999);
ignore_user_abort(true);
//ob_end_flush();
ob_implicit_flush(1);


require 'moduls/config.php';
require 'moduls/header.php';


$HeadTime = microtime(true);


if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error($setup['hackmess']);
}
////////////////////////////


// получаем все папки
$res = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "1" GROUP BY `path`', $mysql);
while ($dir = mysql_fetch_row($res)) {
    // заглушка
    echo 'updated ' . htmlspecialchars($dir[0], ENT_NOQUOTES) . '...<br/>';
    ob_flush();

    $dir[0] = mysql_real_escape_string($dir[0], $mysql);
    // заносим данныев БД
    mysql_query('UPDATE `files` SET `dir_count` = ' . intval(mysql_result(mysql_query('SELECT COUNT(1) FROM `files` WHERE `infolder` LIKE "' . $dir[0] . '%" AND `hidden` = "0"', $mysql), 0)) . ' WHERE `path`="' . $dir[0] . '"', $mysql);
}
mysql_query('OPTIMIZE TABLE `files`', $mysql);

echo '<div class="mainzag">База данных успешно обновлена!</div><div class="row"><a href="apanel.php">Админка</a></div>';

require 'moduls/foot.php';

?>
