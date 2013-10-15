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
require dirname(__FILE__) . '/../core/config.php';

$HeadTime = microtime(true);
$db = Db_Mysql::getInstance();

$info = $db->query('SELECT * FROM loginlog WHERE id = 1')->fetch();
$timeban = $_SERVER['REQUEST_TIME'] - $info['time'];
//-------------------------------
if ($timeban < Config::get('timeban')) {
    Http_Response::getInstance()->renderError('Следующая авторизация возможна через ' . (Config::get('timeban') - $timeban) . ' секунд!');
}
//-------------------------------
if ($info['access_num'] > Config::get('countban')) {
    $db->prepare('UPDATE loginlog SET time = ?, access_num = 0')->execute(array($_SERVER['REQUEST_TIME']));
    Http_Response::getInstance()->renderError(
        'Вы ' . Config::get('countban') . ' раза ввели неверный пароль. Вы заблокированы на ' . Config::get('timeban') . ' секунд'
    );
}
//-------------------------------
if (!Http_Request::post('p') && !Http_Request::get('p')) {
    Http_Response::getInstance()->setBody('<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
    <head>
        <meta name="viewport" content="width=device-width"/>
        <title>Админка - вход</title>
    </head>
    <body>
        <div>
            <fieldset>
                <legend>Вход для администратора</legend>
                <form method="post" action="./">
                    <div>
                        <label>Пароль: <input type="password" name="p" /></label><br/>
                        <input type="submit" value="Войти"/>
                    </div>
                </form>
            </fieldset>
        </div>
    </body>
</html>')->renderBinary();
}

if ((Http_Request::post('p') && md5(Http_Request::post('p')) == Config::get('password')) ||
    Config::get('autologin') && (Http_Request::get('p') && md5(Http_Request::get('p')) == Config::get('password'))) {
    $_SESSION['ipu'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['authorise'] = Config::get('password');

    $db->prepare('INSERT INTO loginlog SET time = ?, ua = ?, ip = ?')->execute(
        array(
             $_SERVER['REQUEST_TIME'],
             $_SERVER['HTTP_USER_AGENT'],
             $_SERVER['REMOTE_ADDR']
        )
    );

    Http_Response::getInstance()->redirect('http://' . $_SERVER['HTTP_HOST'] . str_replace(
        array('\\', '//'),
        '/',
        dirname($_SERVER['PHP_SELF']) . '/'
    ) . 'apanel.php');
} else {
    $db->exec('UPDATE loginlog SET access_num = access_num + 1 WHERE id = 1');
    Http_Response::getInstance()->renderError('Пароль введен неверно. Осталось попыток до блокировки: ' . (Config::get('countban') - $info['access_num']));
}
