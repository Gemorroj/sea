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


//error_reporting(0);
set_time_limit(9999);
header('Content-type: text/html; charset=utf-8');
require 'core/config.php';


echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
<title>Updater</title>
</head>
<body>
<div>';


$act = isset($_GET['act']) ? $_GET['act'] : '';
switch ($act) {
    default:
        echo '<fieldset>
        <legend>Перенос описаний из БД в файлы</legend>
        <a href="' . $_SERVER['PHP_SELF'] . '?act=about">Перенести</a>
        </fieldset>
        <fieldset>
        <legend>Рассортировка описаний из 1 папки в требуемую структуру</legend>
        <form action="' . $_SERVER['PHP_SELF'] . '?act=sort_about" method="post">
        <div>
        Директория с описаниями:<br/><input type="text" name="about_folder" value="' . dirname(__FILE__) . '"/> <input type="submit"/>
        </div>
        </form>
        </fieldset>
        <fieldset>
        <legend>Рассортировка скриншотов из 1 папки в требуемую структуру</legend>
        <form action="' . $_SERVER['PHP_SELF'] . '?act=sort_screen" method="post">
        <div>
        Директория со скриншотами:<br/><input type="text" name="screen_folder" value="' . dirname(__FILE__) . '"/> <input type="submit"/>
        </div>
        </form>
        </fieldset>';
        break;


    case 'about':
        $q = mysql_query('SELECT `dir`, `path`, `about` FROM `files`', $mysql);

        $i = 0;
        while ($file = mysql_fetch_assoc($q)) {
            $about = mb_substr($file['path'], mb_strlen($setup['path']));

            if ($file['dir']) {
                mkdir($setup['opath'] . mb_substr($about, 0, -mb_strlen(strrchr($about, '/'))), 0777, true);
            }

            if (trim($file['about'])) {
                if (file_put_contents($setup['opath'] . $about . '.txt', $file['about'])) {
                    $i++;
                } else {
                    echo 'Error - ' . $about . '<br/>';
                }
            }
        }
        echo 'Готово. Создано <strong>' . $i . '</strong> файлов описаний';
        break;


    case 'sort_about':
        $i = 0;
        foreach (scandir($_POST['about_folder']) as $f) {
            $ext = strtolower(pathinfo($_POST['about_folder'] . '/' . $f, PATHINFO_EXTENSION));
            if ($ext == 'txt' && is_file($_POST['about_folder'] . '/' . $f)) {
                $name = substr($f, 0, -4); // отрезаем .txt
                $path = mysql_query(
                    'SELECT `path` FROM `files` WHERE SUBSTR(`path`, -' . mb_strlen($name) . ') = "'
                        . mysql_real_escape_string($name, $mysql) . '"'
                );
                if (mysql_num_rows($path)) {

                    $about = mysql_result($path, 0);
                    $about = mb_substr($about, mb_strlen($setup['path']));

                    mkdir($setup['opath'] . mb_substr($about, 0, -mb_strlen(strrchr($about, '/'))), 0777, true);

                    if (rename($_POST['about_folder'] . '/' . $f, $setup['opath'] . $about . '.' . $ext)) {
                        $i++;
                    } else {
                        echo 'Error - ' . $f . '<br/>';
                    }

                }
            }
        }
        echo 'Готово. Перемещено <strong>' . $i . '</strong> файлов описаний';
        break;


    case 'sort_screen':
        $i = 0;
        foreach (scandir($_POST['screen_folder']) as $f) {
            $ext = strtolower(pathinfo($_POST['screen_folder'] . '/' . $f, PATHINFO_EXTENSION));
            if (($ext == 'gif' || $ext == 'jpg') && is_file($_POST['screen_folder'] . '/' . $f)) {

                $name = substr($f, 0, -4); // отрезаем расширение

                $path = mysql_query(
                    'SELECT `path` FROM `files` WHERE SUBSTR(`path`, -' . mb_strlen($name) . ') = "'
                        . mysql_real_escape_string($name, $mysql) . '"'
                );
                if (mysql_num_rows($path)) {

                    $about = mysql_result($path, 0);
                    $about = mb_substr($about, mb_strlen($setup['path']));

                    mkdir($setup['spath'] . mb_substr($about, 0, -mb_strlen(strrchr($about, '/'))), 0777, true);

                    if (rename($_POST['screen_folder'] . '/' . $f, $setup['spath'] . $about . '.' . $ext)) {
                        $i++;
                    } else {
                        echo 'Error - ' . $f . '<br/>';
                    }

                }
            }
        }
        echo 'Готово. Перемещено <strong>' . $i . '</strong> скриншотов';
        break;
}


echo '</div></body></html>';
