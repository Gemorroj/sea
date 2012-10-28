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
 * @author Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */


require 'moduls/config.php';
require 'moduls/header.php';
$str = '';

$title .= $language['news'];

// кол-во на страницу
$onpage = get2ses('onpage');
if ($onpage < 3) {
    $onpage = 3;
}
$page = isset($_GET['page']) ? abs($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}

$id = isset($_GET['id']) ? abs($_GET['id']) : 0;

// всего новостей
$all = mysql_result(mysql_query('SELECT COUNT(1) FROM `news`', $mysql), 0);
if (!$all) {
    error($language['news yet']);
}


$pages = ceil($all / $onpage);
if (!$pages) {
    $pages = 1;
}
if ($page > $pages) {
    $page = 1;
}
if ($page) {
    $start = ($page - 1) * $onpage;
} else {
    $start = 0;
}
if ($start > $all || $start < 1) {
    $start = 0;
}
$q = mysql_query('
    SELECT `news`.`id`,
    ' . Language::getInstance()->buildNewsQuery() . ',
    `news`.`time`,
    COUNT(k.id) AS count
    FROM `news`
    LEFT JOIN `news_komments` AS k ON `news`.`id` = k.id_news
    WHERE `news`.`id` > 0
    GROUP BY `news`.`id`
    ORDER BY `news`.`id` DESC
    LIMIT ' . $start . ', ' . $onpage,
$mysql);

while ($arr = mysql_fetch_assoc($q)) {
    $str .= '<div class="iblock">' . tm($arr['time']) . '<br/><span style="font-size:9px;">' . $arr['news'] . '</span><br/><a href="' . DIRECTORY . 'news_komm/' . $arr['id'] . '">' . $language['comments'] . '</a> [' . $arr['count'] . ']</div>';
}

echo $str;

if ($pages > 1) {
    echo '<div class="iblock">' . $language['pages'] . ': ';

    $asd = $page - 2;
    $asd2 = $page + 3;
    if ($asd < $all && $asd > 0 && $page > 3) {
        echo '<a href="' . DIRECTORY . 'news/1">1</a> ... ';
    }

    for ($i = $asd; $i < $asd2; ++$i) {
        if ($i <= $all && $i > 0) {
            if ($i > $pages) {
                break;
            }
            if ($page == $i) {
                echo '<strong>[' . $i . ']</strong> ';
            } else {
                echo '<a href="' . DIRECTORY . 'news/' . $i . '">' . $i . '</a> ';
            }
        }
    }
    if ($i <= $pages) {
        if ($asd2 < $all) {
            echo ' ... <a href="' . DIRECTORY . 'news/' . $pages . '">' . $pages . '</a>';
        }
    }
    
    echo '<br/>';
    ###############Ручной ввод страниц###############
    if ($pages > $setup['pagehand'] && $setup['pagehand_change']) {
        echo str_replace(array('%page%', '%pages%'), array($page, $pages), $language['page']) . ':<br/><form action="' . $_SERVER['PHP_SELF'] . '?" method="get"><div class="row"><input type="hidden" name="id" value="' . $id . '"/><input class="enter" name="page" type="text" maxlength="8" size="8"/> <input class="buttom" type="submit" value="' . $language['go'] . '"/></div></form>';
    }
    echo '</div>';
}

echo '<div class="iblock">- <a href="' . DIRECTORY . 'settings/' . $id . '">' . $language['settings'] . '</a><br/>';

if ($setup['stat_change']) {
    echo '- <a href="' . DIRECTORY . 'stat/'.$id.'">' . $language['statistics'] . '</a><br/>';
}
if ($setup['zakaz_change']) {
    echo '- <a href="' . DIRECTORY . 'table/'.$id.'">' . $language['orders'] . '</a><br/>';
}

echo '- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>
- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a></div>';
