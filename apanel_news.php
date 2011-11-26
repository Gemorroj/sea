<?php
// mod Gemorroj

//error_reporting(0);
//set_time_limit(99999);
//ignore_user_abort(1);

require 'moduls/config.php';
require 'moduls/header.php';

$HeadTime = microtime(true);


$error = false;

if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error($setup['hackmess']);
}
////////////////////////////

if ($_GET['action'] == 'del') {
	if (!$_GET['level']) {
		echo 'Будут удалены все новости! Продолжить?<br/><a href="apanel_news.php?action=del&amp;level=1">Да, продолжить</a><br/>';
		require 'moduls/foot.php';
		exit;
	} else {
		if (mysql_query('TRUNCATE TABLE `news`;', $mysql)) {
			echo 'База данных новостей очищена.<br/>';
		} else {
			error('Ошибка: ' . mysql_error($mysql));
		}
	}
}


if (trim($_POST['news']) && trim($_POST['rus_news'])) {
	$news = mysql_real_escape_string(bbcode(htmlspecialchars($_POST['news'], ENT_NOQUOTES)), $mysql);
	$rus_news = mysql_real_escape_string(bbcode(htmlspecialchars($_POST['rus_news'], ENT_NOQUOTES)), $mysql);
	
	mysql_query("INSERT INTO `news` VALUES(0,'" . $news . "','" . $rus_news . "'," . $_SERVER['REQUEST_TIME'] . ")", $mysql);
	
	if ($err = mysql_error($mysql)) {
		error('При добавлении новости произошла ошибка!<br/>' . $err);
	} else {
		echo '<div class="iblock">Новость успешно добавлена!</div>';
	}
}


echo '<div class="mblock"><a href="news.php">Новости</a></div>
<div class="mblock"><a href="apanel_news.php?action=del">Очистка</a></div>
<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
<div class="row">
Новость на Английском<br/>
<textarea name="news" rows="4" cols="64"></textarea><br/>
Новость на Русском<br/>
<textarea name="rus_news" rows="4" cols="64"></textarea><br/>
<input type="submit" value="Добавить"/>
</div>
</form>
<div class="row"><a href="apanel.php">Админка</a></div>';

require 'moduls/foot.php';

?>
