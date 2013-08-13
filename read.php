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

// если библиотека отключена
if (!Config::get('lib_change')) {
    error('Not found');
}


// Получаем инфу о файле
$v = getFileInfo($id);
if (!is_file($v['path'])) {
    error('File not found');
}

$paginatorConf = getPaginatorConf(PHP_INT_MAX);


$template->setTemplate('read.tpl');


// Директория
$q = Mysqldb::getInstance()->prepare('SELECT *, ' . Language::buildFilesQuery() . ' FROM `files` WHERE `path` = ? LIMIT 1');
$q->execute(array($v['infolder']));
$directory = $q->fetch();

$template->assign('directory', $directory);

$breadcrumbs = getBreadcrumbs($v, false);
$breadcrumbs['read/' . $id] = Language::get('read');
$template->assign('breadcrumbs', $breadcrumbs);


$seo = unserialize($v['seo']);
if (!$seo['title']) {
    $seo['title'] = $v['name'];
}
$seo['title'] .= ' - ' . Language::get('read') . ' / ' . $paginatorConf['page'];


$lib = isset($_SESSION['lib']) ? $_SESSION['lib'] : Config::get('lib');


// UTF-8
$fp = fopen($v['path'], 'rb');
if ($paginatorConf['page'] > 1) {
    fseek($fp, $paginatorConf['page'] * $lib - $lib);
}
$content = fread($fp, $lib) . fgets($fp, 1024);
fclose($fp);

if ($paginatorConf['page'] > 1) {
    $i = 0;
    foreach (str_split($content, 1) as $f) {
        if ($f == ' ' || $f == "\n" || $f == "\r" || $f == "\t") {
            break;
        }
        $i++;
    }
    $content = substr($content, $i);
}

$content = Helper::str2utf8($content);
$paginatorConf['pages'] = ceil(filesize($v['path']) / $lib);
if ($paginatorConf['page'] > $paginatorConf['pages']) {
    $paginatorConf['page'] = 1;
}


$template->assign('content', $content);
$template->assign('file', $v);
$template->assign('paginatorConf', $paginatorConf);
$template->send();
