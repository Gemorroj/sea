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


require 'moduls/header.php';
require 'moduls/PEAR/pclzip.lib.php';


// если просмотр zip
if (!$setup['zip_change']) {
    error('Not found');
}


// Получаем инфу о файле
$v = getFileInfo($id);
if (!is_file($v['path'])) {
    error('File not found');
}


$template->setTemplate('zip.tpl');


$sql_dir = mysql_real_escape_string($v['infolder'], $mysql);
// Директория
$directory = mysql_fetch_assoc(mysql_query('SELECT *, ' . Language::getInstance()->buildFilesQuery() . ' FROM `files` WHERE `path` = "' . $sql_dir . '" LIMIT 1', $mysql));
$template->assign('directory', $directory);
$template->assign('breadcrumbs', array(
    $directory['id'] => $directory['name'],
    'view/' . $id => $v['name'],
    'read/' . $id => $language['view_archive']
));

$onpage = get2ses('onpage');
if ($onpage < 3) {
    $onpage = $setup['onpage'];
}
$page = isset($_GET['page']) ? abs($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}


$seo = unserialize($v['seo']);
if (!$seo['title']) {
    $seo['title'] = $v['name'];
}
$seo['title'] .= ' - ' . $language['view_archive'];


$zipFiles = array();
$all = 0;
$size = 0;
$pages = 0;
$zipFileName = '';
$zipFileType = '';
$zipFileData = '';
$action = isset($_GET['action']) ? $_GET['action'] : null;


$zip = new PclZip($v['path']);
if (!$zip) {
    error('Can not open archive');
}


switch ($action) {
    case 'down':
        $zipFileName = rtrim($_GET['name'], '/');

        $mime = ext_to_mime(pathinfo($zipFileName, PATHINFO_EXTENSION));
        header('Content-Type: ' . $mime);
        if ($mime == 'text/plain') {
            $f = $zip->extract(PCLZIP_OPT_BY_NAME, $zipFileName, PCLZIP_OPT_EXTRACT_AS_STRING);
            echo str_to_utf8($f[0]['content']);
        } else {
            $zip->extract(PCLZIP_OPT_BY_NAME, $zipFileName, PCLZIP_OPT_EXTRACT_IN_OUTPUT);
        }
        exit;
        break;

    case 'preview':
        $zipFileName = rtrim($_GET['name'], '/');
        $seo['title'] .= ' - ' . $language['view_archive'] . ' / ' . $zipFileName;

        $mime = ext_to_mime(pathinfo($zipFileName, PATHINFO_EXTENSION));

        if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/jpeg' || $mime == 'image/bmp') {
            $f = $setup['zppath'] . '/' . str_replace(
                '/',
                '--',
                mb_substr(strstr($v['path'], '/'), 1) . '_' . strtolower($zipFileName)
            );
            if (!file_exists($f)) {
                $content = $zip->extract(PCLZIP_OPT_BY_NAME, $zipFileName, PCLZIP_OPT_EXTRACT_AS_STRING);
                file_put_contents($f, $content[0]['content']);
            }

            $zipFileType = 'image';
            $zipFileData = DIRECTORY . $f;
        } else if ($mime == 'text/plain') {
            $content = $zip->extract(PCLZIP_OPT_BY_NAME, $zipFileName, PCLZIP_OPT_EXTRACT_AS_STRING);
            $content = str_to_utf8($content[0]['content']);

            $pages = floor(mb_strlen($content) / $setup['lib']);
            $content = mb_substr($content, $page * $setup['lib'] - $setup['lib'], $setup['lib'] + 64);

            if ($page > 1) {
                $i = 0;
                foreach (str_split($content) as $val) {
                    if ($val == ' ' || $val == "\n" || $val == "\r" || $val == "\t") {
                        break;
                    }
                    $i++;
                }
                $content = substr($content, $i);
            }

            $zipFileType = 'text';
            $zipFileData = $content;
        } else {
            message($language['file_unavailable_for_viewing']);
        }
        break;

    default:
        if (!($list = $zip->listContent())) {
            error('Can not list archive');
        }

        $all = sizeof($list);
        $pages = ceil($all / $onpage);
        if ($pages < 1) {
            $pages = 1;
        }
        if ($page > $pages) {
            $page = 1;
        }


        for ($i = ($page - 1) * $onpage, $end = $page * $onpage; $i < $end; ++$i) {
            if (isset($list[$i]) && !$list[$i]['folder']) {
                $size += $list[$i]['size'];
                $zipFiles[] = $list[$i];
            }
        }
        break;
}


$template->assign('action', $action);
$template->assign('zipFileName', $zipFileName);
$template->assign('zipFileType', $zipFileType);
$template->assign('zipFileData', $zipFileData);
$template->assign('file', $v);
$template->assign('zipFiles', $zipFiles);
$template->assign('allItemsInDir', $all);
$template->assign('allItemsSize', $size);
$template->assign('page', $page);
$template->assign('pages', $pages);
$template->send();
