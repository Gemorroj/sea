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



// Получаем инфу о файле
$v = mysql_fetch_assoc(
    mysql_query(
        '
    SELECT *,
    ' . Language::getInstance()->buildFilesQuery() . '
    FROM `files`
    WHERE `id` = ' . $id . '
    AND `hidden` = "0"
',
        $mysql
    )
);

if (!is_file($v['path'])) {
    error('File not found');
}


$template->setTemplate('view.tpl');


// Система голосований
if (isset($_GET['eval']) && $setup['eval_change']) {
    if (strpos($v['ips'], $_SERVER['REMOTE_ADDR']) === false) {
        $vote = 'success';
        if (!$v['ips']) {
            $ipp = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipp = $v['ips'] . "\n" . $_SERVER['REMOTE_ADDR'];
        }

        if ($_GET['eval'] < 1) {
            $v['no'] += 1;
            mysql_unbuffered_query(
                'UPDATE `files` SET `no`=`no` + 1,`ips` = "' . $ipp . '" WHERE `id` = ' . $v['id'],
                $mysql
            );
        } else {
            $v['yes'] += 1;
            mysql_unbuffered_query(
                'UPDATE `files` SET `yes`=`yes` + 1,`ips` = "' . $ipp . '" WHERE `id` = ' . $v['id'],
                $mysql
            );
        }
    } else {
        $vote = 'fail';
    }
} else {
    $vote = null;
}
// рейтинг
$rate = $v['yes'] + $v['no'];
$rate = $rate ? round($v['yes'] / $i * 100, 0) : 50;


#######Получаем имя файла и обратный каталог#####
$filename = pathinfo($v['path']);
$ext = strtolower($filename['extension']);
$dir = $filename['dirname'] . '/';
$basename = $filename['basename'];
$seo = unserialize($v['seo']);
$v['ext'] = $ext;


// данные по файлам
require 'moduls/inc/_file.php';


$sql_dir = mysql_real_escape_string($dir, $mysql);
// Директория
$directory = mysql_fetch_assoc(mysql_query('SELECT `id`, ' . Language::getInstance()->buildFilesQuery() . ' FROM `files` WHERE `path` = "' . $sql_dir . '" LIMIT 1', $mysql));
// Всего комментариев
$kommentsCount = mysql_result(mysql_query('SELECT COUNT(1) FROM `komments` WHERE `file_id` = ' . $id, $mysql), 0);
// Последние комментарии
$komments = array();
if ($setup['komments_view'] && $kommentsCount) {
    $q = mysql_query(
        'SELECT `name`, `text`, `time` FROM `komments` WHERE `file_id` = ' . $id . ' ORDER BY `id` DESC LIMIT '
            . intval($setup['komments_view']),
        $mysql
    );
    if ($q && mysql_num_rows($q)) {
        while ($row = mysql_fetch_assoc($q)) {
            $komments[] = $row;
        }
    }
}

// предыдущий/следующий файл
$prevNext = array('prev' => array(), 'next' => array());
if ($setup['prev_next']) {
    $count = mysql_result(
        mysql_query(
            '
        SELECT COUNT(1)
        FROM `files`
        WHERE `infolder` = "' . $sql_dir . '"
        AND `dir` = "0"
        AND `hidden` = "0"
    ',
            $mysql
        ),
        0
    );
    $prevNext['count'] = $count;

    if ($count > 1) {
        $next = mysql_fetch_row(
            mysql_query(
                '
            SELECT MIN(`id`), COUNT(`id`)
            FROM `files`
            WHERE `infolder` = "' . $sql_dir . '"
            AND `dir` = "0"
            AND `hidden` = "0"
            AND `id` > ' . $id
                ,
                $mysql
            )
        );

        $prev = mysql_fetch_row(
            mysql_query(
                '
            SELECT MAX(`id`), COUNT(`id`)
            FROM `files`
            WHERE `infolder` = "' . $sql_dir . '"
            AND `dir` = "0"
            AND `hidden` = "0"
            AND `id` < ' . $id
                ,
                $mysql
            )
        );


        if ($prev[0]) {
            $prevNext['prev'] = array(
                'index' => $prev[1],
                'id' => $prev[0],
            );
        }
        if ($next[0]) {
            $prevNext['next'] = array(
                'index' => $next[1],
                'id' => $next[0],
            );
        }
    }
}




if (!$seo['title']) {
    $seo['title'] = $v['name'];
}

$template->assign('prevNext', $prevNext);
$template->assign('file', $v);
$template->assign('directory', $directory);
$template->assign('vote', $vote);
$template->assign('rate', $rate);
$template->assign('kommentsCount', $kommentsCount);
$template->assign('komments', $komments);
$template->assign('breadcrumbs', array(
    $directory['id'] => $directory['name'],
    'view/' . $id => $v['name']
));



$template->send();
