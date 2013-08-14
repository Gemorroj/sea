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


header('Content-Type: image/jpeg');
header('Pragma: public');
header('Cache-Control: public, max-age=8640000');
header('Expires: ' . date('r', $_SERVER['REQUEST_TIME'] + 8640000));

require 'core/config.php';


$id = intval($_REQUEST['id']);
$resize = true;
$marker = Config::get('marker');


$w = isset($_GET['w']) ? abs($_GET['w']) : 0;
$h = isset($_GET['h']) ? abs($_GET['h']) : 0;


if (!$w || !$h) {
    $resize = false;
    list($w, $h) = explode('*', Config::get('prev_size'));
} else {
    if ($marker) {
        $marker = ($marker == 2 ? 0 : 1);
    }
}

$v = getFileInfo($id);
$pic = $v['path'];
$prev_pic = str_replace('/', '--', mb_substr(strstr($pic, '/'), 1));

if ($resize) {
    $prev_pic = $w . 'x' . $h . '_' . $prev_pic;
    updFileLoad($id);
}

$location = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.gif';


if (!file_exists(Config::get('picpath') . '/' . $prev_pic . '.gif')) {
    if (!Image::resize($pic, Config::get('picpath') . '/' . $prev_pic . '.gif', $w, $h, $marker)) {
        Http_Response::getInstance()->renderError('Error');
    }
}

Http_Response::getInstance()->redirect($location, 301);
