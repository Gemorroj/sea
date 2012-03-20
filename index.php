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


###############Проверка переменных###############
$onpage = get2ses('onpage');
$prew = get2ses('prew');
$sort = get2ses('sort');
$id = isset($_GET['id']) ? abs($_GET['id']) : 0;
$page = isset($_GET['page']) ? abs($_GET['page']) : 0;
$start = isset($_GET['start']) ? abs($_GET['start']) : 0;
$out = '';

if ($onpage < 3) {
    $onpage = $setup['onpage'];
}

if ($prew != 0 && $prew != 1) {
    $prew = $setup['preview'];
}


if ($sort == 'data') {
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
        INNER JOIN `files` AS `t2` ON `t2`.`infolder` = `t1`.`path` AND `t2`.`hidden` = "0"
        WHERE `t1`.`id` = ' . $id . '
        AND `t1`.`hidden` = "0"
        GROUP BY `t1`.`id`
        ORDER BY NULL',
    $mysql));
    $seo = unserialize($d['seo']);
    $title .= htmlspecialchars($seo['title'], ENT_NOQUOTES);
} else {
    $seo = array();
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

$online = mysql_fetch_row(mysql_query('SELECT COUNT(1) FROM online', $mysql));
if ($online[0] > $setup['online_max']) {
    mysql_query('REPLACE INTO `setting`(`name`, `value`) VALUES("online_max", "' . $online[0] . '");', $mysql);
    mysql_query('REPLACE INTO `setting`(`name`, `value`) VALUES("online_max_time", NOW());', $mysql);
}


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

###############Готовим заголовок###################
$ex = explode('/', $d['path']);
$sz = sizeof($ex) - 2;
$nav_dir = $setup['path'] . '/';

unset($ex[0], $ex[$sz + 1]);
$path = $setup['path'] . '/';

$put = '';
if ($ex) {
	$implode = 'SELECT ' . ($_SESSION['langpack'] == 'russian' ? '`id`, `rus_name`' : '`id`, `name`') . ' FROM `files` WHERE `path` IN(';
	foreach ($ex as $v) {
		$path .= $v . '/';
		$implode .= '"' . mysql_real_escape_string($path, $mysql) . '",';
	}


	$q = mysql_query(rtrim($implode, ',') . ')', $mysql);
	while ($s = mysql_fetch_row($q)) {
		$put .= '<a href="' . DIRECTORY . $s[0] . '">' . htmlspecialchars($s[1], ENT_NOQUOTES) . '</a> &#187; ';
		if (!$seo['title']) {
            $title .= '/' . htmlspecialchars($s[1], ENT_NOQUOTES);
        }
	}
}

##############Заголовок##########################
$out .= '<div class="mainzag"><img src="' . DIRECTORY . 'dis/load.png" alt=""/><a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a> &#187; ' . $put . '</div>';
###############Вывод рекламы###############
$banner = '';
if ($setup['buy_change']) {
	if ($setup['buy']) {
		$out .= '<div class="iblock">';
		if ($setup['randbuy']) {
			$list = explode("\n", $setup['buy']);
			shuffle ($list);
			for ($i = 0; $i < $setup['countbuy']; ++$i) {
				$out .= $list[$i] . '<br/>';
			}
		} else {
			$list = explode("\n", $setup['buy']);
			for ($i = 0; $i < $setup['countbuy']; ++$i) {
				$out .= $list[$i] . '<br/>';
			}
		}
		$out .= '</div>';
	}
	if ($setup['banner']) {
		$banner .= '<div class="iblock">';
		if ($setup['randbanner']) {
			$list = explode("\n", $setup['banner']);
			shuffle($list);
			for ($i = 0; $i < $setup['countbanner']; ++$i) {
				$banner .= $list[$i] . '<br/>';
			}
		} else {
			$list = explode("\n", $setup['banner']);
			for ($i = 0; $i < $setup['countbanner']; ++$i) {
				$banner .= $list[$i] . '<br/>';
			}
		}
		$banner .= '</div>';
	}
}

// модуль расширенного сервиса
if ($setup['service_change_advanced']) {
	$user = isset($_GET['user']) ? intval($_GET['user']) : (isset($_SESSION['user']) ? $_SESSION['user'] : '');
	if ($user) {
		$_SESSION['user'] = $user;

		$q = mysql_fetch_row(mysql_query('SELECT `url`, `name`, `style` FROM `users_profiles` WHERE `id` = ' . $_SESSION['user'], $mysql));
		$_SESSION['site_url'] = $setup['site_url'] = 'http://' . htmlspecialchars($q[0]);
		//$_SESSION['site_name'] = $setup['site_name'] = $q[1];
		$q[2] = htmlspecialchars($q[2]);

		if ($q[2] && $q[2] != $_SESSION['style']) {
			$_SESSION['style'] = $q[2];
			$str = str_replace('<link rel="stylesheet" type="text/css" href="http://' . $GLOBALS['style'] . '"/>', '<link rel="stylesheet" type="text/css" href="http://' . $_SESSION['style'] . '"/>', ob_get_contents());
			ob_clean();
			echo $str;
		}

		if ($setup['service_head']) {
			$head = mysql_query('SELECT `name`, `value` FROM `users_settings` WHERE `parent_id` = ' . $user . ' AND `position` = "0"', $mysql);
			$all = mysql_num_rows($head);
			$all = $all < $setup['service_head'] ? $all : $setup['service_head'];
			if ($all) {
				$out .= '<div class="iblock">';
				for ($i = 0; $i < $all; ++$i) {
					$q = mysql_fetch_row($head);
					$out .= '<a href="' . htmlspecialchars($q[1]) . '">' . htmlspecialchars($q[0], ENT_NOQUOTES) . '</a><br/>';
				}
				$out .= '</div>';
			}
		}
		if ($setup['service_foot']) {
			$foot = mysql_query('SELECT `name`, `value` FROM `users_settings` WHERE `parent_id` = ' . $user . ' AND `position` = "1"', $mysql);
			$all = mysql_num_rows($foot);
			$all = $all < $setup['service_foot'] ? $all : $setup['service_foot'];
			if ($all) {
				$banner .= '<div class="iblock">';
				for ($i = 0; $i < $all; ++$i) {
					$q = mysql_fetch_row($foot);
					$banner .= '<a href="' . htmlspecialchars($q[1]) . '">' . htmlspecialchars($q[0], ENT_NOQUOTES) . '</a><br/>';
				}
				$banner .= '</div>';
			}
		}
	}
}


// только если корень
if ($id < 1) {
    $str = '';
    /// новости													// кол-во символов
    $news = mysql_fetch_row(mysql_query('SELECT `time`, LEFT(`' . ($_SESSION['langpack'] == 'russian' ? 'rus_news' : 'news') . '`,64) FROM `news` ORDER BY `id` DESC LIMIT 1', $mysql));

    if ($news) {
    	$str.= '<a href="' . DIRECTORY . 'news.php">' . $_SESSION['language']['news'] . '</a> (' . tm($news[0]) . ')<br/><span style="font-size:9px;">' . $news[1] . '</span><br/>';
    }

    if ($setup['search_change']) {
    	$str.= '<a href="' . DIRECTORY . 'search.php">' . $_SESSION['language']['search'] . '</a><br/>';
    }
    if ($setup['top_change']) {
    	$str.= '<a href="' . DIRECTORY . 'top.php">' . str_replace('%files%', $setup['top_num'], $_SESSION['language']['top20']) . '</a><br/>';
    }

    if ($str) {
    	$out .= '<div class="iblock">' . $str . '</div>';
    	unset($str);
    }
}


###############Список фалов и папок###############
if (!$d['all']) {
	$out .= '<div class="mainzag"><strong>[' . $_SESSION['language']['empty'] . ']</strong></div>';
}


$dn = 86400 * $setup['day_new'];
$key = false;

if ($_SESSION['langpack'] == 'russian') {
	$query = mysql_query('
    	SELECT
	    `id`,
		`dir`,
		`dir_count`,
		`path` as `v`,
		`rus_name` AS `name`,
		`size`,
		`loads`,
		`timeupload`,
		`yes`,
		`no`,
		(SELECT COUNT(1) FROM `files` WHERE `infolder` = `v` AND `timeupload` > ' . ($_SERVER['REQUEST_TIME'] - $dn) . ' AND `hidden` = "0") AS `count`
		FROM `files`
		WHERE `infolder` = "' . mysql_real_escape_string($d['path'], $mysql) . '"
        AND `hidden` = "0"
		ORDER BY ' . $mode . '
		LIMIT ' . $start . ', ' . $onpage,
	$mysql);
} else {
	$query = mysql_query('
        SELECT
    	`id`,
    	`dir`,
    	`dir_count`,
    	`path` as `v`,
    	`name`,
    	`size`,
    	`loads`,
    	`timeupload`,
    	`yes`,
    	`no`,
    	(SELECT COUNT(1) FROM `files` WHERE `infolder` = `v` AND `timeupload` > ' . ($_SERVER['REQUEST_TIME'] - $dn) . ' AND `hidden` = "0") AS `count`
    	FROM `files`
    	WHERE `infolder` = "' . mysql_real_escape_string($d['path'], $mysql) . '"
        AND `hidden` = "0"
    	ORDER BY ' . $mode . '
    	LIMIT ' . $start . ', ' . $onpage,
	$mysql);
}

while ($v = mysql_fetch_assoc($query)) {
    $pre = $desc = $info = $new_info = '';
    $screen = strstr($v['v'], '/'); // убираем папку с загрузками
    
    if ($key = !$key) {
    	$row = '<div class="mainzag">';
    } else {
    	$row = '<div class="row">';
    }
    if ($v['dir']) {
        //Кол-во новых файлов в папке
        if ($setup['day_new'] && $v['count']) {
            $new_all = '(<span class="yes">+' . $v['count'] . '</span>)';
        } else {
        	$new_all = '';
        }

        //Иконка к папке
        if (file_exists($v['v'] . 'folder.png')) {
            $ico = '<img src="' . DIRECTORY . htmlspecialchars($v['v']) . 'folder.png" alt=""/>';
        } else {
        	$ico = '<img src="' . DIRECTORY . 'ext/dir.png" alt=""/>';
        }

        //Собсвенно вывод
        $out .= $row . $ico . '<strong><a href="' . DIRECTORY . $v['id'] . '">' . htmlspecialchars($v['name'], ENT_NOQUOTES) . '</a></strong>(' . $v['dir_count'] . ')' . $new_all;

        // описания
        if ($setup['desc'] && file_exists($setup['opath'] . $screen . '.txt')) {
            $out .= '<br/>' . iconv_substr(trim(file_get_contents($setup['opath'] . $screen . '.txt')), 0, $setup['desc']);
        }

        $out .= '</div>';
    } else {
        $prev_pic = str_replace('/', '--', iconv_substr($screen, 1));
        $ext = strtolower(pathinfo($v['v'], PATHINFO_EXTENSION));
        $pre = '';

        //Предосмотр
        if ($prew) {
            if ($setup['screen_change'] && ($ext == 'gif' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'bmp')) {
                if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
                    $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['picpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/>';
                } else {
                    $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . 'im/' . $v['id'] . '" alt=""/>';
                }
            } else if ($setup['screen_change'] && ($ext == 'avi' || $ext == '3gp' || $ext == 'mp4') && extension_loaded('ffmpeg')) {
                $wh = explode('*', $setup['prev_size']);
                if (file_exists($setup['ffmpegpath'] . '/' . htmlspecialchars($prev_pic) . '_frame_' . $setup['ffmpeg_frame'] . '.gif')) {
                    $pre .= '<img style="margin: 1px; width:' . $wh[0] . '; height:' . $wh[1] . ';" src="' . DIRECTORY . $setup['ffmpegpath'] . '/' . htmlspecialchars($prev_pic) . '_frame_' . $setup['ffmpeg_frame'] . '.gif" alt=""/>';
                } else {
                    $pre .= '<img style="margin: 1px; width:' . $wh[0] . '; height:' . $wh[1] . ';" src="' . DIRECTORY . 'ffmpeg/' . $v['id'] . '" alt=""/>';
                }
            } else if ($setup['screen_change'] && ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs' || $ext == 'apk')) {
                if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif')) {
                    $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/>';
                } else if ($setup['swf_change'] && file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
                    $pre .= '<object style="width:128px; height:128px;"><param name="movie" value="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif.swf"><embed src="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif.swf" style="width:128px; height:128px;"></embed></param></object>';
                } else if (!file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
                    $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . 'theme/' . $v['id'] . '" alt=""/>';
                }
            } else if ($setup['jar_change'] && $ext == 'jar') {
            	if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
            		$pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['ipath'] . '/' . htmlspecialchars($prev_pic) . '.png" alt=""/>';
            	} else if (jar_ico($v['v'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
            		$pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['ipath'] . '/' . htmlspecialchars($prev_pic) . '.png" alt=""/>';
            	}
            } else if ($setup['swf_change'] && $ext == 'swf') {
            	$pre .= '<object style="width:128px; height:128px;"><param name="movie" value="' . DIRECTORY . htmlspecialchars($v['v']) . '"><embed src="' . DIRECTORY . htmlspecialchars($v['v']) . '" style="width:128px; height:128px;"></embed></param></object>';
            }
        }


        if ($sort == 'load') {
        	$info = '(<span class="yes">' . $v['loads'] . '</span>)';
        } else if ($sort == 'data') {
        	$info = '(' . tm($v['timeupload']) . ')';
        } else if ($sort == 'eval' && $setup['eval_change']) {
        	$info = '(<span class="yes">' . $v['yes'] . '</span>/<span class="no">' . $v['no'] . '</span>)';
        } else {
        	$info = '';
        }

        //Новизна файла
        if (($v['timeupload'] + $dn) >= $_SERVER['REQUEST_TIME'] && $setup['day_new']) {
        	$new_info = '<span class="yes">' . $_SESSION['language']['new'] . '</span>';
        } else {
        	$new_info = '';
        }
        //Красивый размер
        $v['size'] = '(' . size($v['size']) . ')';

        //Иконка к файлу
        if (file_exists('ext/' . $ext . '.png')) {
        	$ico = '<img src="' . DIRECTORY . 'ext/' . $ext . '.png" alt=""/>';
        } else {
        	$ico = '<img src="' . DIRECTORY . 'ext/stand.png" alt=""/>';
        }
        //Показ расиширения
        if ($setup['ext']) {
        	$extension = '(' . $ext . ')';
        } else {
        	$extension = '';
        }


        if ($setup['screen_change']) {
            $th_gif = file_exists($setup['spath'] . $screen . '.thumb.gif');
            $th_jpg = file_exists($setup['spath'] . $screen . '.thumb.jpg');

            if (file_exists($setup['spath'] . $screen . '.gif') && !$th_gif) {
                img_resize($setup['spath'] . $screen . '.gif', $setup['spath'] . $screen . '.thumb.gif', 0, 0, $setup['marker']);
            } else if (file_exists($setup['spath'] . $screen . '.jpg') && !$th_gif) {
                img_resize($setup['spath'] . $screen . '.jpg', $setup['spath'] . $screen . '.thumb.gif', 0, 0, $setup['marker']);
            }

            if ($th_gif) {
                $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['spath'] . htmlspecialchars($screen) . '.thumb.gif" alt=""/>';
            } else if ($th_jpg) {
                $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['spath'] . htmlspecialchars($screen) . '.thumb.jpg" alt=""/>';
            }
        }

        if ($pre) {
        	$pre .= '<br/>';
        }


        if ($setup['desc'] && file_exists($setup['opath'] . $screen . '.txt')) {
        	$desc .= '<br/>' . iconv_substr(trim(file_get_contents($setup['opath'] . $screen . '.txt')), 0, $setup['desc']);
        }


        //Собственно вывод
        $out .= $row . $pre . $ico . '<strong><a href="' . DIRECTORY . 'view/' . $v['id'] . '">' . htmlspecialchars($v['name'], ENT_NOQUOTES) . '</a></strong>' . $extension . $v['size'] . $info . '[<a class="yes" href="' . DIRECTORY . 'load/' . $v['id'] . '">L</a>]' . $new_info . $desc . '<br/></div>';
    }
}

###############Постраничная навигация########
if ($pages > 1) {
    $out .= '<div class="iblock">' . $_SESSION['language']['pages'] . ': ';
    $asd = $page - 2;
    $asd2 = $page + 3;
    if ($asd < $d['all'] && $asd > 0 && $page > 3) {
    	$out .= '<a href="' . DIRECTORY . $id . '/1">1</a> ... ';
    }
    for ($i = $asd; $i < $asd2; ++$i) {
        if ($i < $d['all'] && $i > 0) {
            if ($i > $pages ) {
            	break;
            }
            if ($page == $i) {
            	$out .= '<strong>[' . $i . ']</strong> ';
            } else {
            	$out .= '<a href="' . DIRECTORY . $id . '/' . $i . '">' . $i . '</a> ';
            }
        }
    }
    if ($i <= $pages) {
        if ($asd2 < $d['all']) {
        	$out .= ' ... <a href="' . DIRECTORY . $id . '/' . $pages . '">' . $pages . '</a>';
        }
    }
    $out .= '<br/></div>';

    ###############Ручной ввод страниц###############
    if ($pages > $setup['pagehand'] && $setup['pagehand_change']) {
        $out .= str_replace(array('%page%', '%pages%'), array($page, $pages), $_SESSION['language']['page']) . ':<br/><form action="' . DIRECTORY . 'index.php?" method="get"><div class="row"><input type="hidden" name="id" value="' . $id . '"/><input class="enter" name="page" type="text" maxlength="8" size="8"/>&#160;<input class="buttom" type="submit" value="' . $_SESSION['language']['go'] . '"/></div></form>';
    }
}



$out .= '<div class="iblock">- <a href="' . DIRECTORY . 'user/' . $id . '">' . $_SESSION['language']['settings'] . '</a><br/>';

if ($setup['stat_change']) {
	$out .= '- <a href="' . DIRECTORY . 'stat.php">' . $_SESSION['language']['statistics'] . '</a><br/>';
}
if ($setup['zakaz_change']) {
	$out .= '- <a href="' . DIRECTORY . 'table.php">' . $_SESSION['language']['orders'] . '</a><br/>';
}
if ($setup['exchanger_change']) {
	$out .= '- <a href="' . DIRECTORY . 'exchanger.php">' . $_SESSION['language']['add file'] . '</a><br/>';
}
$out .= '- <a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a>';
if ($setup['online']) {
	$out .= '<br/>- Online: <strong>' . $online[0] . '</strong><br/>';
}

echo $out . '</div>' . $banner;

require 'moduls/foot.php';

?>
