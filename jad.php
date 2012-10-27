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
define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));

###############Если jad выключен##########
if (!$setup['jad_change']) {
    error('Not found');
}
###############Проверка переменных###############
$id = intval($_GET['id']);
###############Получаем инфу о файле###########
$d = mysql_fetch_row(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));

if (is_file($d[0])) {
    mysql_query('UPDATE `files` SET `loads` = `loads` + 1, `timeload` = ' . $_SERVER['REQUEST_TIME'] . ' WHERE `id` = ' . $id, $mysql);

    include 'moduls/PEAR/pclzip.lib.php';

    $zip = new PclZip($d[0]);
    $content = $zip->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);

    header('Content-type: text/vnd.sun.j2me.app-descriptor');
    header('Content-Disposition: attachment; filename="' . rawurlencode(basename($d[0])) . '.jad";');

    echo trim($content[0]['content']) . "\n" .
        'MIDlet-Jar-Size: ' . filesize($d[0]) . "\n" .
        'MIDlet-Jar-URL: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $d[0];
} else {
    error($setup['hackmess']);
}
