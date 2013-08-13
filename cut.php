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


require 'core/header.php';

// если нарезка отключена
if (!Config::get('cut_change')) {
    error('Not found!');
}


// Получаем инфу о файле
$v = getFileInfo($id);

if (!is_file($v['path'])) {
    error('File not found');
}
$v['info'] = getMusicInfo($id, $v['path']);
$cut = array();

$seo['title'] = $language['splitting'] . ' - ' . $v['name'];
$template->setTemplate('cut.tpl');
$template->assign('file', $v);
$template->assignByRef('cut', $cut);

$breadcrumbs = getBreadcrumbs($v, false);
$breadcrumbs['cut/' . $id] = $language['splitting'];
$template->assign('breadcrumbs', $breadcrumbs);


if ($_POST) {
    $s = isset($_POST['s']) ? intval($_POST['s']) : 0;
    $p = isset($_POST['p']) ? intval($_POST['p']) : 0;

    if (isset($_POST['way']) && $_POST['way'] != 'size' && $_POST['way'] != 'time') {
        error($language['error']);
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

        if ($_POST['way'] == 'size') {
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

            updFileLoad($id);

            $cut = array(
                'link' => DIRECTORY . $randintval,
                'size' => $ras,
            );
        } else {
            error($language['error']);
        }
    } else {
        error($language['error']);
    }
}


$template->send();
