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
###############Если топ выключен###############
if (!$setup['top_change']) {
    error('Not found');
}

$template->setTemplate('top.tpl');
$seo['title'] = str_replace('%files%', $setup['top_num'], $language['top20']);
$template->assign('breadcrumbs', array('top' => $seo['title']));


$onpage = get2ses('onpage');
$prew = get2ses('prew');
$sort = get2ses('sort');
$page = isset($_GET['page']) ? abs($_GET['page']) : 0;


if ($onpage < 3) {
    $onpage = $setup['onpage'];
}

if ($prew != 0 && $prew != 1) {
    $prew = $setup['preview'];
}


$all = mysql_result(mysql_query('SELECT COUNT(1) FROM `files` WHERE `hidden` = "0" AND `dir` = "0";', $mysql), 0);
$all = $all > $setup['top_num'] ? $setup['top_num'] : $all;

$onpage = $onpage > $all ? $all : $onpage;

###############Постраничная навигация###############
$pages = ceil($all / $onpage);
if (!$pages) {
    $pages = 1;
}
if ($page > $pages || $page < 1) {
    $page = 1;
}

$start = ($page - 1) * $onpage;
if ($start > $all || $start < 0) {
    $start = 0;
}

$template->assign('allItemsInDir', $all);
$template->assign('page', $page);
$template->assign('pages', $pages);
$template->assign('prew', $prew);
$template->assign('sort', $sort);


if ($sort == 'date') {
    $mode = '`priority` DESC, `timeupload` DESC';
} else {
    if ($sort == 'size') {
        $mode = '`priority` DESC, `size` ASC';
    } else {
        if ($sort == 'load') {
            $mode = '`priority` DESC, `loads` DESC';
        } else {
            if ($sort == 'eval' && $setup['eval_change']) {
                $mode = '`priority` DESC, `yes` DESC , `no` ASC';
            } else {
                $mode = '`priority` DESC, `name` ASC';
            }
        }
    }
}


$query = mysql_query(
    '
    SELECT `id`,
    `dir`,
    `dir_count`,
    `path` AS `v`,
    `infolder`,
    ' . Language::getInstance()->buildFilesQuery() . ',
    `size`,
    `loads`,
    `timeupload`,
    `yes`,
    `no`,
    0 AS `count`
    FROM `files`
    WHERE `hidden` = "0" AND `dir` = "0"
    ORDER BY ' . $mode . '
    LIMIT ' . $start . ', ' . $onpage,
    $mysql
);


require 'moduls/inc/_files.php';

$template->assign('directories', $directories);
$template->assign('files', $files);
$template->send();
