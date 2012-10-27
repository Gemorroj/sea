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


require 'moduls/header.php';


###############Проверка переменных###############
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


$template->assign('prew', $prew);
$template->assign('sort', $sort);

if ($sort == 'date') {
    $mode = '`priority` DESC, `timeupload` DESC';
} else if ($sort == 'size') {
    $mode = '`priority` DESC, `size` ASC';
} else if ($sort == 'load') {
    $mode = '`priority` DESC, `loads` DESC';
} else if ($sort == 'eval' && $setup['eval_change']) {
    $mode = '`priority` DESC, `yes` DESC , `no` ASC';
} else {
    $mode = '`priority` DESC, `name` ASC';
}
###############Получаем текущий каталог#############
if ($id) {
    $d = mysql_fetch_assoc(mysql_query('
        SELECT `t1`.`path`,
        `t1`.`seo`,
        COUNT(1) AS `all`
        FROM `files` AS `t1`
        LEFT JOIN `files` AS `t2` ON `t2`.`infolder` = `t1`.`path` AND `t2`.`hidden` = "0"
        WHERE `t1`.`id` = ' . $id . '
        AND `t1`.`hidden` = "0"
        GROUP BY `t1`.`id`
        ORDER BY NULL',
    $mysql));
    $seo = unserialize($d['seo']);
} else {
    $d['path'] = $setup['path'] . '/';
    $d['all'] = mysql_result(mysql_query('
        SELECT COUNT(1)
        FROM `files`
        WHERE `infolder` = "' . mysql_real_escape_string($d['path'], $mysql) . '"
        AND `hidden` = "0"
    ', $mysql), 0);
}


if (!is_dir($d['path'])) {
    error('Folder not found.');
}

###############Онлайн#############
mysql_query("REPLACE INTO `online` (`ip`, `time`) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', NOW());", $mysql);
mysql_query('DELETE FROM `online` WHERE `time` < (NOW() - INTERVAL ' . $setup['online_time'] . ' SECOND)', $mysql);

$online = mysql_result(mysql_query('SELECT COUNT(1) FROM online', $mysql), 0);
if ($online > $setup['online_max']) {
    mysql_query('REPLACE INTO `setting`(`name`, `value`) VALUES("online_max", "' . $online . '");', $mysql);
    mysql_query('REPLACE INTO `setting`(`name`, `value`) VALUES("online_max_time", NOW());', $mysql);
}
$template->assign('online', $online);


###############Постраничная навигация###############
$pages = ceil($d['all'] / $onpage);
if (!$pages) {
    $pages = 1;
}
if ($page > $pages || $page < 1) {
    $page = 1;
}

$start = ($page - 1) * $onpage;
if ($start > $d['all'] || $start < 0){
    $start = 0;
}

$template->assign('page', $page);
$template->assign('pages', $pages);

###############Готовим заголовок###################
$ex = explode('/', $d['path']);
$sz = sizeof($ex) - 2;

unset($ex[0], $ex[$sz + 1]);
$path = $setup['path'] . '/';

$breadcrumbs = array();
if ($ex) {
    $implode = '
        SELECT `id`,
        ' . Language::getInstance()->buildFilesQuery() . '
        FROM `files`
        WHERE `path` IN(';
    foreach ($ex as $v) {
        $path .= $v . '/';
        $implode .= '"' . mysql_real_escape_string($path, $mysql) . '",';
    }


    $q = mysql_query(rtrim($implode, ',') . ')', $mysql);
    while ($s = mysql_fetch_assoc($q)) {
        $breadcrumbs[$s['id']] = $s['name'];
        if (!$seo['title']) {
            $seo['title'] = '/' . $s['name'];
        }
    }
}
$template->assign('breadcrumbs', $breadcrumbs);



$banner = array();
$buy = array();
if ($setup['buy_change']) {
    if ($setup['buy']) {
        if ($setup['randbuy']) {
            $list = explode("\n", $setup['buy']);
            shuffle($list);
            for ($i = 0; $i < $setup['countbuy']; ++$i) {
                $buy[] = $list[$i];
            }
        } else {
            $list = explode("\n", $setup['buy']);
            for ($i = 0; $i < $setup['countbuy']; ++$i) {
                $buy[] = $list[$i];
            }
        }
    }

    if ($setup['banner']) {
        if ($setup['randbanner']) {
            $list = explode("\n", $setup['banner']);
            shuffle($list);
            for ($i = 0; $i < $setup['countbanner']; ++$i) {
                $banner[] = $list[$i];
            }
        } else {
            $list = explode("\n", $setup['banner']);
            for ($i = 0; $i < $setup['countbanner']; ++$i) {
                $banner[] = $list[$i];
            }
        }
    }
}
$template->assign('buy', $buy);
$template->assign('banner', $banner);


// модуль расширенного сервиса
$serviceBanner = array();
$serviceBuy = array();
if ($setup['service_change_advanced']) {
    $user = isset($_GET['user']) ? intval($_GET['user']) : (isset($_SESSION['user']) ? $_SESSION['user'] : '');
    if ($user) {
        $_SESSION['user'] = $user;

        $q = mysql_fetch_row(mysql_query('
            SELECT `url`, `name`, `style`
            FROM `users_profiles`
            WHERE `id` = ' . $_SESSION['user']
        , $mysql));
        $_SESSION['site_url'] = $setup['site_url'] = 'http://' . htmlspecialchars($q[0]);
        //$_SESSION['site_name'] = $setup['site_name'] = $q[1];
        $q[2] = htmlspecialchars($q[2]);

        if ($q[2] && $q[2] != $_SESSION['style']) {
            $_SESSION['style'] = $q[2];
            $template->assign('style', $_SESSION['style']);
        }

        if ($setup['service_head']) {
            $head = mysql_query('
                SELECT `name`, `value`
                FROM `users_settings`
                WHERE `parent_id` = ' . $user . '
                AND `position` = "0"
            ', $mysql);
            $all = mysql_num_rows($head);
            $all = $all < $setup['service_head'] ? $all : $setup['service_head'];
            if ($all) {
                for ($i = 0; $i < $all; ++$i) {
                    $q = mysql_fetch_assoc($head);
                    $serviceBuy[$q['value']] = $q['name'];;
                }
            }
        }

        if ($setup['service_foot']) {
            $foot = mysql_query('
                SELECT `name`, `value`
                FROM `users_settings`
                WHERE `parent_id` = ' . $user . '
                AND `position` = "1"
            ', $mysql);
            $all = mysql_num_rows($foot);
            $all = $all < $setup['service_foot'] ? $all : $setup['service_foot'];
            if ($all) {
                for ($i = 0; $i < $all; ++$i) {
                    $q = mysql_fetch_assoc($foot);
                    $serviceBanner[$q['value']] = $q['name'];
                }
            }
        }
    }
}
$template->assign('serviceBuy', $serviceBuy);
$template->assign('serviceBanner', $serviceBanner);

/// новости
$news = mysql_fetch_assoc(mysql_query('
    SELECT `time`,
    ' . Language::getInstance()->buildNewsQuery() . '
    FROM `news`
    ORDER BY `id` DESC
    LIMIT 1
', $mysql));

$template->assign('news', $news);

$template->assign('allItemsInDir', $d['all']);


$query = mysql_query('
    SELECT
    `id`,
    `dir`,
    `dir_count`,
    `path` as `v`,
    ' . Language::getInstance()->buildFilesQuery() . ',
    `size`,
    `loads`,
    `timeupload`,
    `yes`,
    `no`,
    (SELECT COUNT(1) FROM `files` WHERE `infolder` = `v` AND `timeupload` > ' . ($_SERVER['REQUEST_TIME'] - (86400 * $setup['day_new'])) . ' AND `hidden` = "0") AS `count`
    FROM `files`
    WHERE `infolder` = "' . mysql_real_escape_string($d['path'], $mysql) . '"
    AND `hidden` = "0"
    ORDER BY ' . $mode . '
    LIMIT ' . $start . ', ' . $onpage,
$mysql);

require 'moduls/inc/_files.php';

$template->assign('directories', $directories);
$template->assign('files', $files);


require 'moduls/foot.php';
