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


$template->setTemplate('news.tpl');
$seo['title'] = $language['news'];
$template->assign('breadcrumbs', array('news' => $language['news']));

$onpage = get2ses('onpage');
$page = isset($_GET['page']) ? abs($_GET['page']) : 0;

if ($onpage < 3) {
    $onpage = $setup['onpage'];
}


// всего новостей
$all = mysql_result(mysql_query('SELECT COUNT(1) FROM `news`', $mysql), 0);

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


$query = mysql_query(
    '
    SELECT `news`.`id`,
    ' . Language::getInstance()->buildNewsQuery() . ',
    `news`.`time`,
    COUNT(k.id) AS `count`
    FROM `news`
    LEFT JOIN `news_comments` AS k ON `news`.`id` = k.id_news
    WHERE `news`.`id` > 0
    GROUP BY `news`.`id`
    ORDER BY `news`.`id` DESC
    LIMIT ' . $start . ', ' . $onpage,
    $mysql
);

$news = array();
while ($row = mysql_fetch_assoc($query)) {
    $news[] = $row;
}

$template->assign('allItemsInDir', $all);
$template->assign('page', $page);
$template->assign('pages', $pages);
$template->assign('news', $news);
$template->send();
