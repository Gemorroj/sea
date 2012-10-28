<?php
/**
 * Copyright (c) 2012, Gemorroj
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @author Sea, Gemorroj
 */
/**
 * Sea Downloads
 *
 * @author Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */


require 'moduls/config.php';
require 'moduls/header.php';
###############Если комменты выключены##########
if (!$setup['komments_change']) {
    error('Not found');
}

$title .= $language['comments'];
$out = '';

###############Проверка#########################

$id = intval($_GET['id']);
$page = intval($_GET['page']);
if ($page < 1) {
    $page = 1;
}

$onpage = get2ses('onpage');
if ($onpage < 3) {
    $onpage = 3;
}


###############Инфа о файле#####################
$file_info_real = mysql_fetch_assoc(mysql_query('SELECT `path`, `loads`, `seo` FROM `files` WHERE `id` = ' . $id, $mysql));
if (!file_exists($file_info_real['path'])) {
    error('File not found!');
}
$seo = unserialize($file_info_real['seo']);
###############Получаем комменты###############
$all = mysql_fetch_row(mysql_query('SELECT COUNT(1) FROM `komments` WHERE `file_id` = ' . $id, $mysql));
$all = $all[0];
$onpage = ($onpage > $all) ? $all : $onpage;
$start = ($onpage * $page) - $onpage;

$sql = mysql_query('
    SELECT *
    FROM `komments`
    WHERE `file_id` = ' . $id . '
    ORDER BY `id` DESC
    LIMIT ' . $start . ', ' . $onpage
, $mysql);


$filepath = pathinfo($file_info_real['path']);
$namefile = $filepath['basename'];


if ($seo['title']) {
    $title .= ' ' . htmlspecialchars($seo['title'], ENT_NOQUOTES);
} else {
    $title .= ' ' . htmlspecialchars($namefile, ENT_NOQUOTES);
}

#######Получаем имя файла и обратный каталог#####
$dir = $filepath['dirname'] . '/';
$back = mysql_fetch_assoc(mysql_query("
    SELECT `id`
    FROM `files`
    WHERE `path` = '" . mysql_real_escape_string($dir, $mysql) . "'
", $mysql));
###############Запись###########################
if ($_GET['act'] == 'add') {
    //Проверка на ошибки
    $error = '';
    if (!$_POST['msg'] || !$_POST['name']) {
        $error .= $language['not filled one of the fields'] . '<br/>';
    }
    if (mb_strlen($_POST['msg']) < 4) {
        $error .= $language['you have not written a comment or he is too short'] . '<br/>';
    }
    //if(empty($file_info_real['loads'])){
    //    $error .= $language['before the vote, download the file'].'<br/>';
    //}
    if ($setup['komments_captcha']) {
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']) {
            $error .= $language['not a valid code'] . '<br/>';
        }
        unset($_SESSION['captcha_keystring']);
    }

    $_POST['msg'] = mysql_real_escape_string(nl2br(bbcode(htmlspecialchars(mb_substr($_POST['msg'], 0, 32512), ENT_NOQUOTES))), $mysql);
    $_POST['name'] = mysql_real_escape_string(mb_substr($_POST['name'], 0, 32), $mysql);


    if (mysql_fetch_row(mysql_query("SELECT 1 FROM `komments` WHERE `text` = '" . $_POST['msg'] . "' LIMIT 1", $mysql))) {
        $error .= $language['why repeat myself'] . '<br/>';
    }
    //Если нет ошибок пишем в базу
    if ($error) {
        error($error);
    }
    mysql_query("
        INSERT INTO `komments` (
            `file_id`, `name`, `text`, `time`
        ) VALUES (
            " . $id . ", '" . $_POST['name'] . "', '" . $_POST['msg'] . "', " . $_SERVER['REQUEST_TIME'] . "
        )
    ", $mysql);

    $out .= '<div class="iblock">' . $language['your comment has been successfully added'] . '</div>';
} else {
    $out .= '<div class="mblock"><strong>' . $language['comments to the file'] . ' "' . htmlspecialchars($namefile, ENT_NOQUOTES) . '"</strong></div>';
    //Страницы

    $pages = $onpage ? ceil($all / $onpage) : 1;

    //Если комментов пока нет
    if (!$all) {
        $out .= '<div class="row">' . $language['at the moment comment for this file does not'] . '</div>';
    }
    //Выводим комменты

    $bool = true;
    while ($komments = mysql_fetch_assoc($sql)) {
        $bool != $bool;

        if ($bool) {
            $out .= '<div class="row">';
        } else {
            $out .= '<div class="row2">';
        }


        if (isset($_SESSION['autorise']) && $_SESSION['autorise'] == $setup['password']) {
            $out .= '<a href="' . DIRECTORY . 'apanel/apanel.php?komm=' . $komments['id'] . '&amp;action=del_komm" title="del">[X]</a> ';
        }

        $out .= '<strong>' . htmlspecialchars($komments['name'], ENT_NOQUOTES) . '</strong> (' . tm($komments['time']) . ')<br/>' . str_replace("\n", '<br/>', $komments['text']) . '</div>';
    }

    // капча
    if ($setup['komments_captcha']) {
        $captcha = '<img alt="" src="' . DIRECTORY . 'moduls/kcaptcha/index.php?' . session_name() . '=' . session_id() . '" /><br/>' . $language['code'] . '<input class="enter" type="text" name="keystring" size="4" maxlength="4"/><br/>';
    } else {
        $captcha = '';
    }

    //Форма добавления камментов
    $out .= '<div class="iblock"><form action="' . DIRECTORY . 'komm/' . $id . '/1/add" method="post"><div class="row">' . $language['your name'] . ':<br/><input class="enter" name="name" type="text" maxlength="10"/><br/>' . $language['message'] . ':<br/><textarea class="enter" cols="40" rows="5" name="msg"></textarea><br/>' . $captcha . '<br/><input class="buttom" type="submit" value="' . $language['go'] . '"/></div></form></div>';


    //Страницы
    if ($pages > 1) {
        $out .= '<div class="iblock">' . $language['pages'] . ': ';
        $asd = $page - 2;
        $asd2 = $page + 3;
        if($asd < $all && $asd > 0 && $page > 3) {
            $out .= ' <a href="' . DIRECTORY . 'komm/' . $id . '/1">1</a> ... ';
        }
        for ($i = $asd; $i < $asd2; ++$i) {
            if ($i < $all && $i > 0) {
                if ($i > $pages ) {
                    break;
                }
                if ($page == $i) {
                    $out .= '<strong>[' . $i . ']</strong> ';
                } else {
                    $out .= '<a href="' . DIRECTORY . 'komm/' . $id . '/' . $i . '">' . $i . '</a> ';
                }
            }
        }
        if ($i <= $pages) {
            if ($asd2 < $all) {
                $out .= ' ... <a href="' . DIRECTORY . 'komm/' . $id . '/' . $pages . '">' . $pages . '</a>';
            }
        }
        $out .= '<br/>';
    }
}


if ($back['id']) {
    $str = '- <a href="' . DIRECTORY . $back['id'] . '">' . $language['go to the category'] . '</a><br/>';
} else {
    $str = '';
}

echo $out . '<div class="iblock">- <a href="' . DIRECTORY . 'view/' . $id . '">' . $language['go to the description of the file'] . '</a><br/>' . $str . '- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a><br/></div>';



//Авточистка комментов
if ($all > $setup['klimit']) {
    mysql_query('
        DELETE FROM `komments`
        WHERE `id` = ' . mysql_result(mysql_query('SELECT MIN(`id`) FROM komments WHERE `file_id` = ' . $id, $mysql), 0)
    , $mysql);
}
