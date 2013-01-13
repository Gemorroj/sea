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


//error_reporting(0);

define('CORE_DIRECTORY', dirname(__FILE__));
if (defined('APANEL') === true) {
    define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname(dirname($_SERVER['PHP_SELF'])) . '/'));
} else {
    define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));
}

mb_internal_encoding('UTF-8');

ini_set('session.use_trans_sid', '0');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.save_path', CORE_DIRECTORY . '/tmp');
ini_set('session.session.cookie_path', DIRECTORY);

session_name('sea');
session_start() or die('Can not start session');


set_include_path(
    get_include_path() . PATH_SEPARATOR .
        CORE_DIRECTORY . DIRECTORY_SEPARATOR . 'PEAR'
);

require_once CORE_DIRECTORY . '/Smarty/libs/Smarty.class.php';
require_once CORE_DIRECTORY . '/classes/Template.php';
require_once CORE_DIRECTORY . '/classes/MysqlDb.php';

require_once CORE_DIRECTORY . '/classes/functions.php';
require_once CORE_DIRECTORY . '/classes/Language.php';


// данные для соединения с БД
MysqlDb::setOptions(array(
    'host' => '127.0.0.1',
    'username' => 'mysql',
    'password' => 'mysql',
    'dbname' => 'sea3',
));
$mysqldb = MysqlDb::getInstance();

$setup = array();
foreach ($mysqldb->query('SELECT name, value FROM setting') as $set) {
    $setup[$set['name']] = $set['value'];
}

define('IS_ADMIN', (isset($_SESSION['authorise']) && $_SESSION['authorise'] == $setup['password']));

// Подключаем модуль партнерки
//require CORE_CORE_DIRECTORY '/../partner/inc.php';
