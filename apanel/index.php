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


define('APANEL', true);
require '../moduls/config.php';

$HeadTime = microtime(true);

$info = mysql_fetch_array(mysql_query('SELECT * FROM `loginlog` WHERE `id` = 1', $mysql));
$timeban = $_SERVER['REQUEST_TIME'] - $info['time'];
//-------------------------------
if ($timeban < $setup['timeban']) {
    include '../moduls/header.php';
    error('Следующая авторизация возможна через ' . ($setup['timeban'] - $timeban) . ' секунд!');
}
//-------------------------------
if ($info['access_num'] > $setup['countban']) {
    include '../moduls/header.php';
    $query = mysql_query('UPDATE `loginlog` SET `time` = ' . $_SERVER['REQUEST_TIME'] . ', `access_num` = 0 WHERE `id` = 1', $mysql);
    error('Вы ' . $setup['countban'] . ' раза ввели неверный пароль. Вы заблокированы на ' . $setup['timeban'] . ' секунд');
}
//-------------------------------
if (!isset($_POST['p']) && !isset($_GET['p'])) {
    include '../moduls/header.php';
    echo '<div class="mblock">Вход для администратора:</div><form method="post" action="' . $_SERVER['PHP_SELF'] . '"><div class="row">Пароль:<br/><input class="enter" type="password" name="p"/><br/><input class="buttom" type="submit" value="Войти"/></div></form>';
    exit;
}

if ($setup['autologin'] && ((@$_POST['p'] && md5($_POST['p']) == $setup['password']) || (@$_GET['p'] && md5($_GET['p']) == $setup['password']))) {
    $_SESSION['ipu'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['autorise'] = $setup['password'];
    mysql_query("INSERT INTO `loginlog` (`ua`, `ip`, `time`) VALUES ('" . mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'], $mysql)."', '" . $_SERVER['REMOTE_ADDR'] . "', " . $_SERVER['REQUEST_TIME'] . ");", $mysql);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/') . 'apanel.php?' . session_name() . '=' . session_id());
} else {
    include '../moduls/header.php';
    mysql_query('UPDATE `loginlog` SET `access_num` = `access_num` + 1 WHERE `id` = 1', $mysql);
    error('Пароль введен неверно. Осталось попыток до блокировки: ' . ($setup['countban'] - $info['access_num']));
}
