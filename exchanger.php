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


require 'moduls/header.php';


if (!$setup['exchanger_change']) {
    error('Not found');
}


$template->setTemplate('exchanger.tpl');

$seo['title'] = $language['upload_file'];

$folders = array();
$insertId = null;

if ($_POST) {
    if (!$_FILES['file'] || $_FILES['file']['error']) {
        error($language['when_downloading_a_file_error_occurred']);
    }

    $pathinfo = pathinfo($_FILES['file']['name']);

    $ext = explode(',', strtolower($setup['exchanger_extensions']));
    if (!in_array(strtolower($pathinfo['extension']), $ext)) {
        error($language['invalid_file_extension']);
    }

    if (!preg_match('/^' . $setup['exchanger_name'] . '+$/i', $pathinfo['filename'])) {
        error($language['not_a_valid_file_name']);
    }

    $path = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . intval($_POST['topath']) . ' AND `dir` = "1" AND `hidden` = "0"', $mysql), 0);

    if (!$path) {
        error($language['you_have_specified_the_correct_path_to_load']);
    }
    $pathname = $path . $_FILES['file']['name'];

    $q = mysql_query('SELECT 1 FROM `files` WHERE `path` = "' . mysql_real_escape_string($pathname, $mysql) . '"', $mysql);
    if (mysql_num_rows($q)) {
        error($language['file_with_this_name_already_exists']);
    }

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $pathname)) {
        error($language['an_error_occurred_while_copying_files']);
    }

    $q = mysql_query("
        INSERT INTO `files` (
            `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`, `hidden`
        ) VALUES (
            '" . mysql_real_escape_string($pathname, $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($path, $mysql) . "',
            " . filesize($pathname) . ",
            " . filectime($pathname) . ",
            '" . ($setup['exchanger_hidden'] ? 1 : 0) . "'
        )", $mysql
    );
    if (!$q) {
        unlink($pathname);
        error($language['error_writing_to_database']);
    }
    $insertId = mysql_insert_id($mysql);
    if (!$setup['exchanger_hidden']) {
        dir_count($path, true);
    }


    if (!$_FILES['screen']['error']) {
        $screen = $setup['spath'] . substr($pathname, strlen($setup['path'])) . '.gif';
        $image = getimagesize($_FILES['screen']['tmp_name']);
        switch ($image[2]) {
            case 1: // GIF
                img_resize($_FILES['screen']['tmp_name'], $screen, 0, 0, $setup['marker']);
                break;


            case 2: // JPEG
                $im = imagecreatefromjpeg($_FILES['screen']['tmp_name']);
                imagegif($im, $screen);
                imagedestroy($im);
                img_resize($_FILES['screen']['tmp_name'], $screen, 0, 0, $setup['marker']);
                break;


            case 3: //PNG
                $im = imagecreatefrompng($_FILES['screen']['tmp_name']);
                imagegif($im, $screen);
                imagedestroy($im);
                img_resize($_FILES['screen']['tmp_name'], $screen, 0, 0, $setup['marker']);
                break;
        }
    }

    if ($_POST['about']) {
        $about = $setup['opath'] . substr($pathname, strlen($setup['path'])) . '.txt';
        file_put_contents($about, nl2br(bbcode(htmlspecialchars(trim($_POST['about'])))));
    }

    if ($setup['exchanger_notice']) {
        mail(
            $setup['zakaz_email'],
            '=?utf-8?B?' . base64_encode('Новый файл') . '?=',
            'Загружен новый файл http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'apanel/apanel_view.php?id=' . $insertId . "\r\n" .
            'Браузер: ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n" .
            'IP: ' . $_SERVER['REMOTE_ADDR'],
            "From: robot@" . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
        );
    }
} else {
    $dirs = mysql_query('SELECT `id`, `path` FROM `files` WHERE `dir` = "1" AND `hidden` = "0"', $mysql);
    while ($item = mysql_fetch_assoc($dirs)) {
        $arr = explode('/', $item['path']);
        $all = sizeof($arr) - 1;
        $in = array();
        for ($i = 0; $i < $all; ++$i) {
            if ($i > 0) {
                $in[$i] = $in[$i - 1] . mysql_real_escape_string($arr[$i], $mysql) . '/';
            } else {
                $in[$i] = mysql_real_escape_string($arr[$i], $mysql) . '/';
            }
        }

        $names = '';
        $q = mysql_query('SELECT ' . Language::getInstance()->buildFilesQuery() . ' FROM `files` WHERE `path` IN ("' . implode('","', $in) . '")', $mysql);
        while ($arrn = mysql_fetch_assoc($q)) {
            $names .= $arrn['name'] . '/';
        }
        $folders[$item['id']] = $names;
    }
}

$template->assign('insertId', $insertId);
$template->assign('upload_max_filesize', ini_get('upload_max_filesize'));
$template->assign('folders', $folders);
$template->assign('breadcrumbs', array('exchanger' => $language['upload_file']));
require 'moduls/foot.php';
