<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#             	 Автор  :  Sea                        #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#   		По всем вопросам пишите в ICQ.            #
#-----------------------------------------------------#

// mod Gemorroj


require 'moduls/config.php';

$HeadTime = microtime(true);

$info = mysql_fetch_array(mysql_query('SELECT * FROM `loginlog` WHERE `id` = 1', $mysql));
$timeban = $_SERVER['REQUEST_TIME'] - $info['time'];
//-------------------------------
if ($timeban < $setup['timeban']) {
	include 'moduls/header.php';
	error('Следующая авторизация возможна через ' . ($setup['timeban'] - $timeban) . ' секунд!');
}
//-------------------------------
if ($info['access_num'] > $setup['countban']) {
	include 'moduls/header.php';
	$query = mysql_query('UPDATE `loginlog` SET `time` = ' . $_SERVER['REQUEST_TIME'] . ', `access_num` = 0 WHERE `id` = 1', $mysql);
	error('Вы ' . $setup['countban'] . ' раза ввели неверный пароль. Вы заблокированы на ' . $setup['timeban'] . ' секунд');
}
//-------------------------------
if (!isset($_POST['p']) && !isset($_GET['p'])) {
    include 'moduls/header.php';
    echo '<div class="mainzag">Вход для администратора:</div><form method="post" action="' . $_SERVER['PHP_SELF'] . '"><div class="row">Пароль:<br/><input class="enter" type="password" name="p"/><br/><input class="buttom" type="submit" value="Войти"/></div></form>';
    include 'moduls/foot.php';
    exit;
}

if ($setup['autologin'] && ((@$_POST['p'] && md5($_POST['p']) == $setup['password']) || (@$_GET['p'] && md5($_GET['p']) == $setup['password']))) {
	$_SESSION['ipu'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['autorise'] = $setup['password'];
	mysql_query("INSERT INTO `loginlog` (`ua`, `ip`, `time`) VALUES ('" . mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'], $mysql)."', '" . $_SERVER['REMOTE_ADDR'] . "', " . $_SERVER['REQUEST_TIME'] . ");", $mysql);
	header('Location: http://' . $_SERVER['HTTP_HOST'] . str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/') . 'apanel.php?' . session_name() . '=' . session_id());
} else {
	include 'moduls/header.php';
	mysql_query('UPDATE `loginlog` SET `access_num` = `access_num` + 1 WHERE `id` = 1', $mysql);
	error('Пароль введен неверно. Осталось попыток до блокировки: ' . ($setup['countban'] - $info['access_num']));
}

?>
