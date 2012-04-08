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

$id = intval($_REQUEST['id']);
$resize = true;
$marker = $setup['marker'];


if (isset($_POST['size'])) {
    list($w, $h) = explode('x', $_POST['size']);
    $w = abs($w);
    $h = abs($h);
} else {
    $w = abs(@$_REQUEST['W']);
    $h = abs(@$_REQUEST['H']);
}

if (!$w || !$h) {
    $resize = false;
    list($w, $h) = explode('*', $setup['prev_size']);
} else if ($marker) {
    $marker = ($marker == 2 ? 0 : 1);
}

$pic = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql), 0);
$prev_pic = str_replace('/', '--', mb_substr(strstr($pic, '/'), 1));

if ($resize) {
    $prev_pic = $w . 'x' . $h . '_' . $prev_pic;
    mysql_query('UPDATE `files` SET `loads`=`loads`+1, `timeload`=' . $_SERVER['REQUEST_TIME'] . ' WHERE `id`=' . $id, $mysql);
}

$location = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $setup['picpath'] . '/' . $prev_pic . '.gif';


if (!file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
    if (!img_resize($pic, $setup['picpath'] . '/' . $prev_pic . '.gif', $w, $h, $marker)) {
        error('Error');
    }
}

header('Location: ' . $location, true, 301);

?>
