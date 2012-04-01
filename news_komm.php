<?php
// mod Gemorroj

require 'moduls/config.php';
require 'moduls/header.php';
###############Если комменты выключены##########
if (!$setup['komments_change']) {
	error('Not found');
}
###############Проверка#########################


$title .= $_SESSION['language']['comments'];

$id = intval($_GET['id']);
$page = intval($_GET['page']);
if ($page < 1) {
	$page = 1;
}

$onpage = get2ses('onpage');
is_num($onpage,'onpage');
if ($onpage < 3) {
    $onpage = 3;
}

$out = '';
###############Получаем комменты###############
$all = mysql_fetch_row(mysql_query('SELECT COUNT(1) FROM `news_komments` WHERE `id_news` = ' . $id, $mysql));
$all = $all[0];
$onpage = ($onpage > $all) ? $all : $onpage;
$start = ($onpage * $page) - $onpage;

$sql = mysql_query('SELECT * FROM `news_komments` WHERE `id_news` = ' . $id . ' ORDER BY `id` DESC LIMIT ' . $start . ', ' . $onpage, $mysql);


###############Запись###########################
if ($_GET['act'] == 'add') {
    //Проверка на ошибки
    $error = '';
    if (!$_POST['msg'] || !$_POST['name']) {
    	$error .= $_SESSION['language']['not filled one of the fields'] . '<br/>';
    }
    if (mb_strlen($_POST['msg']) < 4) {
    	$error .= $_SESSION['language']['you have not written a comment or he is too short'] . '<br/>';
    }
    if ($setup['komments_captcha']) {
    	if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']) {
    	    $error .= $_SESSION['language']['not a valid code'] . '<br/>';
    	}
    	unset($_SESSION['captcha_keystring']);
    }

    $_POST['msg'] = mysql_real_escape_string(nl2br(bbcode(htmlspecialchars(mb_substr($_POST['msg'], 0, 32512), ENT_NOQUOTES))), $mysql);
    $_POST['name'] = mysql_real_escape_string(mb_substr($_POST['name'], 0, 24), $mysql);


    if (mysql_fetch_row(mysql_query("SELECT 1 FROM `news_komments` WHERE `text` = '" . $_POST['msg'] . "' LIMIT 1", $mysql))) {
    	$error .= $_SESSION['language']['why repeat myself'] . '<br/>';
    }
    //Если нет ошибок пишем в базу
    if ($error) {
    	error($error);
    }

    mysql_query("INSERT INTO `news_komments` (`id_news`, `text`, `name`, `time`) VALUES (" . $id . ", '" . $_POST['msg'] . "', '" . $_POST['name'] . "', " . $_SERVER['REQUEST_TIME'] . ");", $mysql);

    $out .= '<div class="iblock">' . $_SESSION['language']['your comment has been successfully added'] . '</div>';
} else {
    $out .= '<div class="mblock"><strong>' . $_SESSION['language']['comments'] . '</strong></div>';
    //Страницы
    if ($onpage) {
        $pages = ceil($all / $onpage);
        if (!$pages) {
        	$pages = 1;
        }
    } else {
        $pages = 1;
    }

    //Если комментов пока нет
    if (!$all) {
    	$out .= '<div class="row">' . $_SESSION['language']['at the moment comments for this news does not'] . '</div>';
    }

    //Выводим комменты
    $bool = true;
    while ($komments = mysql_fetch_assoc($sql)) {
        $bool != $bool;

        if ($bool){
        	$out .= '<div class="row">';
        } else {
        	$out .= '<div class="mainzag">';
        }


        if (isset($_SESSION['autorise']) && $_SESSION['autorise'] == $setup['password']) {
            $out .= '<a href="' . DIRECTORY . 'apanel.php?news_komm=' . $komments['id'] . '&amp;action=del_news_komm" title="del">[X]</a> ';
        }

        $out .= '<strong>' . htmlspecialchars($komments['name'], ENT_NOQUOTES) . '</strong> (' . tm($komments['time']) . ')<br/>' . str_replace("\n", '<br/>', $komments['text']) . '</div>';
    }

    // капча
    if ($setup['komments_captcha']) {
    	$captcha = '<img alt="" src="' . DIRECTORY . 'moduls/kcaptcha/index.php?' . session_name() . '=' . session_id() . '" /><br/>' . $_SESSION['language']['code'] . '<input class="enter" type="text" name="keystring" size="4" maxlength="4"/><br/>';
    } else {
    	$captcha = '';
    }


    //Форма добавления камментов
    $out .= '<div class="iblock"><form action="' . DIRECTORY . 'news_komm/' . $id . '/1/add" method="post"><div class="row">' . $_SESSION['language']['your name'] . ':<br/><input class="enter" name="name" type="text" value="" maxlength="10"/><br/>' . $_SESSION['language']['message'] . ':<br/><textarea class="enter" cols="40" rows="5" name="msg"></textarea><br/>' . $captcha . '<br/><input class="buttom" type="submit" value="' . $_SESSION['language']['go'] . '"/></div></form></div>';


    //Страницы
    if ($pages > 1) {
        $out .= '</div><div class="iblock">' . $_SESSION['language']['pages'] . ': ';
        $asd = $page - 2;
        $asd2 = $page + 3;
        if ($asd < $all && $asd > 0 && $page > 3) {
        	$out .= ' <a href="' . DIRECTORY . 'news_komm/' . $id . '/1">1</a> ... ';
        }
        for ($i = $asd; $i < $asd2; ++$i) {
            if ($i < $all && $i > 0) {
                if ($i > $pages ) {
                	break;
                }
                if ($page == $i) {
                	$out .= '<strong>[' . $i . ']</strong> ';
                } else {
                	$out .= '<a href="' . DIRECTORY . 'news_komm/' . $id . '/' . $i . '">' . $i . '</a> ';
                }
            }
        }
        if ($i <= $pages) {
            if ($asd2 < $all) {
            	$out .= ' ... <a href="' . DIRECTORY . 'news_komm/' . $id . '/' . $pages . '">' . $pages . '</a>';
            }
        }
        $out .= '<br/>';
    }
}


echo $out . '<div class="iblock">- <a href="' . DIRECTORY . 'news/' . $id . '">' . $_SESSION['language']['news'] . '</a><br/>
- <a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/>
- <a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a><br/></div>';

require 'moduls/foot.php';



//Авточистка комментов
if ($all > $setup['klimit']) {
    mysql_query('DELETE FROM `news_komments` WHERE `id` = ' . mysql_result(mysql_query('SELECT MIN(`id`) FROM news_komments WHERE `id_news` = ' . $id, $mysql), 0), $mysql);
    $page = 1;
}

?>
