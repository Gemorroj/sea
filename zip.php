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

if (!Config::get('zip_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

$id = intval(Http_Request::get('id'));
$v = Files::getFileInfo($id);
if (!$v || !is_file($v['path'])) {
    Http_Response::getInstance()->renderError(Language::get('not_found'));
}


$template = Http_Response::getInstance()->getTemplate();
$template->setTemplate('zip.tpl');

// Директория
$q = Db_Mysql::getInstance()->prepare('SELECT *, ' . Language::buildFilesQuery() . ' FROM `files` WHERE `path` = ? LIMIT 1');
$q->execute(array($v['infolder']));
$directory = $q->fetch();

$template->assign('directory', $directory);

Breadcrumbs::init($v['path']);
Breadcrumbs::add('zip/' . $id, Language::get('view_archive'));


Seo::unserialize($v['seo']);
//Seo::addTitle($v['name']);
//Seo::addTitle(Language::get('view_archive'));


$paginatorConf = array();
$zipFiles = array();
$size = 0;
$zipFileName = '';
$zipFileType = '';
$zipFileData = '';
$action = Http_Request::get('action');


$zip = new PclZip($v['path']);
if (!$zip) {
    Http_Response::getInstance()->renderError(Language::get('error'));
}


switch ($action) {
    case 'down':
        $zipFileName = rtrim(Http_Request::get('name'), '/');
        $mime = Helper::ext2mime(pathinfo($zipFileName, PATHINFO_EXTENSION));

        $f = $zip->extract(PCLZIP_OPT_BY_NAME, $zipFileName, PCLZIP_OPT_EXTRACT_AS_STRING);
        if ($mime == 'text/plain') {
            Http_Response::getInstance()->setBody(Helper::str2utf8($f[0]['content']));
        } else {
            Http_Response::getInstance()->setBody($f[0]['content']);
        }
        Http_Response::getInstance()
            ->setHeader('Content-Type', $mime)
            ->setCache()
            ->renderBinary();
        break;

    case 'preview':
        $zipFileName = rtrim(Http_Request::get('name'), '/');
        Seo::addTitle($zipFileName);

        $ext = pathinfo($zipFileName, PATHINFO_EXTENSION);
        $mime = Helper::ext2mime($ext);

        if (Media_Image::isSupported($ext)) {
            $f = Config::get('zppath') . '/' . str_replace(
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
        } elseif ($mime == 'text/plain') {
            $content = $zip->extract(PCLZIP_OPT_BY_NAME, $zipFileName, PCLZIP_OPT_EXTRACT_AS_STRING);
            $content = Helper::str2utf8($content[0]['content']);

            $paginatorConf = Helper::getPaginatorConf(PHP_INT_MAX);
            $paginatorConf['pages'] = ceil(mb_strlen($content) / Config::get('lib'));

            $content = mb_substr($content, $paginatorConf['page'] * Config::get('lib') - Config::get('lib'), (int)Config::get('lib') + 64);

            if ($paginatorConf['page'] > 1) {
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
            Http_Response::getInstance()->renderMessage(Language::get('file_unavailable_for_viewing'));
        }
        break;

    default:
        if (!($list = $zip->listContent())) {
            Http_Response::getInstance()->renderError(Language::get('error'));
        }
        $paginatorConf = Helper::getPaginatorConf(sizeof($list));

        for ($i = ($paginatorConf['page'] - 1) * $paginatorConf['onpage'], $end = $paginatorConf['page'] * $paginatorConf['onpage']; $i < $end; ++$i) {
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
$template->assign('allItemsSize', $size);
$template->assign('paginatorConf', $paginatorConf);
Http_Response::getInstance()->render();
