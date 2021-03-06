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


define('SEA_IS_P_NAME', true);

require_once SEA_CORE_DIRECTORY . '/header.php';

if (!Config::get('top_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

$db = Db_Mysql::getInstance();
$template = Http_Response::getInstance()->getTemplate();
$template->setTemplate('top.tpl');

$title = str_replace('%files%', Config::get('top_num'), Language::get('top20'));
//Seo::addTitle($title);
Breadcrumbs::add('top', $title);


$all = $db->query('
    SELECT COUNT(1)
    FROM `files`
    WHERE `dir` = "0"
    ' . (SEA_IS_ADMIN !== true ? 'AND `hidden` = "0"' : '')
)->fetchColumn();
$all = $all > Config::get('top_num') ? Config::get('top_num') : $all;

$paginatorConf = Helper::getPaginatorConf($all);

// Постраничная навигация
$template->assign('paginatorConf', $paginatorConf);

$query = $db->prepare('
    SELECT `f`.`id`,
    `f`.`hidden`,
    `f`.`dir`,
    `f`.`dir_count`,
    `f`.`path` AS `v`,
    `f`.`infolder`,
    ' . Language::buildFilesQuery('f') . ',
    `f`.`size`,
    `f`.`loads`,
    `f`.`timeupload`,
    `f`.`yes`,
    `f`.`no`,
    0 AS `count`,
    `p_files`.`id` AS `p_id`,
    ' . Language::buildFilesQuery('p_files', 'p_name') . '
    FROM `files` AS `f`
    LEFT JOIN `files` AS `p_files` ON `p_files`.`dir` = "1" AND `p_files`.`path` = `f`.`infolder`
    WHERE `f`.`dir` = "0"
    ' . (SEA_IS_ADMIN !== true ? 'AND `f`.`hidden` = "0"' : '') . '
    ORDER BY `f`.`loads` DESC, ' . Helper::getSortMode('f') . '
    LIMIT ?, ?
');
$query->bindValue(1, $paginatorConf['start'], PDO::PARAM_INT);
$query->bindValue(2, $paginatorConf['onpage'], PDO::PARAM_INT);

$query->execute();

require SEA_CORE_DIRECTORY . '/inc/_files.php';

$template->assign('directories', $directories);
$template->assign('files', $files);
Http_Response::getInstance()->render();
