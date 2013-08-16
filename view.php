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

// Получаем инфу о файле
$file = Files::getFileInfo($id);

if (!is_file($file['path'])) {
    Http_Response::getInstance()->renderError('File not found');
}

$template = Http_Response::getInstance()->getTemplate();
$template->setTemplate('view.tpl');


// Система голосований
$vote = null;
require 'core/inc/view/_vote.php';

// рейтинг
$rate = $file['yes'] + $file['no'];
$rate = $rate ? round($file['yes'] / $rate * 100, 0) : 50;


#######Получаем имя файла и обратный каталог#####
$filename = pathinfo($file['path']);
$ext = strtolower($filename['extension']);
$file['ext'] = $ext;
$dir = $filename['dirname'] . '/';
$basename = $filename['basename'];
Seo::unserialize($file['seo']);
Seo::addTitle($file['name']);

// данные по файлам
require 'core/inc/view/_file.php';


// Директория
$q = Db_Mysql::getInstance()->prepare('SELECT *, ' . Language::buildFilesQuery() . ' FROM `files` WHERE `path` = ? LIMIT 1');
$q->execute(array($file['infolder']));
$directory = $q->fetch();

// Всего комментариев
$q = Db_Mysql::getInstance()->prepare('SELECT COUNT(1) FROM `comments` WHERE `file_id` = ?');
$q->bindValue(1, $id, PDO::PARAM_INT);
$q->execute();
$commentsCount = $q->fetchColumn();

// Последние комментарии
$comments = array();
require 'core/inc/view/_comments.php';

// предыдущий/следующий файл
$prevNext = array('prev' => array(), 'next' => array());
require 'core/inc/view/_prevnext.php';

$template = Http_Response::getInstance()->getTemplate();
$template->assign('dirs', (IS_ADMIN === true ? Files::getAllDirs() : array()));
$template->assign('prevNext', $prevNext);
$template->assign('file', $file);
$template->assign('directory', $directory);
$template->assign('vote', $vote);
$template->assign('rate', $rate);
$template->assign('commentsCount', $commentsCount);
$template->assign('comments', $comments);

$template->assign('breadcrumbs', Helper::getBreadcrumbs($file, false));

Http_Response::getInstance()->render();
