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


require_once SEA_CORE_DIRECTORY . '/header.php';

if (!Config::get('cut_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}


$id = intval(Http_Request::get('id'));
$v = Files::getFileInfo($id);

if (!$v || !is_file($v['path'])) {
    Http_Response::getInstance()->renderError(Language::get('not_found'));
}

$v['info'] = Media_Audio::getInfo($id, $v['path']);
$cut = array();

Seo::unserialize($v['seo']);
//Seo::addTitle(Language::get('splitting'));
//Seo::addTitle($v['name']);

Http_Response::getInstance()->getTemplate()
    ->setTemplate('cut.tpl')
    ->assign('file', $v)
    ->assignByRef('cut', $cut);

Breadcrumbs::init($v['path']);
Breadcrumbs::add('cut/' . $id, Language::get('splitting'));


if (Http_Request::isPost()) {
    $s = intval(Http_Request::post('s', 0));
    $p = intval(Http_Request::post('p', 0));

    $way = Http_Request::post('way');
    if ($way && $way != 'size' && $way != 'time') {
        Http_Response::getInstance()->renderError(Language::get('error'));
    }

    $allsize = 0;
    foreach (glob(Config::get('mp3path') . '/*') as $string) {
        $allsize += round(filesize($string) / 1024 / 1024, 1);
        if ($allsize > Config::get('limit')) {
            $dir = opendir(Config::get('mp3path') . '/');
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') {
                    unlink(Config::get('mp3path') . '/' . $file);
                }
            }
            break;
        }
    }

    $randintval = Config::get('mp3path') . '/' . uniqid() . '_' . pathinfo($v['path'], PATHINFO_BASENAME);

    if (copy($v['path'], $randintval)) {
        $fp = fopen($randintval, 'rb');
        $raz = filesize($randintval);

        if ($way == 'size') {
            $s *= 1024;
            $p *= 1024;
            if ($s > $raz || $s < 0) {
                $s = 0;
            }
            if ($p > $raz || $p < $s) {
                $p = $raz;
            }
        } else {
            //time
            //Todo:avgBitrate может быть плавающим
            $byterate = ($v['info']['avgBitrate'] ? $v['info']['avgBitrate'] : 128000) / 8;
            $secbit = $raz / 1024 / $byterate;
            if ($s > $secbit || $s < 0) {
                $s = 0;
            }
            if ($p > $secbit || $p < $s) {
                $p = $secbit;
            }
            $s *= $byterate * 1024;
            $p *= $byterate * 1024;
        }

        $p -= $s;
        fseek($fp, $s);
        $filefp = fread($fp, $p);
        fclose($fp);
        unlink($randintval);

        $fp = fopen($randintval, 'xb');
        if (fwrite($fp, $filefp)) {
            $fp = fopen($randintval, 'rb');
            $ras = filesize($randintval);
            fclose($fp);

            Files::updateFileLoad($id);

            $cut = array(
                'link' => SEA_PUBLIC_DIRECTORY . $randintval,
                'size' => $ras,
            );
        } else {
            Http_Response::getInstance()->renderError(Language::get('error'));
        }
    } else {
        Http_Response::getInstance()->renderError(Language::get('error'));
    }
}


Http_Response::getInstance()->render();
