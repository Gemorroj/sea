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

if (!Config::get('lib_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

$id = intval(Http_Request::get('id'));
$v = Files::getFileInfo($id);
if (!$v || !is_file($v['path'])) {
    Http_Response::getInstance()->renderError(Language::get('not_found'));
}

$paginatorConf = Helper::getPaginatorConf(PHP_INT_MAX);

$template = Http_Response::getInstance()->getTemplate();
$template->setTemplate('read.tpl');


// Директория
$q = Db_Mysql::getInstance()->prepare('SELECT *, ' . Language::buildFilesQuery() . ' FROM `files` WHERE `path` = ? LIMIT 1');
$q->execute(array($v['infolder']));
$directory = $q->fetch();

$template->assign('directory', $directory);

Breadcrumbs::init($v['path']);
Breadcrumbs::add('read/' . $id, Language::get('read'));

Seo::unserialize($v['seo']);
//Seo::addTitle($v['name']);
//Seo::addTitle(Language::get('read'));
Seo::addTitle($paginatorConf['page']);

$lib = isset($_SESSION['lib']) ? $_SESSION['lib'] : Config::get('lib');

// сколько всего страниц
$paginatorConf['pages'] = ceil(filesize($v['path']) / $lib);
if ($paginatorConf['page'] > $paginatorConf['pages']) {
    $paginatorConf['page'] = 1;
}

// Читаем побайтово
$fp = fopen($v['path'], 'rb');
if ($paginatorConf['page'] > 1) {
    fseek($fp, $paginatorConf['page'] * $lib - $lib);
}
$content = fread($fp, $lib) . fgets($fp, 512);
fclose($fp);

// Чистим от некорректно порезанного UTF-8
if ($paginatorConf['page'] == 1) {
    $content = Helper::rtrimLibText($content);
} else if ($paginatorConf['page'] >= $paginatorConf['pages']) {
    $content = Helper::ltrimLibText($content);
} else {
    $content = Helper::trimLibText($content);
}

// Конвертируем в UTF-8
$content = Helper::str2utf8($content);

// trim только после конвертирования в UTF-8
$content = trim($content);

$template->assign('content', $content);
$template->assign('file', $v);
$template->assign('paginatorConf', $paginatorConf);
Http_Response::getInstance()->render();
