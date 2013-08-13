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

define('IS_INDEX', !$id);

$mysqldb = MysqlDb::getInstance();


###############Получаем текущий каталог#############
if ($id) {
    $d = $mysqldb->query('
        SELECT `t1`.`path`,
        `t1`.`seo`,
        ' . Language::buildFilesQuery('t1') . ',
        IF (`t1`.`dir_count` > 0, COUNT(1), 0) AS `all`
        FROM `files` AS `t1`
        LEFT JOIN `files` AS `t2` ON `t2`.`infolder` = `t1`.`path` ' . (IS_ADMIN !== true ? 'AND `t2`.`hidden` = "0"' : '') . '
        WHERE `t1`.`id` = ' . $id . '
        ' . (IS_ADMIN !== true ? 'AND `t1`.`hidden` = "0"' : '') . '
        GROUP BY `t1`.`id`
        ORDER BY NULL
    ')->fetch();
    $seo = unserialize($d['seo']);
} else {
    $d['path'] = Config::get('path') . '/';
    $q = $mysqldb->prepare('
        SELECT COUNT(1)
        FROM `files`
        WHERE `infolder` = ?
        ' . (IS_ADMIN !== true ? 'AND `hidden` = "0"' : '') . '
    ');
    $q->execute(array($d['path']));
    $d['all'] = $q->fetchColumn();
}


if (!is_dir($d['path'])) {
    error('Folder not found.');
}

$paginatorConf = Helper::getPaginatorConf($d['all']);


###############Постраничная навигация###############
$template->assign('paginatorConf', $paginatorConf);

###############Готовим заголовок###################
$template->assign('breadcrumbs', Helper::getBreadcrumbs($d, true));


/// новости
$news = $mysqldb->query('
    SELECT *, ' . Language::buildNewsQuery() . '
    FROM `news`
    ORDER BY `id` DESC
    LIMIT 1
')->fetch();

$template->assign('news', $news);


$query = $mysqldb->prepare('
    SELECT
    `id`,
    `dir`,
    `dir_count`,
    `path` as `v`,
    ' . Language::buildFilesQuery() . ',
    `size`,
    `loads`,
    `timeupload`,
    `yes`,
    `no`,
    `hidden`,
    (
        SELECT COUNT(1)
        FROM `files`
        WHERE `infolder` LIKE CONCAT(`v`, "%")
        AND `timeupload` > ?
        ' . (IS_ADMIN !== true ? 'AND `hidden` = "0"' : '') . '
    ) AS `count`
    FROM `files`
    WHERE `infolder` = ?
    ' . (IS_ADMIN !== true ? 'AND `hidden` = "0"' : '') . '
    ORDER BY ' . Helper::getSortMode() . '
    LIMIT ?, ?
');
$query->bindValue(1, $_SERVER['REQUEST_TIME'] - (86400 * Config::get('day_new')), PDO::PARAM_INT);
$query->bindValue(2, $d['path']);
$query->bindValue(3, $paginatorConf['start'], PDO::PARAM_INT);
$query->bindValue(4, $paginatorConf['onpage'], PDO::PARAM_INT);

$query->execute();

require 'core/inc/_files.php';

$template->assign('directories', $directories);
$template->assign('files', $files);
$template->send();
