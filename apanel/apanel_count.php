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
set_time_limit(99999);
ignore_user_abort(true);
//ob_end_flush();
ob_implicit_flush(1);

chdir('../');
require 'moduls/config.php';
require 'moduls/header.php';


$HeadTime = microtime(true);


if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error($setup['hackmess']);
}
////////////////////////////


// получаем все папки
$res = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "1" GROUP BY `path`', $mysql);
while ($dir = mysql_fetch_row($res)) {
    // заглушка
    echo 'updated ' . htmlspecialchars($dir[0], ENT_NOQUOTES) . '...<br/>';
    ob_flush();

    $dir[0] = mysql_real_escape_string($dir[0], $mysql);
    // заносим данныев БД

    mysql_query(
        'UPDATE `files` SET `dir_count` = ' . intval(
            mysql_result(
                mysql_query(
                    'SELECT COUNT(1) FROM `files` WHERE `infolder` LIKE "' . $dir[0] . '%" AND `hidden` = "0"',
                    $mysql
                ),
                0
            )
        ) . ' WHERE `path`="' . $dir[0] . '"',
        $mysql
    );
}
mysql_query('OPTIMIZE TABLE `files`', $mysql);

echo '<div class="mblock">База данных успешно обновлена!</div><div class="row"><a href="apanel.php">Админка</a></div>';
