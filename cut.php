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
require 'moduls/PEAR/MP3/Id.php';
require 'moduls/header.php';

###############Если нарезка выключенa##########
if (!$setup['cut_change']) {
    error('Not found!');
}
###############Проверка переменных#############

$id = intval($_GET['id']);
$s = isset($_POST['s']) ? intval($_POST['s']) : 0;
$p = isset($_POST['p']) ? intval($_POST['p']) : 0;

if (isset($_POST['way']) && $_POST['way'] != 'size' && $_POST['way'] != 'time') {
    error($setup['hackmess']);
}

$title .= $language['splitting'];


###############Получаем инфу о файле###########
$file_info = mysql_fetch_assoc(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));
if (!is_file($file_info['path'])) {
    error('Not found!');
}
#######Получаем имя файла и обратный каталог#####
$filename = pathinfo($file_info['path']);
$ext = $filename['extension'];
$dir = $filename['dirname'] . '/';
$filename = $filename['basename'];
$back = mysql_fetch_assoc(mysql_query("SELECT * FROM `files` WHERE `path` = '" . mysql_real_escape_string($dir, $mysql) . "'", $mysql));
//------------------------------------------------------------------------------------------
if (!isset($_POST['a']) || ($s < 1 && $p < 1)) {
    $id3 = new MP3_Id();
    $result = $id3->read($file_info['path']);
    $result = $id3->study();
    // ------------------------Форма ввода параметров---------------------------
    echo '<div class="mblock">' . $language['splitting'] . '</div><div class="iblock">
' . $language['size'] . ': ' . round(($id3->getTag('filesize') / 1024), 0) . ' Kb<br/>
' . $language['length'] . ': ' . $id3->getTag('lengths') . ' ' . $language['sec'] . '</div><div class="row">
<form action="' . DIRECTORY . 'cut/' . $id . '" method="post">
<div class="row">
' . $language['method slicing'] . ':<br/>
<select class="enter" name="way">
<option value="size">' . $language['size'] . '</option>
<option value="time">' . $language['time'] . '</option>
</select><br/>
' . $language['start slicing'] . ':<br/>
<input maxlength="5" class="enter" type="text" name="s"/><br/>
' . $language['stop slicing'] . ':<br/>
<input maxlength="5" class="enter" type="text" name="p"/><br/>
<input class="buttom" type="submit" name="a" value="' . $language['go'] . '"/>
</div>
</form></div>';
} else {

    $list = glob($setup['mp3path'] . '/*');
    $all = sizeof($list);
    $allsize = 0;
    foreach ($list as $key => $string) {
        $allsize += round(filesize($string) / 1024 / 1024, 1);
        if ($allsize > $setup['limit']) {
            $dire = opendir($setup['mp3path'] . '/');
            while (($file = readdir($dire)) !== false) {
                if ($file != '.' && $file != '..'){
                    unlink($setup['mp3path'] . '/' . $file);
                }
            }
            break;
        }
    }

    $randname = mt_rand(10000, mt_getrandmax());
    $randintval = $setup['mp3path'] . '/' . $randname . '_' . $filename;
    if (copy($file_info['path'], $randintval)) {
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
            $id3 = new MP3_Id();
            $result = $id3->read($randintval);
            $result = $id3->study();
            $byterate = $id3->getTag('bitrate') / 8;
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
            $ras = round(filesize($randintval) / 1024);
            fclose($fp);
            $all++;

            mysql_query('UPDATE `files` SET `loads`=`loads` + 1, `timeload` = "' . $_SERVER['REQUEST_TIME'] . '" WHERE `id` = ' . $id, $mysql);

            echo '<div class="mblock">' . $language['the file has been successfully cut'] . '</div><div class="row"><strong><a href="' . DIRECTORY . $randintval . '">' . $language['download'] . '</a> (' . $ras . ' kb)</strong><br/><input class="enter" type="text" name="link" value="http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $setup['mp3path'] . '/' . $randname . '_' . rawurlencode($filename) . '"/></div>';
        } else {
            echo '<div class="iblock">' . $language['error'] . '</div>';
        }
    } else{
        echo '<div class="mblock">' . $language['error'] . '</div>';
    }
}
echo '<div class="iblock">
- <a href="' . DIRECTORY . 'view/' . $id . '">' . $language['go to the description of the file'] . '</a><br/>
- <a href="' . DIRECTORY . '/' . $back['id'] . '">' . $language['go to the category'] . '</a><br/>
- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>
- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a>
</div>';
