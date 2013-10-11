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

if (!extension_loaded('ffmpeg')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

$id = intval(Http_Request::get('id'));
$frame = abs(Http_Request::get('frame', (int)Config::get('ffmpeg_frame') + 1));
$i = $frame;

$v = Files::getFileInfo($id);
if (!$v || !is_file($v['path'])) {
    Http_Response::getInstance()->renderError(Language::get('not_found'));
}


$prev_pic = str_replace('/', '--', mb_substr(strstr($v['path'], '/'), 1));
$cache = Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . $frame . '.png';

if (substr($v['path'], 0, 1) != '.' && !is_file(Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . $frame . '.png')) {
    $mov = new ffmpeg_movie($v['path'], false);
    if (!$mov) {
        Http_Response::getInstance()->renderError(Language::get('error'));
    }

    while (!$fr = $mov->getFrame($i)) {
        $i--;
        if ($i <= 0) {
            copy(CORE_DIRECTORY . '/resources/video.png', $cache);
            break;
        }
    }

    if ($fr) {
        $tmp = CORE_DIRECTORY . '/tmp/' . uniqid('ffmpeg_') . '.png';
        imagepng($fr->toGDImage(), $tmp);
        Image::resize($tmp, $cache, 0, 0, Config::get('marker'));
        unlink($tmp);
    }
}


Http_Response::getInstance()
    ->setCache()
    ->redirect('http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $cache, 301);
