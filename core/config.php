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


// внешняя директория с зц. по умолчанию корень сайта.
define('DIRECTORY', '/');

define('CORE_DIRECTORY', dirname(__FILE__));
define('PCLZIP_TEMPORARY_DIR', CORE_DIRECTORY . '/tmp/');

mb_internal_encoding('UTF-8');

ini_set('session.use_trans_sid', '0');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params(864000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
session_save_path(CORE_DIRECTORY . '/tmp');
session_name('sea');
session_start() or die('Can not start session');


// автозагрузчик классов
require_once CORE_DIRECTORY . '/classes/Autoload.php';
Autoload::init();

// данные для соединения с БД
Db_Mysql::init(array(
    'host' => 'localhost',
    'username' => 'mysql',
    'password' => 'mysql',
    'dbname' => 'sea3',
));

// Инициализируем конфигурацию
Config::init();
// Инициализируем прослойку над HTTP
Http_Request::init();
Http_Response::init(new Template());
// Инициализируем переводы
Language::init();

// Инициализируем маршрутизацию
Routing::init(array(
    '(?P<id>[0-9]*)' => 'index.php',
    '(?P<id>[0-9]+)/(?P<page>[0-9]+)' => 'index.php',
    'view/(?P<id>[0-9]+)' => 'view.php',
    'view_comments/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'view_comments.php',
    'news/*(?P<page>[0-9]*)' => 'news.php',
    'news_comments/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'news_comments.php',
    'rate/(?P<i>[0-9]+)' => 'rate.php',
    'search/*(?P<page>[0-9]*)' => 'search.php',
    'top/*(?P<page>[0-9]*)' => 'top.php',
    'new/*(?P<page>[0-9]*)' => 'new.php',
    'load/(?P<id>[0-9]+)' => 'load.php',
    'ffmpeg/(?P<id>[0-9]+)' => 'ffmpeg.php',
    'apic/(?P<id>[0-9]+)' => 'apic.php',
    'email/(?P<id>[0-9]+)' => 'email.php',
    'abuse/(?P<id>[0-9]+)' => 'abuse.php',
    'im/(?P<id>[0-9]+)' => 'im.php',
    'theme/(?P<id>[0-9]+)' => 'theme.php',
    'jar/(?P<id>[0-9]+)' => 'jar.php',
    'jad/(?P<id>[0-9]+)' => 'jad.php',
    'cut/(?P<id>[0-9]+)' => 'cut.php',
    'txt_zip/(?P<id>[0-9]+)' => 'txt_zip.php',
    'txt_jar/(?P<id>[0-9]+)' => 'txt_jar.php',
    'settings/*(?P<id>[0-9]*)' => 'settings.php',
    'stat/*(?P<id>[0-9]*)' => 'stat.php',
    'table/*(?P<id>[0-9]*)' => 'table.php',
    'exchanger/*(?P<id>[0-9]*)' => 'exchanger.php',
    'service/*(?P<id>[0-9]*)' => 'service.php',
    'kcaptcha' => 'kcaptcha.php',
    'rss' => 'rss.php',
    'read/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'read.php',
    'zip/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'zip.php',
    'zip/(?P<action>preview)/(?P<id>[0-9]+)/(?P<name>.+)/(?P<page>[0-9]*)' => 'zip.php',
    'zip/(?P<action>down)/(?P<id>[0-9]+)/(?P<name>.+)' => 'zip.php',
));


define('IS_ADMIN', (isset($_SESSION['authorise']) && $_SESSION['authorise'] == Config::get('password')));

// Подключаем модуль партнерки
//require CORE_CORE_DIRECTORY '/../partner/inc.php';
