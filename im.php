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


require 'core/config.php';

$id = intval(Http_Request::get('id') ? Http_Request::get('id') : Http_Request::post('id'));

$v = Files::getFileInfo($id);
if (!$v || !is_file($v['path'])) {
    Http_Response::getInstance()->renderError(Language::get('not_found'));
}

$w = abs(Http_Request::get('w', 0));
$h = abs(Http_Request::get('h', 0));

$marker = Config::get('marker');
$resize = true;
if (!$w || !$h) {
    $resize = false;
    list($w, $h) = explode('*', Config::get('prev_size'));
} elseif ($marker) {
    $marker = ($marker == 2 ? 0 : 1);
}


$prev_pic = str_replace('/', '--', mb_substr(strstr($v['path'], '/'), 1));

if ($resize) {
    $prev_pic = $w . 'x' . $h . '_' . $prev_pic;
    Files::updateFileLoad($id);
}

$cache = Config::get('picpath') . '/' . $prev_pic . '.png';
if (!is_file($cache)) {
    if (!Image::resize($v['path'], $cache, $w, $h, $marker)) {
        Http_Response::getInstance()->renderError(Language::get('error'));
    }
}

Http_Response::getInstance()
    ->setCache()
    ->redirect('http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $cache, 301);
