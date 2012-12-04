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
 * @author  Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */


define('APANEL', true);
//set_time_limit(99999);
//ignore_user_abort(1);

chdir('../');
require 'moduls/config.php';
require 'moduls/header.php';

$HeadTime = microtime(true);


$error = false;

if ($_SESSION['authorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error('Error');
}
////////////////////////////

if (isset($_GET['action']) && $_GET['action'] == 'del') {
    if (!$_GET['level']) {
        echo 'Будут удалены все новости! Продолжить?<br/><a href="apanel_news.php?action=del&amp;level=1">Да, продолжить</a><br/>';
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

    $eng = mysql_real_escape_string($_POST['new']['english'], $mysql);
    $rus = mysql_real_escape_string($_POST['new']['russian'], $mysql);
    $aze = mysql_real_escape_string($_POST['new']['azerbaijan'], $mysql);
    $tur = mysql_real_escape_string($_POST['new']['turkey'], $mysql);

    mysql_query(
        "
        INSERT INTO `news` (
            `news`, `rus_news`, `aze_news`, `tur_news`, `time`
        ) VALUES (
            '" . $eng . "', '" . $rus . "', '" . $aze . "', '" . $tur . "', " . $_SERVER['REQUEST_TIME'] . "
        )",
        $mysql
    );

    if ($err = mysql_error($mysql)) {
        error('При добавлении новости произошла ошибка!<br/>' . $err);
    } else {
        echo '<div class="iblock">Новость успешно добавлена!</div>';
    }
}


echo '<div class="mblock"><a href="../news.php">Новости</a></div>
<div class="mblock"><a href="apanel_news.php?action=del">Очистка</a></div>
<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
<div class="row">Введите текст новости:</div><div class="row">';
echo Language::getInstance()->newsLangpacks();
echo '<input type="submit" value="Добавить"/>
</div>
</form>
<div class="row"><a href="apanel.php">Админка</a></div>';
