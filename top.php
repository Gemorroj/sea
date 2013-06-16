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


define('IS_P_NAME', true);

require 'core/header.php';
###############Если топ выключен###############
if (!$setup['top_change']) {
    error('Not found');
}

$template->setTemplate('top.tpl');
$seo['title'] = str_replace('%files%', $setup['top_num'], $language['top20']);
$template->assign('breadcrumbs', array('top' => $seo['title']));


$all = $mysqldb->query('
    SELECT COUNT(1)
    FROM `files`
    WHERE `dir` = "0"
    ' . (IS_ADMIN !== true ? 'AND `hidden` = "0"' : '')
)->fetchColumn();
$all = $all > $setup['top_num'] ? $setup['top_num'] : $all;

$paginatorConf = getPaginatorConf($all);

###############Постраничная навигация###############
$template->assign('paginatorConf', $paginatorConf);

$topSort = ((!$setup['top_sort'] || $setup['top_sort'] === 'auto') ? null : $setup['top_sort']);

$query = $mysqldb->prepare('
    SELECT `f`.`id`,
    `f`.`hidden`,
    `f`.`dir`,
    `f`.`dir_count`,
    `f`.`path` AS `v`,
    `f`.`infolder`,
    ' . Language::getInstance()->buildFilesQuery('f') . ',
    `f`.`size`,
    `f`.`loads`,
    `f`.`timeupload`,
    `f`.`yes`,
    `f`.`no`,
    0 AS `count`,
    `p_files`.`id` AS `p_id`,
    ' . Language::getInstance()->buildFilesQuery('p_files', 'p_name') . '
    FROM `files` AS `f`
    LEFT JOIN `files` AS `p_files` ON `p_files`.`dir` = "1" AND `p_files`.`path` = `f`.`infolder`
    WHERE `f`.`dir` = "0"
    ' . (IS_ADMIN !== true ? 'AND `f`.`hidden` = "0"' : '') . '
    ORDER BY ' . getSortMode('f', $topSort) . '
    LIMIT ?, ?
');
$query->bindValue(1, $paginatorConf['start'], PDO::PARAM_INT);
$query->bindValue(2, $paginatorConf['onpage'], PDO::PARAM_INT);

$query->execute();

require 'core/inc/_files.php';

$template->assign('directories', $directories);
$template->assign('files', $files);
$template->send();
