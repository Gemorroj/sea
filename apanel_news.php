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


if (isset($_POST['new'])) {
    foreach ($_POST['new'] as $k => $v) {
        if ($v == '') {
            error('Введите текст новости на ' . htmlspecialchars($k, ENT_NOQUOTES));
        }
    }

    $eng = mysql_real_escape_string(bbcode(htmlspecialchars($_POST['new']['english'], ENT_NOQUOTES)), $mysql);
    $rus = mysql_real_escape_string(bbcode(htmlspecialchars($_POST['new']['russian'], ENT_NOQUOTES)), $mysql);
    $aze = mysql_real_escape_string(bbcode(htmlspecialchars($_POST['new']['azerbaijan'], ENT_NOQUOTES)), $mysql);
    $tur = mysql_real_escape_string(bbcode(htmlspecialchars($_POST['new']['turkey'], ENT_NOQUOTES)), $mysql);

    mysql_query("
        INSERT INTO `news` (
            `news`, `rus_news`, `aze_news`, `tur_news`, `time`
        ) VALUES (
            '" . $eng . "', '" . $rus . "', '" . $aze . "', '" . $tur . "', " . $_SERVER['REQUEST_TIME'] . "
        )", $mysql
    );

    if ($err = mysql_error($mysql)) {
        error('При добавлении новости произошла ошибка!<br/>' . $err);
    } else {
        echo '<div class="iblock">Новость успешно добавлена!</div>';
    }
}



echo '<div class="mblock"><a href="news.php">Новости</a></div>
<div class="mblock"><a href="apanel_news.php?action=del">Очистка</a></div>
<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
<div class="row">Введите текст новости:</div><div class="row">';
echo Language::getInstance()->newsLangpacks();
echo '<input type="submit" value="Добавить"/>
</div>
</form>
<div class="row"><a href="apanel.php">Админка</a></div>';

require 'moduls/foot.php';

?>
