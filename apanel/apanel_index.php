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


define('APANEL', true);
chdir('../');
require 'moduls/config.php';
require 'moduls/header.php';


$HeadTime = microtime(true);

//------------------------------------------------------------------------------------------
if ($_SESSION['authorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error('Error');
}
//------------------------------------------------------------------------------------------

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;

$onpage = get2ses('onpage');
$sort = get2ses('sort');

if ($onpage < 3) {
    $onpage = 3;
}


//------------------------------------------------------------------------------------------
if (!$id) {
    $d['path'] = $setup['path'] . '/';
} else {
    $d = mysql_fetch_assoc(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));
}

//------------------------------------------------------------------------------------------

$all = mysql_result(
    mysql_query(
        "SELECT COUNT(1) FROM `files` WHERE `infolder` = '" . mysql_real_escape_string($d['path'], $mysql) . "'",
        $mysql
    ),
    0
);
$pages = ceil($all / $onpage);
if (!$pages) {
    $pages = 1;
}
if ($page > $pages || $page < 1) {
    $page = 1;
}
if ($start > $all || $start < 0) {
    $start = 0;
}
if ($page) {
    $start = ($page - 1) * $onpage;
} else {
    $start = 0;
}

/*
$valid_sort = array('name' => '', 'data' => '', 'load' => '', 'size' => '', 'eval' =>'');
if (!isset($valid_sort[$sort])) {
    error('Error);
}
*/

if ($sort == 'data') {
    $MODE = '`priority` DESC, `timeupload` DESC';
} else {
    if ($sort == 'size') {
        $MODE = '`priority` DESC, `size` ASC';
    } else {
        if ($sort == 'load') {
            $MODE = '`priority` DESC, `loads` DESC';
        } else {
            if ($sort == 'eval' && $setup['eval_change']) {
                $MODE = '`priority` DESC, `yes` DESC , `no` ASC';
            } else {
                $MODE = '`priority` DESC, `name` ASC';
            }
        }
    }
}

// загловок
//------------------------------------------------------------------------------------------
###############Готовим заголовок###################
$ex = explode('/', $d['path']);
$sz = sizeof($ex) - 2;
$nav_dir = $setup['path'] . '/';

unset($ex[0], $ex[$sz + 1]);
$path = $setup['path'] . '/';

$put = '';
if ($ex) {
    $implode = 'SELECT `id`, ' . Language::getInstance()->buildFilesQuery() . ' FROM `files` WHERE `path` IN(';
    foreach ($ex as $v) {
        $path .= $v . '/';
        $implode .= '"' . mysql_real_escape_string($path, $mysql) . '",';
    }

    $q = mysql_query(rtrim($implode, ',') . ')', $mysql);
    while ($s = mysql_fetch_assoc($q)) {
        $put .= '<a href="../?id=' . $s['id'] . '">' . htmlspecialchars($s['name'], ENT_NOQUOTES) . '</a> &#187; ';
        $title .= '/' . htmlspecialchars($s['name'], ENT_NOQUOTES);
    }
}

##############Заголовок##########################
echo'<div class="mblock"><img src="../dis/load.png" alt=""/><a href="apanel_index.php">' . $language['downloads']
    . '</a> &#187; ' . $put . '</div><div class="iblock">';

//------------------------------------------------------------------------------------------
if ($setup['eval_change']) {
    $eval = ',<a href="apanel_index.php?sort=eval">рейтинг</a>';
} else {
    $eval = '';
}
if ($sort == 'name') {
    $sortlink
        =
        '<a href="apanel_index.php?sort=data">дата</a>,<a href="apanel_index.php?sort=size">размер</a>,<a href="apanel_index.php?sort=load">популярность</a>'
            . $eval;
} else {
    if ($sort == 'data') {
        $sortlink
            =
            '<a href="apanel_index.php?sort=name">имя</a>,<a href="apanel_index.php?sort=size">размер</a>,<a href="apanel_index.php?sort=load">популярность</a>'
                . $eval;
    } else {
        if ($sort == 'size') {
            $sortlink
                =
                '<a href="apanel_index.php?sort=data">дата</a>,<a href="apanel_index.php?sort=name">имя<a/>,<a href="apanel_index.php?sort=load">популярность</a></a>'
                    . $eval;
        } else {
            if ($sort == 'load') {
                $sortlink
                    =
                    '<a href="apanel_index.php?sort=data">дата</a>,<a href="apanel_index.php?sort=name">имя</a>,<a href="apanel_index.php?sort=size">размер</a>'
                        . $eval;
            } else {
                if ($sort == 'eval' && $setup['eval_change']) {
                    $sortlink
                        = '<a href="apanel_index.php?sort=data">дата</a>,<a href="apanel_index.php?sort=name">имя</a>,<a href="apanel_index.php?sort=size">размер</a>,<a href="apanel_index.php?sort=load">популярность</a>';
                } else {
                    $sortlink = '';
                }
            }
        }
    }
}

echo'Сортировать по:<br/>' . $sortlink . '<br/><a href="apanel.php?id=' . $id
    . '&amp;action=newdir">Новая папка</a></div>';
//------------------------------------------------------------------------------------------
if (!$all) {
    echo '<div class="no">[Раздел пуст]<br/></div>';
}


$dn = 86400 * $setup['day_new'];
$key = false;


$query = mysql_query(
    '
    SELECT `id`,
    `dir`,
    `dir_count`,
    `path` AS `v`,
    ' . Language::getInstance()->buildFilesQuery() . ',
    `size`,
    `loads`,
    `timeupload`,
    `yes`,
    `no`,
    (SELECT COUNT(1) FROM `files` WHERE `infolder` = `v` AND `timeupload` > ' . ($_SERVER['REQUEST_TIME'] - $dn) . ') AS `count`
    FROM `files`
    WHERE `infolder` = "' . mysql_real_escape_string($d['path'], $mysql) . '"
    ORDER BY ' . $MODE . '
    LIMIT ' . $start . ', ' . $onpage,
    $mysql
);


while ($v = mysql_fetch_assoc($query)) {
    if ($key = !$key) {
        $row = '<div class="mblock">';
    } else {
        $row = '<div class="row">';
    }

    if ($v['dir']) {
        echo $row;

        if (file_exists($v['v'] . 'folder.png')) {
            $ico = '<img src="../' . htmlspecialchars($v['v']) . 'folder.png" alt=""/>';
            $addico = '[<a class="no" href="apanel.php?action=reico&amp;id=' . $v['id'] . '">I</a>]';
        } else {
            $ico = '<img src="../ext/dir.png" alt=""/>';
            $addico = '[<a class="yes" href="apanel.php?action=addico&amp;id=' . $v['id'] . '">I</a>]';
        }

        $updown = '[<a class="yes" href="apanel.php?id=' . $v['id']
            . '&amp;action=pos&amp;to=up">Up</a>/<a class="no" href="apanel.php?id=' . $v['id']
            . '&amp;action=pos&amp;to=down">Down</a>]';
        if ($setup['delete_dir'] == 1) {
            $dl = '[<a class="no" href="apanel.php?action=redir&amp;id=' . $v['id'] . '">D</a>]';
        } else {
            $dl = '';
        }


        //Кол-во новых файлов в папке
        if ($setup['day_new'] && $v['count']) {
            $new_all = '(<span class="yes">+' . $v['count'] . '</span>)';
        } else {
            $new_all = '';
        }

        echo$ico . '<strong><a href="apanel_index.php?id=' . $v['id'] . '">' . htmlspecialchars(
            $v['name'],
            ENT_NOQUOTES
        ) . '</a></strong>(' . $v['dir_count'] . ')' . $new_all . ' [<a class="yes" href="apanel.php?id=' . $v['id']
            . '&amp;action=flash">F</a>][<a class="yes" href="apanel.php?id=' . $v['id']
            . '&amp;action=seo">K</a>][<a class="yes" href="apanel.php?id=' . $v['id']
            . '&amp;action=rename">R</a>][<a class="no" href="apanel.php?id=' . $v['id'] . '&amp;action=about">O</a>]'
            . $dl . $addico . $updown;

        // описания
        if ($setup['desc']) {
            $screen = strstr(strstr($v['v'], '/'), '/'); // убираем папку с загрузками
            if (is_file($setup['opath'] . '/' . $screen . '.txt')) {
                echo'<br/>' . mb_substr(
                    trim(file_get_contents($setup['opath'] . '/' . $screen . '.txt')),
                    0,
                    $setup['desc']
                );
            }
        }

        echo '</div>';

    } else {
        $ex = pathinfo($v['v']);
        $ext = strtolower($ex['extension']);
        $filename = $ex['basename'];

        $v['size'] = '(' . size($v['size']) . ')';

        if (!file_exists('ext/' . $ext . '.png')) {
            $ico = '<img src="../ext/stand.png" alt=""/>';
        } else {
            $ico = '<img src="../ext/' . $ext . '.png" alt=""/>';
        }

        $v['timeupload'] = tm($v['timeupload']);

        if ($setup['ext']) {
            $extension = '(' . $ext . ')';
        } else {
            $extension = '';
        }

        if ($setup['delete_file']) {
            $dl = '[<a class="no" href="apanel.php?action=refile&amp;id=' . $v['id'] . '">D</a>]';
        } else {
            $dl = '';
        }

        if ($ext == 'zip') {
            $unzip = '[<a class="yes" href="apanel.php?id=' . $v['id'] . '&amp;action=unpack">U</a>]';
        } else {
            $unzip = '';
        }

        if (!is_file($setup['spath'] . '/' . $filename . '.gif')) {
            $add_screen = '<a class="yes" href="apanel.php?id=' . $v['id'] . '&amp;action=screen">S</a>';
        } else {
            $add_screen = '<a class="no" href="apanel.php?id=' . $v['id'] . '&amp;action=screen">S</a>';
        }


        $desc = '';
        if ($setup['desc']) {
            $screen = strstr(strstr($v['v'], '/'), '/'); // убираем папку с загрузками

            if (is_file($setup['opath'] . '/' . $screen . '.txt')) {
                $desc = '<br/>' . mb_substr(
                    trim(file_get_contents($setup['opath'] . '/' . $screen . '.txt')),
                    0,
                    $setup['desc']
                );
            }
        }


        /////////////
        echo$row . $ico . '<strong><a href="apanel_view.php?id=' . $v['id'] . '">' . htmlspecialchars(
            $v['name'],
            ENT_NOQUOTES
        ) . '</a></strong>' . $extension . $v['size'] . '[<a class="yes" href="apanel.php?id=' . $v['id']
            . '&amp;action=seo">K</a>][<a class="yes" href="apanel.php?id=' . $v['id']
            . '&amp;action=rename">R</a>][<a class="yes" href="apanel.php?id=' . $v['id'] . '&amp;action=about">O</a>]'
            . $unzip . $dl . '[' . $add_screen . ']' . $desc;

        if ($sort == 'data') {
            echo '<br/>Добавлен: ' . $v['timeupload'];
        } else {
            if ($sort == 'load') {
                echo '<br/>Скачано ' . $v['loads'] . ' раз(а)';
            } else {
                if ($sort == 'eval' && $setup['eval_change'] == 1) {
                    echo'<br/>Рейтинг(+/-): <span class="yes">' . $v['yes'] . '</span>/<span class="no">' . $v['no']
                        . '</span><br/>';
                }
            }
        }
        echo '</div>';
    }
}

//------------------------------------------------------------------------------------------
if ($pages > 1) {
    echo '<div class="iblock">Страницы: ';
    $asd = $page - 2;
    $asd2 = $page + 3;
    if ($asd < $all && $asd > 0 && $page > 3) {
        echo '<a href="apanel_index.php?id=' . $id . '&amp;page=1">1</a> ... ';
    }
    for ($i = $asd; $i < $asd2; ++$i) {
        if ($i < $all && $i > 0) {
            if ($i > $pages) {
                break;
            }
            if ($page == $i) {
                echo '<strong>[' . $i . ']</strong> ';
            } else {
                echo '<a href="apanel_index.php?id=' . $id . '&amp;page=' . $i . '">' . $i . '</a> ';
            }
        }
    }

    if ($i <= $pages) {
        if ($asd2 < $all) {
            echo ' ... <a href="apanel_index.php?id=' . $id . '&amp;page=' . $pages . '">' . $pages . '</a>';
        }
    }
    echo '<br/>';
    //------------------------------------------------------------------------------------------
    if ($pages > $setup['pagehand'] && $setup['pagehand_change'] == 1) {
        echo'Страница ' . $page . ' из ' . $pages
            . ':<br/><form action="apanel_index.php" method="get"><div class="row"><input class="buttom" type="hidden" name="id" value="'
            . $id
            . '"/><input class="enter" name="page" type="text" maxlength="4" size="8" value=""/> <input class="buttom" type="submit" value="Перейти"/></div></form>';
    }
    //------------------------------------------------------------------------------------------
    if ($setup['onpage_change']) {
        echo 'Файлов на страницу: ';
        for ($i = 10; $i < 35; $i += 5) {
            if ($i == $onpage) {
                echo '<strong>[' . $i . ']</strong>';
            } else {
                echo '[<a href="apanel_index.php?onpage=' . $i . '&amp;id=' . $id . '">' . $i . '</a>]';
            }
        }
        echo '<br/></div>';
    }
}
//------------------------------------------------------------------------------------------
echo '<code>[R] - переименование, [O] - описание, [K] - SEO, [D] - удаление, [S] - скриншот, [F] - обновить в БД, [U] - распаковать архив, [I] - иконка, [Up/Down] - выше/ниже</code>
<div class="iblock"><a href="apanel.php">Админка</a></div>';
