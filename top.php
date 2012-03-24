<?php
#-----------------------------------------------------#
# ============ЗАГРУЗ-ЦЕНТР============= #
# 	 Автор : Sea #
# E-mail : x-sea-x@ya.ru #
# ICQ : 355152215 #
# Вы не имеете права распространять данный скрипт. #
# 		По всем вопросам пишите в ICQ. #
#-----------------------------------------------------#

// mod Gemorroj

require 'moduls/config.php';
require 'moduls/header.php';
###############Если топ выключен###############
if (!$setup['top_change']) {
	error('Not found');
}
###############Проверка переменных###############
$title .= str_replace('%files%', $setup['top_num'], $_SESSION['language']['top20']);


$onpage = get2ses('onpage');
if ($onpage < 3) {
    $onpage = $setup['onpage'];
}

$prew = get2ses('prew');
if ($prew != 0 && $prew != 1) {
    $prew = $setup['preview'];
}

$sort = get2ses('sort');
$id = isset($_GET['id']) ? abs($_GET['id']) : 0;
$page = isset($_GET['page']) ? abs($_GET['page']) : 1;
if ($page < 1) {
	$page = 1;
}
$out = '';


if (!$setup['eval_change']) {
	$sortlink = '';
	$sort = 'load';
} else {
	$sortlink = $_SESSION['language']['sort'] . ': ';
	if($sort != 'eval'){
		$sort = 'load';
		$sortlink .= '<a href="' . DIRECTORY . 'top/eval">' . $_SESSION['language']['rating'] . '</a>';
	} else {
		$sortlink .= '<a href="' . DIRECTORY . 'top/load">' . $_SESSION['language']['popularity'] . '</a>';
	}
}


if ($sort == 'load') {
	$mode = '`t1`.`dir` = "0" AND `t1`.`loads` > 0 AND `t1`.`hidden` = "0" ORDER BY `t1`.`loads`';
} else {
	$mode = '`t1`.`dir` = "0" AND `t1`.`yes` > 0 AND `t1`.`hidden` = "0" ORDER BY `t1`.`yes`';
}

###############Получаем список файлов###############

if ($_SESSION['langpack'] == 'russian') {
    $sql = mysql_query('
        SELECT SQL_CALC_FOUND_ROWS `t1`.`id`,
        `t1`.`path`,
        `t1`.`infolder`,
        `t1`.`rus_name` AS `name`,
        `t1`.`size`,
        `t1`.`loads`,
        `t1`.`timeupload`,
        `t1`.`yes`,
        `t1`.`no`,
        `t2`.`id` AS `back`
        FROM `files` AS `t1`
        LEFT JOIN `files` AS `t2` ON `t2`.`path` = `t1`.`infolder` AND `t2`.`hidden` = "0"
        WHERE ' . $mode . ' DESC
        LIMIT ' . (($page * $onpage) - $onpage) . ', ' . $onpage,
    $mysql);
} else {
    $sql = mysql_query('
        SELECT SQL_CALC_FOUND_ROWS `t1`.`id`,
        `t1`.`path`,
        `t1`.`infolder`,
        `t1`.`name`,
        `t1`.`size`,
        `t1`.`loads`,
        `t1`.`timeupload`,
        `t1`.`yes`,
        `t1`.`no`,
        `t2`.`id` AS `back`
        FROM `files` AS `t1`
        LEFT JOIN `files` AS `t2` ON `t2`.`path` = `t1`.`infolder` AND `t2`.`hidden` = "0"
        WHERE ' . $mode . ' DESC
        LIMIT ' . (($page * $onpage) - $onpage) . ', ' . $onpage,
    $mysql);
}
$all = mysql_fetch_row(mysql_query('SELECT FOUND_ROWS();', $mysql));
$all = $all[0] > $setup['top_num'] ? $setup['top_num'] : $all[0];

$onpage = $onpage > $all ? $all : $onpage;

###############Вывод###############
$out .= '<div class="mblock"><img src="' . DIRECTORY . 'dis/about.png" alt=""/>' . str_replace('%files%', $setup['top_num'], $_SESSION['language']['top20']) . ': <br/>' . $sortlink . '</div>';
###############Cтраницы###############

$pages = ceil($all / $onpage);
if (!$pages) {
	$pages = 1;
}

###############Если их нет...###########
if (!$all) {
	$out .= $_SESSION['language']['empty'];
}


###############Вывод списка#############
$bool = true;
while ($v = mysql_fetch_assoc($sql)) {
    $bool != $bool;

    if ($bool) {
    	$out .= '<div class="row">';
    } else {
    	$out .= '<div class="mainzag">';
    }

    $ext = strtolower(pathinfo($v['path'], PATHINFO_EXTENSION));

    if ($sort == 'load') {
    	$info = '[<span class="yes">' . $v['loads'] . '</span>]';
    } elseif ($sort == 'eval' && $setup['eval_change']) {
    	$info = '[<span class="yes">' . $v['yes'] . '</span>/<span class="no">' . $v['no'] . '</span>]';
    } else {
        $info = '';
    }

    //Красивый размер
    $v['size'] = '(' . size($v['size']) . ')';

    //Предосмотр
    $pre = '';
    if ($prew) {
        $prev_pic = str_replace('/', '--', iconv_substr(strstr($v['path'], '/'), 1));

        if ($setup['screen_change'] && ($ext == 'gif' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'bmp')) {
            if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
                $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['picpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/><br/>';
            } else {
                $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . 'im/' . $v['id'] . '" alt=""/><br/>';
            }
        } else if ($setup['screen_change'] && ($ext == 'avi' || $ext == '3gp' || $ext == 'mp4' || $ext == 'flv') && extension_loaded('ffmpeg')) {
            $wh = explode('*', $setup['prev_size']);
            if (file_exists($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $setup['ffmpeg_frame'] . '.gif')) {
                $pre .= '<img style="margin: 1px; width:' . $wh[0] . '; height:' . $wh[1] . ';" src="' . DIRECTORY . $setup['ffmpegpath'] . '/' . htmlspecialchars($prev_pic) . '_frame_' . $setup['ffmpeg_frame'] . '.gif" alt=""/><br/>';
            } else {
                $pre .= '<img style="margin: 1px; width:' . $wh[0] . '; height:' . $wh[1] . ';" src="' . DIRECTORY . 'ffmpeg/' . $v['id'] . '" alt=""/><br/>';
            }
        } else if ($setup['screen_change'] && ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs' || $ext == 'apk')) {
            if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif')) {
                $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/><br/>';
            } else if ($setup['swf_change'] && file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
                $pre .= '<object style="width:128px; height:128px;"><param name="movie" value="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif.swf"><embed src="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif.swf" style="width:128px; height:128px;"></embed></param></object><br/>';
            } else if (!file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
                $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . 'theme/' . $v['id'] . '" alt=""/><br/>';
            }
        } else if ($setup['jar_change'] && $ext == 'jar') {
        	if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
        		$pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['ipath'] . '/' . htmlspecialchars($prev_pic) . '.png" alt=""/><br/>';
        	} else if (jar_ico($v['v'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
        		$pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['ipath'] . '/' . htmlspecialchars($prev_pic) . '.png" alt=""/><br/>';
        	}
        } else if ($setup['swf_change'] && $ext == 'swf') {
        	$pre .= '<object style="width:128px; height:128px;"><param name="movie" value="' . DIRECTORY . htmlspecialchars($v['v']) . '"><embed src="' . DIRECTORY . htmlspecialchars($v['v']) . '" style="width:128px; height:128px;"></embed></param></object><br/>';
        }
    }

    //Иконка к файлу
    if (!file_exists('ext/' . $ext . '.png')) {
    	$ico = '<img src="' . DIRECTORY . 'ext/stand.png" alt=""/>';
    } else {
    	$ico = '<img src="' . DIRECTORY . 'ext/' . $ext . '.png" alt=""/>';
    }

    if ($setup['ext']) {
    	$extension = '(' . $ext . ')';
    } else {
    	$extension = '';
    }
    //Собсвенно вывод
    $out .= $pre . ' ' . $ico . '<strong><a href="' . DIRECTORY . 'view/' . $v['id'] . '">' . htmlspecialchars($v['name'], ENT_NOQUOTES) . '</a></strong>' . $extension . $v['size'] . $info . '[<a href="' . DIRECTORY . $v['back'] . '">' . $_SESSION['language']['go to the category'] . '</a>]<br/></div>';
}
//------------------------------------------------------------------------------------------

if ($pages > 1) {
    $out .= '<div class="iblock">' . $_SESSION['language']['pages'] . ': ';
    $asd = $page - 2;
    $asd2 = $page + 3;
    if ($asd < $all && $asd > 0 && $page > 3) {
    	$out .= '<a href="' . DIRECTORY . 'top/' . $sort . '/1">1</a> ... ';
    }
    for ($i = $asd; $i < $asd2; ++$i) {
        if($i < $all && $i > 0) {
            if ($i > $pages ) {
            	break;
            }
            if ($page == $i) {
            	$out .= '<strong>[' . $i . ']</strong> ';
            } else {
            	$out .= '<a href="' . DIRECTORY . 'top/' . $sort . '/' . $i . '">' . $i . '</a> ';
            }
        }
    }

    if ($i <= $pages) {
        if ($asd2 < $all) {
        	$out .= ' ... <a href="' . DIRECTORY . 'top/' . $sort . '/' . $pages . '">' . $pages . '</a>';
        }
    }
    $out .= '</div>';
}
//------------------------------------------------------------------------------------------
echo $out . '<div class="iblock">- <a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a></div>';

require 'moduls/foot.php';

?>
