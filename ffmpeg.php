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


if (!extension_loaded('ffmpeg')) {
    header('Content-Type: image/png');
    readfile(dirname(__FILE__) . '/moduls/marker.png');
    exit;
}

require 'moduls/config.php';
define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));

$id = intval($_GET['id']);
$frame = $i = $_GET['frame'] ? abs($_GET['frame']) : $setup['ffmpeg_frame'] + 1;

$pic = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql), 0);
$prev_pic = str_replace('/', '--', mb_substr(strstr($pic, '/'), 1));
$location = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame
    . '.gif';

if (substr($pic, 0, 1) != '.' && !is_file($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif')) {
    $mov = new ffmpeg_movie($pic, false);
    if (!$mov) {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'dis/load.png', true, 301);
        exit;
    }

    while (!$fr = $mov->getFrame($i)) {
        $i--;
        if ($i < 0) {
            exit;
        }
    }

    $tmp = DIR . '/cache/' . uniqid() . '.tmp';
    imagegif($fr->toGDImage(), $tmp);
    img_resize($tmp, $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif', 0, 0, $setup['marker']);
    unlink($tmp);
}

header('Location: ' . $location, true, 301);
