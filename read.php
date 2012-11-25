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

// если библиотека отключена
if (!$setup['lib_change']) {
    error('Not found');
}


// Получаем инфу о файле
$v = getFileInfo($id);
if (!is_file($v['path'])) {
    error('File not found');
}

// страница
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}


$template->setTemplate('read.tpl');


$sql_dir = mysql_real_escape_string($v['infolder'], $mysql);
// Директория
$directory = mysql_fetch_assoc(mysql_query('SELECT *, ' . Language::getInstance()->buildFilesQuery() . ' FROM `files` WHERE `path` = "' . $sql_dir . '" LIMIT 1', $mysql));
$template->assign('directory', $directory);
$template->assign('breadcrumbs', array(
  $directory['id'] => $directory['name'],
  'view/' . $id => $v['name'],
  'read/' . $id => $language['read']
));


$seo = unserialize($v['seo']);
if (!$seo['title']) {
    $seo['title'] = $v['name'];
}
$seo['title'] .= ' - ' . $language['read'] . ' / ' . $page;



if (isset($_SESSION['lib'])) {
    $setup['lib'] = $_SESSION['lib'];
}


// UTF-8
$fp = fopen($v['path'], 'rb');
if ($page > 1) {
    fseek($fp, $page * $setup['lib'] - $setup['lib']);
}
$content = fread($fp, $setup['lib']) . fgets($fp, 1024);
fclose($fp);

if ($page > 1) {
    $i = 0;
    foreach (str_split($content, 1) as $f) {
        if ($f == ' ' || $f == "\n" || $f == "\r" || $f == "\t") {
            break;
        }
        $i++;
    }
    $content = substr($content, $i);
}

$content = str_to_utf8($content);
$pages = ceil(filesize($v['path']) / $setup['lib']);
if ($page > $pages) {
    $page = 1;
}


$template->assign('content', $content);
$template->assign('file', $v);
$template->assign('page', $page);
$template->assign('pages', $pages);
$template->send();
