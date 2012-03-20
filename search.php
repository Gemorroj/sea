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
###############Если поиск выключен###############
if (!$setup['search_change']) {
	error('Not found');
}
###############Проверка переменных###############

$title .= $_SESSION['language']['search'];
$out = '';

$onpage = get2ses('onpage');
if ($onpage < 3) {
    $onpage = $setting['onpage'];
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


###############Проверка переменных###############
if (isset($_GET['act'])) {
    if ($_REQUEST['word'] == '') {
    	error($_SESSION['language']['do not fill in the required fields']);
    }
    $word = mysql_real_escape_string($_REQUEST['word'], $mysql);
    $start = ($onpage * $page) - $onpage;

    if ($_SESSION['langpack'] == 'russian') {
        $sql = mysql_query('
            SELECT SQL_CALC_FOUND_ROWS `t1`.`id`,
            `t1`.`dir`,
            `t1`.`dir_count`,
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
            WHERE `t1`.`rus_name` LIKE "%' . str_replace(array('%', '_'), array('\%', '\_'), $word) . '%"
            AND `t1`.`hidden` = "0"
            LIMIT ' . $start . ', ' . $onpage,
        $mysql);
    } else {
        $sql = mysql_query('
            SELECT SQL_CALC_FOUND_ROWS `t1`.`id`,
            `t1`.`dir`,
            `t1`.`dir_count`,
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
            WHERE `t1`.`name` LIKE "%' . str_replace(array('%', '_'), array('\%', '\_'), $word) . '%"
            AND `t1`.`hidden` = "0"
            LIMIT ' . $start . ', ' . $onpage,
        $mysql);
    }

    $all = mysql_result(mysql_query('SELECT FOUND_ROWS();', $mysql), 0);


    if ($onpage) {
        $pages = ceil($all / $onpage);
        if (!$pages) {
        	$pages = 1;
        }
    } else {
        $pages = 1;
    }

    $out .= '<div class="mblock"><img src="' . DIRECTORY . 'dis/load.png" alt=""/>' . str_replace(array('%word%', '%all%'), array(htmlspecialchars($word, ENT_NOQUOTES), $all), $_SESSION['language']['upon request']) . '</div>';

    if (!$all) {
    	$out .= '<div class="no">' . $_SESSION['language']['your search found nothing'] . '</div>';
    }


    $bool = true;
    while ($v = mysql_fetch_assoc($sql)) {
        $bool != $bool;

        if ($bool) {
        	$out .= '<div class="row">';
        } else {
        	$out .= '<div class="mainzag">';
        }


        if ($v['dir']) {

            //Иконка к папке
            if (file_exists($v['path'] . 'folder.png')) {
                $out .= '<img src="' . DIRECTORY . htmlspecialchars($v['path']) . 'folder.png" alt=""/>';
            } else {
            	$out .= '<img src="' . DIRECTORY . 'ext/dir.png" alt=""/>';
            }

            $out .= '<strong><a href="' . DIRECTORY . $v['id'] . '">' . str_ireplace(htmlspecialchars($word, ENT_NOQUOTES), '<span class="yes">' . htmlspecialchars($word, ENT_NOQUOTES) . '</span>', htmlspecialchars($v['name'], ENT_NOQUOTES)) . '</a></strong>(' . $v['dir_count'] . ')<br/></div>';
        } else {

            $ext = strtolower(pathinfo($v['path'], PATHINFO_EXTENSION));

            //Красивый размер
            $v['size'] = '(' . size($v['size']) . ')';

            //Предосмотр
            $pre = '';
            if ($prew) {
                $prev_pic = str_replace('/', '--', iconv_substr(strstr($v['path'], '/'), 1));
    
                if ($setup['screen_change'] && ($ext == 'gif' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'bmp')) {
                    if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')){
                        $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . $setup['picpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/><br/>';
                    } else {
                        $pre .= '<img style="margin: 1px;" src="' . DIRECTORY . 'im/' . $v['id'] . '" alt=""/><br/>';
                    }
                } else if ($setup['screen_change'] && ($ext == 'avi' || $ext == '3gp' || $ext == 'mp4') && extension_loaded('ffmpeg')) {
                    $wh = explode('*', $setup['prev_size']);
                    if (file_exists($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $setup['ffmpeg_frame'] . '.gif')) {
                        $pre .= '<img style="margin: 1px; width:' . $wh[0] . '; height:' . $wh[1] . ';" src="' . DIRECTORY . $setup['ffmpegpath'] . '/' . htmlspecialchars($prev_pic) . '_frame_' . $setup['ffmpeg_frame'] . '.gif" alt=""/><br/>';
                    } else {
                        $pre .= '<img style="margin: 1px; width:' . $wh[0] . '; height:' . $wh[1] . ';" src="' . DIRECTORY . 'ffmpeg/' . $v['id'] . '" alt=""/><br/>';
                    }
                } else if($setup['screen_change'] && ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs' || $ext == 'apk')) {
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
            if (file_exists('ext/' . $ext . '.png')) {
                $ico = '<img src="' . DIRECTORY . 'ext/' . $ext . '.png" alt=""/>';
            } else {
            	$ico = '<img src="' . DIRECTORY . 'ext/stand.png" alt=""/>';
            }

            if ($setup['ext']) {
            	$extension = '(' . $ext . ')';
            } else {
            	$extension = '';
            }

            //Собсвенно вывод
            $out .= $pre . ' ' . $ico . '<strong><a href="' . DIRECTORY . 'view/' . $v['id'] . '">' . str_ireplace(htmlspecialchars($word, ENT_NOQUOTES), '<span class="yes">' . htmlspecialchars($word, ENT_NOQUOTES) . '</span>', htmlspecialchars($v['name'], ENT_NOQUOTES)) . '</a></strong>' . $extension . $v['size'] . '[<a href="' . DIRECTORY . $v['back'] . '">' . $_SESSION['language']['go to the category'] . '</a>]<br/></div>';
        }
    }


    //------------------------------------------------------------------------------------------
    $word = rawurlencode($_REQUEST['word']);
    if ($pages > 1) {
    	$out .= '<div class="iblock">' . $_SESSION['language']['pages'] . ': ';
    	$asd = $page - 2;
    	$asd2 = $page + 3;
    	if ($asd < $all && $asd > 0 && $page > 3) {
    		$out .= '<a href="' . DIRECTORY . 'search/1/' . $onpage . '/' . $prew . '/' . $word . '">1</a> ... ';
    	}
    	for ($i = $asd; $i < $asd2; ++$i) {
    		if ($i < $all && $i > 0) {
    			if ($i > $pages) {
    				break;
    			}
    			if ($page == $i) {
    				$out .= '<strong>[' . $i . ']</strong> ';
    			} else {
    				$out .= '<a href="' . DIRECTORY . 'search/' . $i . '/' . $onpage . '/' . $prew . '/' . $word . '">' . $i . '</a> ';
    			}
    		}
    	}
    	if ($i <= $pages) {
    		if ($asd2 < $all) {
    			$out .= ' ... <a href="' . DIRECTORY . 'search/' . $pages . '/' . $onpage . '/' . $prew . '/' . $word . '">' . $pages . '</a>';
    		}
    	}
    	$out .= '<br/></div>';
    }
    //------------------------------------------------------------------------------------------


    if ($setup['pagehand_change'] && $pages > $setup['pagehand']) {
        $out .= str_replace(array('%page%', '%pages%'), array($page, $pages), $_SESSION['language']['page']) . ':<br/><form action="' . DIRECTORY . 'search.php" method="get"><div class="row"><input class="enter" name="act" type="hidden" value="search"/><input class="enter" name="word" type="hidden" value="' . $word . '"/><input class="enter" name="page" type="text" maxlength="4" size="8"/><input class="buttom" type="submit" value="' . $_SESSION['language']['go'] . '"/></div></form>';
    }
    //------------------------------------------------------------------------------------------
    $out .= '<div class="iblock">- <a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a></div>';
} else {
    // Форма ввода слова
    $out .= '<div class="mblock"><img src="' . DIRECTORY . 'dis/s.png" alt=""/>' . $_SESSION['language']['find files'] . '</div><div class="mainzag"><form action="' . DIRECTORY . 'search.php?act=search" method="post"><div class="row">' . $_SESSION['language']['enter the name of the file you are'] . '</div><div class="row"><input class="enter" name="word" type="text"/><br/><input class="buttom" type="submit" value="' . $_SESSION['language']['go'] . '"/></div></form></div><div class="iblock"><a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/><a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a><br/></div>';
}

echo $out;

require 'moduls/foot.php';

?>
