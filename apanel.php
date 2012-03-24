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
@set_time_limit(99999);
ignore_user_abort(true);
$HeadTime = microtime(true);

require 'moduls/config.php';
require 'moduls/header.php';



$id = isset($_GET['id']) ? intval($_GET['id']) : 0;


mysql_query('REPLACE INTO `loginlog` SET `time` = UNIX_TIMESTAMP(), `access_num` = 0, `id` = 1', $mysql);
if (mysql_result(mysql_query('SELECT COUNT(1) FROM `loginlog`', $mysql), 0) > 21) {
    mysql_query('DELETE FROM `loginlog` WHERE `id` <> 1 ORDER BY `id` LIMIT 1', $mysql);
}
###################################################
if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
	error($setup['hackmess']);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
switch ($action) {
    case 'exit':
        session_destroy();
        echo 'Вы вышли из админки<br/><a href="index.php">Загрузки</a>';
        break;


default:
echo '<div class="mainzag">Админка</div>
<div class="row"><a href="apanel_news.php">Новости</a> (' . intval(mysql_result(mysql_query('SELECT COUNT(1) FROM `news`', $mysql), 0)) . ')</div>
<div class="row"><a href="apanel_index.php">Файловый менеджер</a></div>
<div class="row"><a href="apanel_scan.php">Полное обновление БД</a></div>
<div class="row"><a href="apanel.php?action=rot">Очистка БД от мусора</a></div>
<div class="row"><a href="apanel.php?action=upload">Upload файлов</a></div>
<div class="row"><a href="apanel.php?action=import">Импорт файлов</a></div>
<div class="row"><a href="apanel.php?action=setting">Настройки</a></div>
<div class="row"><a href="apanel.php?action=modules">Модули</a></div>
<div class="row"><a href="apanel.php?action=service">Сервис</a></div>
<div class="row"><a href="apanel.php?action=exchanger">Обменник</a></div>
<div class="row"><a href="apanel.php?action=library">Библиотека</a></div>
<div class="row"><a href="apanel.php?action=sec">Безопасность</a></div>
<div class="row"><a href="apanel.php?action=log">Лог авторизаций</a></div>
<div class="row"><a href="apanel.php?action=buy">Реклама</a></div>
<div class="row"><a href="apanel.php?action=id3">MP3 теги</a></div>
<div class="row"><a href="apanel.php?action=mark">Маркер картинок</a></div>
<div class="row"><a href="apanel.php?action=optm">Оптимизация БД</a></div>
<div class="row"><a href="apanel.php?action=clean">Очистка БД</a></div>
<div class="row"><a href="apanel.php?action=cleankomm">Очистка комментариев к файлам</a></div>
<div class="row"><a href="apanel.php?action=cleankomm_news">Очистка комментариев к новостям</a></div>
<div class="row"><a href="apanel.php?action=exit">Выход</a></div>';
break;


// расширенное сервисное использование
case 'service':
if ($_POST) {
	switch ($_GET['mode']) {
		default:
		if (
		mysql_query('REPLACE INTO setting(name, value) VALUES("service_head", "' . abs($_POST['head']) . '")', $mysql)
		&&
		mysql_query('REPLACE INTO setting(name, value) VALUES("service_foot", "' . abs($_POST['foot']) . '")', $mysql)
		) {
			echo '<div class="row">Настройки изменены<br/></div>';
		} else {
			error('Ошибка: ' . mysql_error($mysql));
		}
		break;
		
		case 'del':
		$user = intval($_POST['user']);
		if (
		mysql_query('DELETE FROM `users_profiles` WHERE `id` = ' . $user, $mysql)
		&&
		mysql_query('DELETE FROM `users_settings` WHERE `parent_id` = ' . $user, $mysql)
		) {
			echo '<div class="row">Пользователь удален<br/></div>';
		} else {
			error('Ошибка: ' . mysql_error($mysql));
		}
		break;
	}
} else {
echo '<div class="mainzag">
Пользователей: ' . mysql_result(mysql_query('SELECT COUNT(1) FROM `users_profiles`', $mysql), 0) . '<br/>
<form action="apanel.php?action=service" method="post">
<div class="row">
Ссылок вверху: <input type="text" name="head" value="' . $setup['service_head'] . '" size="3"/><br/>
Ссылок внизу: <input type="text" name="foot" value="' . $setup['service_foot'] . '" size="3"/><br/>
<input class="buttom" type="submit" value="Готово"/>
</div>
</form>
<form action="apanel.php?action=service&amp;mode=del" method="post">
<div class="row">
ID: <input type="text" name="user" size="4"/> <input class="buttom" type="submit" value="Удалить"/>
</div>
</form>
</div>';
}
break;


case 'exchanger':
if ($_POST) {
    $exchanger_notice = $_POST['exchanger_notice'] ? 1 : 0;
    $exchanger_hidden = $_POST['exchanger_hidden'] ? 1 : 0;
    $exchanger_extensions = mysql_real_escape_string($_POST['exchanger_extensions'], $mysql);
    $exchanger_name = mysql_real_escape_string($_POST['exchanger_name'], $mysql);


    if (
        mysql_query('REPLACE INTO setting(name, value) VALUES("exchanger_notice", "' . $exchanger_notice . '")', $mysql)
        &&
        mysql_query('REPLACE INTO setting(name, value) VALUES("exchanger_extensions", "' . $exchanger_extensions . '")', $mysql)
        &&
        mysql_query('REPLACE INTO setting(name, value) VALUES("exchanger_name", "' . $exchanger_name . '")', $mysql)
        &&
        mysql_query('REPLACE INTO setting(name, value) VALUES("exchanger_hidden", "' . $exchanger_hidden . '")', $mysql)
    ) {
        echo '<div class="row">Настройки изменены<br/></div>';
    } else {
        error('Ошибка: ' . mysql_error($mysql));
    }

} else {
echo '<div class="mainzag">
<form action="apanel.php?action=exchanger" method="post">
<div class="row">
Отправлять уведомления на Email о новых файлах: <input type="checkbox" name="exchanger_notice" ' . check($setup['exchanger_notice']) . '/><br/>
Делать загруженные файлы невидимыми: <input type="checkbox" name="exchanger_hidden" ' . check($setup['exchanger_hidden']) . '/><br/>
Регулярное выпажение для проверки имени файла:<br/>
<input type="text" value="' . htmlspecialchars($setup['exchanger_name'], ENT_NOQUOTES) . '" name="exchanger_name"/><br/>
Расширения файлов, разрешенные для загрузки, перечисленные через запятую:<br/>
<input style="width:95%" type="text" value="' . htmlspecialchars($setup['exchanger_extensions']) . '" name="exchanger_extensions"/><br/>
<input class="buttom" type="submit" value="Готово"/>
</div>
</form>
</div>';
}
break;


case 'library':
if ($_POST) {
    $lib = abs($_POST['lib']);
    $lib_str = abs($_POST['lib_str']);
    
    if (
    mysql_query('REPLACE INTO setting(name, value) VALUES("lib", "' . $lib . '")', $mysql)
    &&
    mysql_query('REPLACE INTO setting(name, value) VALUES("lib_str", "' . $lib_str . '")', $mysql)
    ) {
        echo '<div class="row">Настройки изменены<br/></div>';
    } else {
        error('Ошибка: ' . mysql_error($mysql));
    }
} else {
    echo '<div class="mainzag">
<form action="apanel.php?action=library" method="post">
<div class="row">
Максимальное число символов на страницу:<br/>
<input class="enter" name="lib" type="text" value="' . $setup['lib'] . '"/><br/>
Максимальное число символов на одну строку:<br/>
<input class="enter" name="lib_str" type="text" value="' . $setup['lib_str'] . '"/><br/>
<input class="buttom" type="submit" value="Готово"/>
</div>
</form>
</div>';
}
break;


case 'mark':
if (!$_POST) {
echo '<form action="apanel.php?action=mark" method="post">
<div class="mainzag">Маркер картинок<br/></div>
<div class="row">
<input name="marker" type="radio" value="1" ' . ($setup['marker'] == 1 ? 'checked="checked"' : '') . '/>Вкл
<input name="marker" type="radio" value="0" ' . ($setup['marker'] == 0 ? 'checked="checked"' : '') . '/>Выкл
<input name="marker" type="radio" value="2" ' . ($setup['marker'] == 2 ? 'checked="checked"' : '') . '/>Только в общем просмотре<br/>
Расположение<br/>
<select name="marker_where">
<option value="top"' . ($setup['marker_where'] == 'top' ? ' selected="selected"' : '') . '>вверху</option>
<option value="foot"' . ($setup['marker_where'] == 'foot' ? ' selected="selected"' : '') . '>внизу</option>
</select><br/>
<input class="buttom" type="submit" value="Готово"/>
</div>
</form>
<form action="apanel.php?action=mark" method="post">
<div class="mainzag">На картинки будет нанесена указанная надпись, удалить ее будет невозможно<br/></div>
<div class="row">
<input name="text" type="text"/><br/>
Расположение<br/>
<select name="y">
<option value="top">вверху</option>
<option value="foot">внизу</option>
</select><br/>
Шрифт<br/>
<input name="size" type="text" size="3" value="12"/><br/>
Цвет<br/>
<input name="color[]" type="text" size="3" maxlength="3" value="200"/>
<input name="color[]" type="text" size="3" maxlength="3" value="200"/>
<input name="color[]" type="text" size="3" maxlength="3" value="200"/><br/>
<input class="buttom" type="submit" value="Готово"/>
</div>
</form>';
} else {
    if (isset($_POST['marker'])) {
        if (
        mysql_query('REPLACE INTO setting(name, value) VALUES("marker", "' . intval($_POST['marker']) . '")', $mysql)
        &&
        mysql_query('REPLACE INTO setting(name, value) VALUES("marker_where", "' . ($_POST['marker_where'] == 'top' ? 'top' : 'foot') . '")', $mysql)
        ) {
            echo 'Настройки изменены';
        } else {
            echo 'Ошибка: ' . mysql_error($mysql);
        }
    } else {
        $q = mysql_query('SELECT `path` FROM `files` WHERE `path` LIKE "%.jpg" OR `path` LIKE "%.jpe" OR `path` LIKE "%.jpeg" OR `path` LIKE "%.gif" OR `path` LIKE "%.png"', $mysql);
        $all = mysql_num_rows($q);
        $i = $tmp = 0;
        while ($arr = mysql_fetch_row($q)) {
        	$tmp++;
        
        	if ($tmp == 1000) {
            	$tmp = 0;
        
            	// такая вот хуита... =( забиваем буфер
            	echo 'scan ' . htmlspecialchars($arr[0], ENT_NOQUOTES) . '...<br/>' . str_repeat(' ', 2048);
            	ob_flush();
        	}
        
        
            chmod($arr[0], 0666); // fix
            list($w, $h, $type) = getimagesize($arr[0]);


            switch ($type) {
                case 1:
                    $pic = imagecreatefromgif($arr[0]);
                    break;


                case 2:
                    $pic = imagecreatefromjpeg($arr[0]);
                    break;


                case 3:
                    $pic = imagecreatefrompng($arr[0]);
                    break;


                default:
                    $pic = false;
                    break;
            }

            if ($pic) {
                $f = false;

                // цвет
                $color = imagecolorallocate($pic, $_POST['color'][0], $_POST['color'][1], $_POST['color'][2]);

                // верх/низ
                if ($_POST['y'] == 'foot') {
                    $y = $h - ($_POST['size'] * 1.5);
                } else {
                    $y = intval($_POST['size']);
                }

                /*
                imagestring($pic, $_POST['size'], ($w/2)-(strlen($_POST['text'])*3), $y, $_POST['text'], $color);
                */
                imagettftext($pic, $_POST['size'], 0, ($w/2) - (iconv_strlen($_POST['text']) * 3), $y, $color, 'moduls/font.ttf', $_POST['text']);

                switch ($type) {
                    case 1:
                        $f = imagegif($pic, $arr[0]);
                        break;


                    case 2:
                        $f = imagejpeg($pic, $arr[0], 100);
                        break;


                    case 3:
                        $f = imagepng($pic, $arr[0], 100);
                        break;
                }

                if ($f) {
                	$i++;
                }
            }
        }
        echo 'Всего картинок: ' . $all . ', промаркированы: ' . $i;
    }
}
break;


// редактор MP3 тегов
case 'id3':
include 'moduls/PEAR/MP3/Id.php';
include 'moduls/mp3.class.php';
$id3 = new MP3_Id();

$genres = $id3->genres();


if (!$_POST) {

if ($id) {

$tmp = mysql_fetch_row(mysql_query('SELECT `path` FROM `files` WHERE `id`=' . $id, $mysql));

$id3->read($tmp[0]);

$name = str_to_utf8($id3->name);
$artists = str_to_utf8($id3->artists);
$album = str_to_utf8($id3->album);
$year = str_to_utf8($id3->year);
$track = str_to_utf8($id3->track);
$genre = str_to_utf8($id3->genre);
$comment = str_to_utf8($id3->comment);


echo '<div class="mainzag">Редактор MP3 тегов<br/></div>
<div class="row">
<form action="apanel.php?action=id3&amp;id=' . $id . '" method="post">
<div class="row">
Название<br/>
<input name="name" type="text" value="' . $name . '"/><br/>
Артист<br/>
<input name="artists" type="text" value="' . $artists . '"/><br/>
Альбом<br/>
<input name="album" type="text" value="' . $album . '"/><br/>
Год<br/>
<input name="year" type="text" value="' . $year . '"/><br/>
Трек<br/>
<input name="track" type="text" value="' . $track . '"/><br/>
Жанр<br/>
<select name="genre"><option value="' . $genre . '"/>' . $genre . '</option>';

foreach ($genres as $var) {
    if ($var == $genre) {
        continue;
    }
    echo '<option value="' . htmlspecialchars($var) . '">' . htmlspecialchars($var, ENT_NOQUOTES) . '</option>';
}

echo '</select><br/>
Комментарии<br/>
<textarea name="comment" rows="2" cols="32">' . $comment . '</textarea><br/>
<input class="buttom" type="submit" value="Задать"/>
</div>
</form>
</div>';
} else {
echo '<div class="mainzag">Модуль задаст всем MP3 файлам указанные теги. Если поле пустое, то тег изменяться не будет<br/></div>
<div class="row">
<form action="apanel.php?action=id3" method="post">
<div class="row">
Название<br/>
<input name="name" type="text"/><br/>
Артист<br/>
<input name="artists" type="text"/><br/>
Альбом<br/>
<input name="album" type="text"/><br/>
Год<br/>
<input name="year" type="text"/><br/>
Трек<br/>
<input name="track" type="text"/><br/>
Жанр<br/>
<select name="genre"><option value=""></option>';

foreach ($genres as $var) {
    echo '<option value="' . htmlspecialchars($var) . '">' . htmlspecialchars($var, ENT_NOQUOTES) . '</option>';
}

echo '</select><br/>
Комментарии<br/>
<textarea name="comment" rows="2" cols="32"></textarea><br/>
<input class="buttom" type="submit" value="Задать"/>
</div>
</form>
</div>';
}
} else {
    if ($id) {
        $tmp = mysql_fetch_row(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));
        chmod($tmp[0], 0666); // fix
        $id3->read($tmp[0]);
        
        $name = iconv('utf-8', 'windows-1251', $_POST['name']);
        $artist = iconv('utf-8', 'windows-1251', $_POST['artists']);
        $album = iconv('utf-8', 'windows-1251', $_POST['album']);
        $year = iconv('utf-8', 'windows-1251', $_POST['year']);
        $track = iconv('utf-8', 'windows-1251', $_POST['track']);
        $genre = iconv('utf-8', 'windows-1251', $_POST['genre']);
        $comment = iconv('utf-8', 'windows-1251', $_POST['comment']);


        // Записываем Idv2 теги
        $mp3 = new mp3($tmp[0]);
        //$mp3->striptags(); // bug
        $mp3->setIdv3_2($track, $name, $artist, $album, $year, $genre, $comment, $artist, $artist, $comment, 'http://' . $_SERVER['HTTP_HOST'], '');
        $mp3->save($tmp[0]);


        // записываем Idv1 теги
        $id3->name = $name;
        $id3->artists = $artist;
        $id3->album = $album;
        $id3->year = $year;
        $id3->track = $track;
        $id3->genre = $genre;
        $id3->comment = $comment;
        $id3->write();

        echo 'Теги изменены';
    } else {

        if ($_POST['name'] != '') {
        	$_POST['name'] = iconv('utf-8', 'windows-1251', $_POST['name']);
        }
        if ($_POST['artists'] != '') {
        	$_POST['artists'] = iconv('utf-8', 'windows-1251', $_POST['artists']);
        }
        if ($_POST['album'] != '') {
        	$_POST['album'] = iconv('utf-8', 'windows-1251', $_POST['album']);
        }
        if ($_POST['year'] != '') {
        	$_POST['year'] = iconv('utf-8', 'windows-1251', $_POST['year']);
        }
        if ($_POST['track'] != '') {
        	$_POST['track'] = iconv('utf-8', 'windows-1251', $_POST['track']);
        }
        if ($_POST['genre'] != '') {
        	$_POST['genre'] = iconv('utf-8', 'windows-1251', $_POST['genre']);
        }
        if ($_POST['comment'] != '') {
        	$_POST['comment'] = iconv('utf-8', 'windows-1251', $_POST['comment']);
        }

        $all = 0;
        $q = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "0" AND `path` LIKE("%.mp3")', $mysql);
        while ($f = mysql_fetch_row($q)) {

        	// Записываем Idv2 теги
        	$mp3 = new mp3($f[0]);
        	//$mp3->striptags(); // bug
        	$mp3->setIdv3_2($_POST['track'], $_POST['name'], $_POST['artist'], $_POST['album'], $_POST['year'], $_POST['genre'], $_POST['comment'], $_POST['artist'], $_POST['artist'], $_POST['comment'], 'http://' . $_SERVER['HTTP_HOST'], '');
        	$mp3->save($f[0]);

            if (PEAR::isError($id3->read($f[0]))) {
                continue;
            }
            $all++;

        	if ($_POST['name'] != '') {
        		$id3->name = $_POST['name'];
        	}
        	if ($_POST['artists'] != '') {
        		$id3->artists = $_POST['artists'];
        	}
        	if ($_POST['album'] != '') {
        		$id3->album = $_POST['album'];
        	}
        	if ($_POST['year'] != '') {
        		$id3->year = $_POST['year'];
        	}
        	if ($_POST['track'] != '') {
        		$id3->track = $_POST['track'];
        	}
        	if ($_POST['genre'] != '') {
        		$id3->genre = $_POST['genre'];
        	}
        	if ($_POST['comment'] != '') {
        		$id3->comment = $_POST['comment'];
        	}
        	$id3->write();
        }

        echo 'Теги заданы для ' . $all . ' файлов';
    }
}
break;


######################################ЛОГ######################################################
case 'pos':
$file_info = mysql_fetch_assoc(mysql_query('SELECT `name`, `path` FROM `files` WHERE `id` = ' . $id, $mysql));
if (!is_dir($file_info['path'])) {
	error('Error');
}
//$file_info['name'] = str_replace('*','',$file_info['name']);
if ($_GET['to'] == 'down') {
	$query = 'UPDATE `files` SET `priority` = `priority` - 1 WHERE `id` = ' . $id;
} else {
	$query = 'UPDATE `files` SET `priority` = `priority` + 1 WHERE `id` = ' . $id;
}
if (mysql_query($query, $mysql)) {
	echo '<div class="mainzag">Приоритет каталога ' . $file_info['name'] . ' изменен!</div>';
} else {
	echo '<div class="minizag">Ошибка при изменении приоритета</div>';
}
break;


######################################ЛОГ######################################################
case 'rot':
ob_implicit_flush(1);

$d = $tmp = 0;
$r = mysql_query('SELECT `id`, `path` FROM `files`', $mysql);
while ($a = mysql_fetch_row($r)) {
    $tmp++;
    if ($tmp > 1000) {
        $tmp = 0;
        echo 'scan ' . htmlspecialchars($a[1], ENT_NOQUOTES) . str_repeat(' ', 2048) . '...<br/>';
		ob_flush();
    }

	if (!file_exists($a[1])) {
		mysql_query('DELETE FROM `files` WHERE `id` = ' . $a[0], $mysql);
		mysql_query('DELETE FROM `komments` WHERE `file_id` = ' . $a[0], $mysql);

        dir_count($a[1], false);

		$d++;
		// заглушка
		echo '<strong class="no">DEL ' . htmlspecialchars($a[1], ENT_NOQUOTES) . '...<br/></strong>';
		ob_flush();
	}
}
echo '<div class="mainzag">База данных успешно обновлена!</div><div class="row">Удалено неверных записей: ' . $d . '</div>';
break;


######################################ЛОГ######################################################
case 'flash':
$file_info = mysql_fetch_assoc(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id . ' AND `dir` = "1"', $mysql));

if (!is_dir($file_info['path'])) {
	error('Такой категории не существует.');
}

echo '<div class="mainzag">Будет пересканирована директория <strong>' . $file_info['path'] . '</strong><br/>Для продолжения нажмите на <a class="yes" href="apanel_scan.php?scan=' . rawurlencode($file_info['path']) . '">ЭТУ</a> ссылку<br/></div>';
break;


######################################ЛОГ######################################################
case 'log':
$q = mysql_query('SELECT * FROM `loginlog` WHERE `id` > 1 ORDER BY `time` DESC', $mysql);
echo '<div class="mainzag">Лог последних 20 посещений админки([UserAgent] [IP] [Time]):</div><div class="row">';
while ($log = mysql_fetch_assoc($q)) {
	echo '[' . htmlspecialchars($log['ua'], ENT_NOQUOTES) . '] [' . $log['ip'] . '] [' . tm($log['time']) . ']<br/>';
}
echo '</div>';
break;


######################################ЛОГ######################################################
case 'addico':
$file_info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
if (!$_FILES) {
echo '<div class="mainzag">Загрузка иконки к папке</div>
<div class="row">
<form action="apanel.php?action=addico&amp;id=' . $id . '" method="post" enctype="multipart/form-data">
<div class="row">
Файл будет скопирован в назначеную папку:<br/>
<input name="ico" type="file"/><br/>
<input class="buttom" type="submit" value="Добавить"/>
</div>
</form>
</div>';
} else {
    $to = $file_info['path'] . 'folder.png';

    if (strtolower(pathinfo($_FILES['ico']['name'], PATHINFO_EXTENSION)) != 'png') {
    	error('Поддерживаются иконки только png формата');
    }
    if (file_exists($to)) {
    	error('Файл уже существует');
    }
    chmod($file_info['path'], 0777);
    if (move_uploaded_file($_FILES['ico']['tmp_name'], $to)) {
        echo 'Закачка иконки прошла успешно.<br/>';
        chmod($to, 0644);
    } else {
        echo 'Закачка иконки окончилась неудачно.<br/>';
        //chmod($file_info['path'], 0777);
    }
}
break;


######################################ЛОГ######################################################
case 'reico':
$file_info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
if (!file_exists($file_info['path'] . 'folder.png')) {
	error('Иконки к данной папке не существует');
}
chmod($file_info['path'] . 'folder.png', 0777);
if (unlink($file_info['path'] . 'folder.png')) {
	echo 'Удаление иконки прошло успешно.<br/>';
} else {
	echo 'Удаление иконки окончилось неудачно.<br/>';
}
break;


######################################РАСПАКОВЩИК###############################################
case 'unpack':
$file = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
$dir = dirname($file['path']) . '/';
chmod($dir, 0777);

include 'moduls/PEAR/pclzip.lib.php';
$zip = new PclZip($file['path']);

if ($zip->extract(PCLZIP_OPT_PATH, $dir)) {
	error('Ахрив распакован в ' . $dir . '<br/>Не забудьте обновить БД.');
} else {
	error('Ошибка при распаковке.');
}
break;


######################################УДАЛЕНИЕ ПАПКИ######################################################
case 'redir':
if (!$setup['delete_dir']) {
	error($setup['hackmess']);
}
if (!$_GET['level']) {
    echo 'Будут удалены все файлы в каталоге, а также сам каталог. Продолжить?<br/><a href="apanel.php?action=redir&amp;level=1&amp;id=' . $id . '">Да, продолжить</a>';
} else {
    $file = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id . ' ORDER BY `name`', $mysql));
    if (!is_dir($file['path'])) {
    	error('Такой категории не существует!');
    }

    $ex = explode('/', $file['path']);
    $f_chmod = '';
    foreach ($ex as $chmod) {
        $f_chmod .= $chmod . '/';
        chmod($f_chmod, 0777);
    }

    foreach (glob($file['path'] . '*') as $vv) {
    	if (is_dir($vv)) {
    		error('Разрешено удалять только папки с 1 уровнем вложенности!');
    	} else {
    	    if (!unlink($vv)) {
    		    error('Ошибка при удалении файла ' . htmlspecialchars($vv, ENT_NOQUOTES));
    	    }
    	}
    }
    if (!mysql_query("DELETE FROM `files` WHERE `infolder` = '" . mysql_real_escape_string($file['path'], $mysql) . "'", $mysql)) {
    	error('Ошибка при удалении файлов из базы');
    }

    if (!rmdir($file['path'])) {
    	error('Ошибка при удалении каталога');
    }

    if (!mysql_query('DELETE FROM `files` WHERE `id` = ' . $id, $mysql)) {
    	error('Ошибка при удалении каталога из базы');
    }


    $f_chmod = '';
    foreach ($ex as $chmod) {
        $f_chmod .= $chmod . '/';
        chmod($f_chmod.'/', 0777);
    }


    echo 'Каталог успешно удален!<div class="mainzag" style="color:#b00;">Внимание! Теперь следует пересчитать количество файлов в папках<br/>Для продолжения нажмите на <a href="apanel_count.php">ЭТУ</a> ссылку</div>';
}
break;


######################################УДАЛЕНИЕ ФАЙЛА###############################################
case 'refile':
if (!$setup['delete_dir']) {
	error($setup['hackmess']);
}
$file = mysql_fetch_assoc(mysql_query('SELECT `path`, `hidden`, `infolder`, `attach` FROM `files` WHERE `id` = ' . $id, $mysql));
if (!is_file($file['path'])) {
	error('Такого файла не существует!');
}


$ex = explode('/', $file['path']);
$f_chmod = '';
foreach ($ex as $chmod) {
    $f_chmod .= $chmod;
    if (is_dir($f_chmod)) {
        $f_chmod = $f_chmod.'/';
    }

    @chmod($f_chmod,0777);
}

if (!mysql_query('DELETE FROM `files` WHERE `id` = ' . $id, $mysql)) {
	error('Ошибка при удалении файла из базы');
}

if (!unlink($file['path'])) {
	error('Ошибка при удалении файла ' . htmlspecialchars($file['path'], ENT_NOQUOTES));
}

if ($file['attach']) {
    del_attach($file['infolder'], $id, unserialize($file['attach']));
}

if (!$file['hidden']) {
    dir_count($file['path'], false);
}

echo 'Файл <strong>' . htmlspecialchars($file['path'], ENT_NOQUOTES) . '</strong> удален!';
break;


######################################РЕКЛАМА##################################################
case 'buy':
if (!$_POST) {
echo '<div class="mainzag">Рекламный блок:</div>
<div class="row">
<form action="apanel.php?action=buy" method="post">
<div class="row">
XHTML код отображаемый сверху:
<textarea class="enter" cols="32" rows="5" name="text">' . htmlspecialchars($setup['buy'], ENT_NOQUOTES) . '</textarea><br/>
В случайном порядке:
<input name="randbuy" type="checkbox" ' . check($setup['randbuy']) . '/><br/>
Количество отображаемых строк:
<input name="countbuy" type="text" value="' . intval($setup['countbuy']) . '" size="3"/><br/>
XHTML код отображаемый снизу:
<textarea class="enter" cols="32" rows="5" name="banner">' . htmlspecialchars($setup['banner'], ENT_NOQUOTES) . '</textarea><br/>
В случайном порядке:
<input name="randbanner" type="checkbox" ' . check($setup['randbanner']) . '/><br/>
Количество отображаемых строк:
<input name="countbanner" type="text" value="' . intval($setup['countbanner']) . '" size="3"/><br/>
<input class="buttom" type="submit" value="Сохранить"/>
</div>
</form>
</div>';
} else {
    if ($_POST['text'] == '') {
    	error('Не заполнено поле');
    }

    if (
        mysql_query("REPLACE INTO setting(name, value) VALUES('buy', '" . mysql_real_escape_string($_POST['text'], $mysql) . "')", $mysql)
        &&
        mysql_query("REPLACE INTO setting(name, value) VALUES('randbuy', '" . ($_POST['randbuy'] ? 1 : 0) . "')", $mysql)
        &&
        mysql_query("REPLACE INTO setting(name, value) VALUES('countbuy', '" . abs($_POST['countbuy']) . "')", $mysql)
        &&
        mysql_query("REPLACE INTO setting(name, value) VALUES('banner', '" . mysql_real_escape_string($_POST['banner'], $mysql) . "')", $mysql)
        &&
        mysql_query("REPLACE INTO setting(name, value) VALUES('randbanner', '" . ($_POST['randbanner'] ? 1 : 0) . "')", $mysql)
        &&
        mysql_query("REPLACE INTO setting(name, value) VALUES('countbanner', '" . abs($_POST['countbanner']) . "')", $mysql)
    ) {
    	echo 'Настройки сохранены.';
    } else {
    	error('Ошибка при записи в БД.<br/>' . mysql_error($mysql));
    }
}
break;


######################################ПЕРЕИМЕНОВАНИЕ##################################################
case 'rename':
if ($_POST) {
    $eng = mysql_real_escape_string($_POST['new']['english'], $mysql);
    $rus = mysql_real_escape_string($_POST['new']['russian'], $mysql);

    mysql_query("UPDATE `files` SET name = '" . $eng . "', rus_name = '" . $rus . "' WHERE `id` = " . $id, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
    	error('Ошибка при переименовании.<br/>' . $error);
    }
    echo 'Файл переименован';
} else {
    $file = mysql_fetch_assoc(mysql_query('SELECT `name`, `rus_name` FROM `files` WHERE `id` = ' . $id, $mysql));

    echo '<div class="mainzag">Введите новое имя:</div><div class="row"><form method="post" action="apanel.php?action=rename&amp;id=' . $id . '"><div class="row">';
    language_dir($file['name'], $file['rus_name']);
    echo '<input class="buttom" type="submit" value="Готово"/></div></form></div>';
}
break;


#########################################ОЧИСТКА КОММЕНТОВ К ФАЙЛУ#########################################
case 'clearkomm':
mysql_query('DELETE FROM `komments` WHERE `file_id` = ' . $id, $mysql);
$error = mysql_error($mysql);
if ($error) {
	error('Ошибка при сбросе.<br/>' . $error);
}
echo 'Комментарии удалены.';
break;


##############################################ОЧИСТКА РЕЙТИНГА К ФАЙЛУ#######################################
case 'cleareval':
if (mysql_query('UPDATE `files` SET `ips` = "", `yes` = 0, `no` = 0 WHERE `id` = ' . $id, $mysql)) {
	echo 'Рейтинг удален.';
} else {
	error('Ошибка при сбросе рейтинга.<br/>' . mysql_error($mysql));
}
break;


#############################################ОПТИМИЗАЦИЯ БД###########################################
case 'optm':
$q = mysql_query('SHOW TABLES', $mysql);
while ($arr = mysql_fetch_row($q)) {
	mysql_query('OPTIMIZE TABLE `' . $arr[0] . '`;', $mysql);
}
echo 'Таблицы оптимизированы.';
break;


################################################ОЧИСТКА БД########################################
case 'clean':
if (!isset($_GET['level'])) {
    echo 'Будут удалены все данные БД, включая описания, счетчики закачек, рейтинги и комментарии. Продолжить?<br/><a href="apanel.php?action=clean&amp;level=1">Да, продолжить</a>';
} else {
    if(mysql_query('TRUNCATE TABLE `files`;', $mysql) && mysql_query('TRUNCATE TABLE `komments`;', $mysql)) {
    	echo 'Таблицы очищены.<br/>';
    } else {
    	error('Ошбка: ' . mysql_error($mysql));
    }
}
break;


##########################################ОЧИСТКА КОММЕНТОВ к файлам##############################################
case 'cleankomm':
if (!$_GET['level']) {
	echo 'Будут удалены все комментарии к файлам! Продолжить?<br/><a href="apanel.php?action=cleankomm&amp;level=1">Да, продолжить</a>';
} else {
    if (mysql_query('TRUNCATE TABLE `komments`;', $mysql)) {
        echo 'Таблица комментариев очищена.<br/>';
    } else {
    	error('Ошибка: ' . mysql_error($mysql));
    }
}
break;


##########################################ОЧИСТКА ВСЕХ КОММЕНТОВ##############################################
case 'cleankomm_news':
if (!$_GET['level']) {
	echo 'Будут удалены все комментарии к новстям! Продолжить?<br/><a href="apanel.php?action=cleankomm_news&amp;level=1">Да, продолжить</a>';
} else {
	if (mysql_query('TRUNCATE TABLE `news_komments`;', $mysql)) {
	    echo 'Таблица комментариев очищена.<br/>';
	} else {
		error('Ошибка: ' . mysql_error($mysql));
	}
}
break;


case 'clean_cache':
$err = '';

$h = opendir($_GET['dir']);

while (($f = readdir($h)) !== false) {
	if ($f == '.htaccess' || $f == '.' || $f == '..') {
		continue;
	}
	//chmod($_GET['dir'].'/'.$f, 0666);
	if (!unlink($_GET['dir'] . '/' . $f)) {
		$err .= htmlspecialchars($_GET['dir'] . '/' . $f, ENT_NOQUOTES) . '<br/>';
	}
}

if ($err) {
	error('Не удалось удалить следующие файлы:<br/>' . $err);
} else {
	echo 'Кэш успешно очищен.<br/>';
}
break;


#########################################SEO########################################
case 'seo':
if (!$_POST) {
    $file = mysql_fetch_assoc(mysql_query('SELECT `name`, `seo` FROM `files` WHERE `id` = ' . $id, $mysql));
    $seo = unserialize($file['seo']);

    echo '<div class="mainzag">SEO <strong>' . htmlspecialchars($file['name'], ENT_NOQUOTES) . '</strong></div>
<div class="row">
<form action="apanel.php?action=seo&amp;id=' . $id . '" method="post">
<div class="row">Title<br/>
<input style="width: 95%" type="text" name="title" value="' . htmlspecialchars($seo['title']) . '"/><br/>
Keywords<br/>
<input style="width: 95%" type="text" name="keywords" value="' . htmlspecialchars($seo['keywords']) . '"/><br/>
Description<br/>
<input style="width: 95%" type="text" name="description" value="' . htmlspecialchars($seo['description']) . '"/><br/>
<input class="buttom" type="submit" value="Изменить"/>
</div>
</form></div>';
} else {
    $seo = serialize(array(
        'title' => $_POST['title'],
        'keywords' => $_POST['keywords'],
        'description' => $_POST['description']
    ));
    if (mysql_query('UPDATE `files` SET `seo` = "' . mysql_real_escape_string($seo, $mysql) . '" WHERE `id` = ' . $id, $mysql)) {
        echo 'Данные изменены<br/>';
    } else {
        error('Данные не изменены');
    }

    echo '<a href="apanel_index.php">Файл-менеджер</a>';
}
break;


#########################################ДОБАВЛЕНИЕ И ИЗМЕНЕНИЕ ОПИСАНИЯ########################################
case 'about':
$file = mysql_fetch_assoc(mysql_query('SELECT `name`, `path` FROM `files` WHERE `id` = ' . $id, $mysql));
$about = $setup['opath'] . iconv_substr($file['path'], iconv_strlen($setup['path'])) . '.txt';

if (!$_POST) {
    echo '<div class="mainzag">Описание файла/директории <strong>' . htmlspecialchars($file['name'], ENT_NOQUOTES) . '</strong></div>
<div class="row">
<form action="apanel.php?action=about&amp;id=' . $id . '" method="post">
<div class="row">
<textarea class="enter" cols="70" rows="10" name="text">' . htmlspecialchars(antibb(file_get_contents($about)), ENT_NOQUOTES, 'UTF-8') . '</textarea><br/><br/>
<input class="buttom" type="submit" value="Написать"/>
</div>
</form></div>';
} else {
	chmods($about);

    if ($_POST['text'] == '') {
    	if (unlink($about)) {
    		echo 'Описание удалено<br/>';
    	} else {
    		error('Описание не удалено');
    	}
    } else {
    	if (file_put_contents($about, nl2br(bbcode(htmlspecialchars(trim($_POST['text'])))))) {
    		echo 'Описание изменено<br/>';
    	} else {
    		error('Описание не изменено');
    	}
    }

    echo '<a href="apanel_view.php?id=' . $id . '">К описанию</a>';
}
break;


#########################################ИМПОРТ####################################################################
case 'import':
if (!$_POST) {
$dirs = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "1"', $mysql);

echo '<div class="mainzag">Импорт файлов</div>
<div class="row">Сохранить в:</div>
<form action="apanel.php?action=import" method="post">
<div class="row">
Импортируемый файл # с каким именем сохранить<br/>
<select class="buttom" name="topath">
<option value="' . htmlspecialchars($setup['path']) . '/">/</option>';
while ($item = mysql_fetch_assoc($dirs)) {
	echo '<option value="' . htmlspecialchars($item['path']) . '">' . htmlspecialchars(substr(strstr($item['path'], '/'), 1), ENT_NOQUOTES) . '</option>';
}
echo '</select><br/>
Файлы:<br/>
<textarea class="enter" cols="70" rows="10" name="files"></textarea><br/><br/>
<input class="buttom" type="submit" value="Импорт"/>
</div>
</form>';
} else {
    $newpath = trim($_POST['topath']);
    if (empty($newpath)) {
    	error('Нет конечного пути!');
    }
    $text = explode("\n", $_POST['files']);
    $a = sizeof($text);
    for ($i = 0; $i < $a; ++$i) {
    	$parametr = explode('#', trim($text[$i]));
    	if (!isset($parametr[1])) {
    		$parametr[1] = basename(trim($parametr[0]));
    	}
    	$to = $newpath . trim($parametr[1]);
    	if (file_exists($to)) {
    		error('Файл ' . $to . ' уже существует');
    	}
    	if (!checkExt(pathinfo(trim($parametr[0]), PATHINFO_EXTENSION))) {
            error($setup['hackmess']);
        }
    	chmod($newpath, 0777);

    	ini_set('user_agent', $_SERVER['HTTP_USER_AGENT']);
    	if (copy(trim($parametr[0]), $to)) {
        	echo 'Импорт файла ' . htmlspecialchars($parametr[1], ENT_NOQUOTES) . ' удался<br/>';
        	$rus_name = $name = basename($to, '.' . pathinfo($to, PATHINFO_EXTENSION));

        	$infolder = dirname($to) . '/';
        	mysql_query("INSERT INTO `files` (`path`, `name`, `rus_name`, `infolder`, `size`, `timeupload`) VALUES ('" . mysql_real_escape_string($to, $mysql) . "', '" . mysql_real_escape_string($name, $mysql) . "', '" . mysql_real_escape_string($rus_name, $mysql) . "', '" . mysql_real_escape_string($infolder, $mysql) . "', " . filesize($to) . ", " . filectime($to) . ");", $mysql);
        	dir_count($infolder, true);
    	} else {
            $err = error_get_last();
            error('Импорт файла ' . htmlspecialchars($parametr[1], ENT_NOQUOTES) . ' не удался<br/>' . $err['message']);
    	}
    }
    chmod($newpath, 0777);
}
break;


#####################################АПЛОАД скрина###################################################
case 'screen':
$info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
$info['path'] = strstr($info['path'], '/'); // убираем папку с загрузками
$to = $setup['spath'] . $info['path'] . '.gif'; // имя конечного файла
$thumb = $setup['spath'] . $info['path'] . '.thumb.gif'; // имя конечного файла

if (!$_FILES) {
echo '<div class="mainzag">Загрузка скрина (JPEG, GIF, PNG)</div>
<form action="apanel.php?action=screen&amp;id=' . $id . '" method="post" enctype="multipart/form-data">
<div class="row">
Файл будет скопирован в папку со скриншотами:<br/>
<input name="scr" type="file"/><br/>
<input class="buttom" type="submit" value="Добавить"/>
</div>
</form>';
} else {
    $ex = pathinfo($_FILES['scr']['name']);
    $ext = strtolower($ex['extension']);

    if ($ext != 'gif' && $ext != 'jpg' && $ext != 'jpe' && $ext != 'jpeg' && $ext != 'png') {
    	error('Поддерживаются скриншоты только gif, jpeg, png форматов');
    }

    chmods($to);

    if (move_uploaded_file($_FILES['scr']['tmp_name'], $to)) {
        echo 'Закачка скрина ' . htmlspecialchars($_FILES['scr']['name'], ENT_NOQUOTES) . ' прошла успешно.<br/>';

        if ($ext == 'jpg' || $ext == 'jpe' || $ext == 'jpeg') {
            $im = imagecreatefromjpeg($to);
            imagegif($im, $to);
            imagedestroy($im);
        } elseif($ext == 'png') {
            $im = imagecreatefrompng($to);
            imagegif($im, $to);
            imagedestroy($im);
        }
        img_resize($to, $thumb, 0, 0, $setup['marker']);
    } else {
        $err = error_get_last();
        error('Закачка скрина ' . htmlspecialchars($_FILES['scr']['name'], ENT_NOQUOTES) . ' окончилась неудачно<br/>' . $err['message']);
    }
}
break;


case 'del_screen':
$info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
$info['path'] = strstr($info['path'], '/'); // убираем папку с загрузками
$to = $setup['spath'] . $info['path'] . '.gif'; // имя конечного файла
$to2 = $setup['spath'] . $info['path'] . '.jpg'; // имя конечного файла

if (unlink($to) || unlink($to2)) {
	echo 'Скриншот удален.<br/>';
} else {
    $err = error_get_last();
	error('Ошибка при удалении скриншота<br/>' . $err['message']);
}
break;


#####################################АПЛОАД###################################################
case 'upload':
if (!$_POST) {
$dirs = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "1"', $mysql);

echo '<script type="text/javascript" src="js.js"></script>
<div class="mainzag">Upload файлов (max ' . ini_get('upload_max_filesize') . ')</div>
<div class="row">Сохранить в:</div>
<form action="apanel.php?action=upload" method="post" enctype="multipart/form-data">
<div class="row">
<select class="buttom" name="topath">
<option value="' . $setup['path'] . '/">./</option>';
while ($item = mysql_fetch_assoc($dirs)) {
	echo '<option value="' . htmlspecialchars($item['path']) . '">' . htmlspecialchars(substr(strstr($item['path'], '/'), 1), ENT_NOQUOTES) . '</option>';
}
echo '</select><br/>
Добавить файлы: <a href="#" onclick="Apanel.files(1);">[+]</a> / <a href="#" onclick="Apanel.files(0);">[-]</a><br/>

<div id="tpl" style="display: none;"><input name="userfile[0]" type="file"/> <a href="#" onclick="Apanel.filesAttach(this, 1);">[+]</a> / <a href="#" onclick="Apanel.filesAttach(this, 0);">[-]</a><br/></div>
<span id="tplAttach" style="display: none; margin-left: 20px;"><input name="attach_userfile[0][]" type="file"/><br/></span>
<div id="fl">
<div><input name="userfile[0]" type="file"/> <a href="#" onclick="Apanel.filesAttach(this, 1);">[+]</a> / <a href="#" onclick="Apanel.filesAttach(this, 0);">[-]</a><br/></div>
<div><input name="userfile[1]" type="file"/> <a href="#" onclick="Apanel.filesAttach(this, 1);">[+]</a> / <a href="#" onclick="Apanel.filesAttach(this, 0);">[-]</a><br/></div>
<div><input name="userfile[2]" type="file"/> <a href="#" onclick="Apanel.filesAttach(this, 1);">[+]</a> / <a href="#" onclick="Apanel.filesAttach(this, 0);">[-]</a><br/></div>
<div><input name="userfile[3]" type="file"/> <a href="#" onclick="Apanel.filesAttach(this, 1);">[+]</a> / <a href="#" onclick="Apanel.filesAttach(this, 0);">[-]</a><br/></div>
<div><input name="userfile[4]" type="file"/> <a href="#" onclick="Apanel.filesAttach(this, 1);">[+]</a> / <a href="#" onclick="Apanel.filesAttach(this, 0);">[-]</a><br/></div>
</div>
<input class="buttom" type="submit" value="Добавить"/>
</div>
</form>';
} else {
    $newpath = trim($_POST['topath']);
    if (empty($newpath)) {
    	error('Нет конечного пути! ' . htmlspecialchars($newpath, ENT_NOQUOTES));
    }
    $a = sizeof($_FILES['userfile']['name']);
    for ($i = 0; $i < $a; ++$i) {
    	if (empty($_FILES['userfile']['name'][$i])) {
    		continue;
    	}
        $name = $_FILES['userfile']['name'][$i];
        $to = $newpath . $name;
        if (!checkExt(pathinfo($name, PATHINFO_EXTENSION))) {
            error($setup['hackmess']);
        }
        if (file_exists($to)) {
        	error('Файл <strong>' . htmlspecialchars($to, ENT_NOQUOTES) . '</strong> уже существует');
        }
        chmod($newpath, 0777);
        if (move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $to)) {
            echo 'Закачка файла <strong>' . htmlspecialchars($name, ENT_NOQUOTES) . '</strong> прошла успешно.<br/>';
            $rus_name = $name = basename($to, '.' . pathinfo($to, PATHINFO_EXTENSION));
            $infolder = dirname($to) . '/';


            $files = $dbFiles = array();
            if (isset($_FILES['attach_userfile']['tmp_name'][$i])) {
                foreach ($_FILES['attach_userfile']['tmp_name'][$i] as $k => $v) {
                    if ($_FILES['attach_userfile']['name'][$i][$k]) {
                        if (!checkExt(pathinfo($_FILES['attach_userfile']['name'][$i][$k], PATHINFO_EXTENSION))) {
                            error($setup['hackmess']);
                        }
                        $dbFiles[] = $_FILES['attach_userfile']['name'][$i][$k];
                        $files[] = array('tmp_name' => $v, 'name' => $_FILES['attach_userfile']['name'][$i][$k]);
                    }
                }
            }

            mysql_query("
                INSERT INTO `files` (
                    `dir`, `path`, `name`, `rus_name`, `infolder`, `size`, `timeupload`, `attach`
                ) VALUES (
                    '0',
                    '" . mysql_real_escape_string($to, $mysql) . "',
                    '" . mysql_real_escape_string($name, $mysql) . "',
                    '" . mysql_real_escape_string($rus_name, $mysql) . "',
                    '" . mysql_real_escape_string($infolder, $mysql) . "' ,
                    " . filesize($to) . ",
                    " . filectime($to) . ",
                    " . ($dbFiles ? "'" . mysql_real_escape_string(serialize($dbFiles), $mysql) . "'" : 'NULL') . "
                );
            ", $mysql);
            $id = mysql_insert_id($mysql);
            if ($files) {
                add_attach($newpath, $id, $files);
            }

            dir_count($infolder, true);

            chmod($to, 0644);
        } else {
        	error('Закачка файла ' . htmlspecialchars($name, ENT_NOQUOTES) . ' окончилась неудачно');
        }
    }
    chmod($newpath, 0777);
}
break;


######################################СОЗДАНИЕ НОВОГО КАТАЛОГА##############################################
case 'newdir':
if ($_POST) {
    if (!preg_match('/^[A-Z0-9_\-]+$/i', $_POST['realname'])) {
        error('Не указано имя папки или оно содержит недопустимые символы. Разрешены [A-Z0-9_-]');
    }
    if ($_POST['new']['english'] == '' || $_POST['new']['russian'] == '') {
        error('Укажите отображаемые названия папки на русском и английском языках');
    }

    // берем корень
    if ($id) {
        $d = mysql_fetch_assoc(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));
    } else {
        $d['path'] = $setup['path'] . '/';
    }

    chmod($d['path'], 0777);
    /////////

    $directory = $d['path'] . $_POST['realname'] . '/';
    //print $directory;

    $temp = iconv_substr($directory, iconv_strlen($setup['path']), iconv_strlen($directory));

    //скриншоты
    $screen = $setup['spath'] . '/' . $temp;
    // описания
    $desc = $setup['opath'] . '/' . $temp;
    // вложения
    $attach = $setup['apath'] . '/' . $temp;

    $dirnew = array();
    $dirnew['english'] = mysql_real_escape_string($_POST['new']['english'], $mysql);
    $dirnew['russian'] = mysql_real_escape_string($_POST['new']['russian'], $mysql);


    mkdir($directory, 0777);
    chmod($directory, 0777); // fix
    
    // скриншоты
    mkdir($screen, 0777);
    chmod($screen, 0777); // fix
    
    // описания
    mkdir($desc, 0777);
    chmod($desc, 0777); // fix

    // вложения
    mkdir($attach, 0777);
    chmod($attach, 0777); // fix


    // заносим в бд
    // пока поддержка только английского и русского языков
    if (mysql_query("INSERT INTO `files` (`dir`, `dir_count`, `path`, `name`, `rus_name`, `infolder`, `timeupload`) VALUES ('1', 0, '" . mysql_real_escape_string($directory, $mysql) . "', '" . $dirnew['english'] . "', '" . $dirnew['russian'] . "', '" . mysql_real_escape_string($d['path'], $mysql) . "', " . $_SERVER['REQUEST_TIME'] . ");", $mysql)) {
        dir_count($d['path'], true);
        echo 'Новый каталог создан.';
    } else {
        error('Ошибка при создании нового каталога. - ' . mysql_error($mysql));
    }
} else {
    echo '<div class="mainzag">Создание новой категории:</div>
    <form action="apanel.php?action=newdir&amp;id=' . $id . '" method="post">
    <div class="row">
    Имя новой папки [A-Z0-9_-]:<br/>
    <input type="text" name="realname" size="70" class="enter" /><br/>';
    language_dir('', '');
    echo '<input class="buttom" type="submit" value="Добавить"/>
    </div>
    </form>';
}
break;


#########################################ИЗМЕНЕНИЕ МОДУЛЕЙ###############################################
case 'modules':
if (!$_POST) {
echo '<div class="mainzag">Управления модулями:</div>
<form action="apanel.php?action=modules" method="post">
<div class="row">
<input name="komments_change" type="checkbox" value="1" ' . check($setup['komments_change']) . '/>Комментарии<br/>
<input name="komments_captcha" type="checkbox" value="1" ' . check($setup['komments_captcha']) . '/>Капча к комментариям<br/>
<input name="eval_change" type="checkbox" value="1" ' . check($setup['eval_change']) . '/>Рейтинг<br/>
<input name="jad_change" type="checkbox" value="1" ' . check($setup['jad_change']) . '/>Генератор Jad<br/>
<input name="cut_change" type="checkbox" value="1" ' . check($setup['cut_change']) . '/>Нарезчик MP3<br/>
<input name="zip_change" type="checkbox" value="1" ' . check($setup['zip_change']) . '/>Просмотр архивов<br/>
<input name="zakaz_change" type="checkbox" value="1" ' . check($setup['zakaz_change']) . '/>Стол заказов<br/>
<input name="buy_change" type="checkbox" value="1" ' . check($setup['buy_change']) . '/>Рекламный блок<br/>
<input name="onpage_change" type="checkbox" value="1" ' . check($setup['onpage_change']) . '/>Меню выбора кол-ва файлов на страницу<br/>
<input name="preview_change" type="checkbox" value="1" ' . check($setup['preview_change']) . '/>Меню выбора отображения предпросмотра<br/>
<input name="top_change" type="checkbox" value="1" ' . check($setup['top_change']) . '/>ТОП<br/>
<input name="stat_change" type="checkbox" value="1" ' . check($setup['stat_change']) . '/>Статистика<br/>
<input name="pagehand_change" type="checkbox" value="1" ' . check($setup['pagehand_change']) . '/>Ручной ввод страниц<br/>
<input name="search_change" type="checkbox" value="1" ' . check($setup['search_change']) . '/>Поиск файлов<br/>
<input name="lib_change" type="checkbox" value="1" ' . check($setup['lib_change']) . '/>Библиотека<br/>

<input name="screen_change" type="checkbox" value="1" ' . check($setup['screen_change']) . '/>Уменьшенные скриншоты в общем просмотре<br/>
<input name="screen_file_change" type="checkbox" value="1" ' . check($setup['screen_file_change']) . '/>Уменьшенные скриншоты в просмотре файла<br/>
<input name="swf_change" type="checkbox" value="1" ' . check($setup['swf_change']) . '/>SWF превью в общем просмотре<br/>
<input name="swf_file_change" type="checkbox" value="1" ' . check($setup['swf_file_change']) . '/>SWF превью в просмотре файла<br/>
<input name="jar_change" type="checkbox" value="1" ' . check($setup['jar_change']) . '/>Иконки JAR файлов в общем просмотре<br/>
<input name="jar_file_change" type="checkbox" value="1" ' . check($setup['jar_file_change']) . '/>Иконки JAR файлов в просмотре файла<br/>

<input name="anim_change" type="checkbox" value="1" ' . check($setup['anim_change']) . '/>Поддержка анимации<br/>
<input name="prew" type="checkbox" value="1" ' . check($setup['prew']) . '/>Предпросмотр по умолчанию<br/>
<input name="lib_desc" type="checkbox" value="1" ' . check($setup['lib_desc']) . '/>Брать первую строку из txt файла как описание<br/>
<input name="ext" type="checkbox" value="1" ' . check($setup['ext']) . '/>Показ расширения<br/>
<input name="prev_next" type="checkbox" value="1" ' . check($setup['prev_next']) . '/>Предыдущий/следующий файлы<br/>
<input name="style_change" type="checkbox" value="1" ' . check($setup['style_change']) . '/>Смена стилей<br/>
<input name="service_change" type="checkbox" value="1" ' . check($setup['service_change']) . '/>Сервисное использование<br/>
<input name="service_change_advanced" type="checkbox" value="1" ' . check($setup['service_change_advanced']) . '/>Расширенное сервисное использование<br/>
<input name="abuse_change" type="checkbox" value="1" ' . check($setup['abuse_change']) . '/>Жалобы<br/>
<input name="exchanger_change" type="checkbox" value="1" ' . check($setup['exchanger_change']) . '/>Обменник<br/>
<input name="send_email" type="checkbox" value="1" ' . check($setup['send_email']) . '/>Отправка ссылки на Email<br/>
<br/>
<input class="buttom" type="submit" value="Сохранить"/>
</div>
</form>';
} else {
    $_POST['komments_change'] = $_POST['komments_change'] ? 1 : 0;
    $_POST['komments_captcha'] = $_POST['komments_captcha'] ? 1 : 0;
    $_POST['eval_change'] = $_POST['eval_change'] ? 1 : 0;
    $_POST['onpage_change'] = $_POST['onpage_change'] ? 1 : 0;
    $_POST['preview_change'] = $_POST['preview_change'] ? 1 : 0;
    $_POST['top_change'] = $_POST['top_change'] ? 1 : 0;
    $_POST['stat_change'] = $_POST['stat_change'] ? 1 : 0;
    $_POST['search_change'] = $_POST['search_change'] ? 1 : 0;
    $_POST['pagehand_change'] = $_POST['pagehand_change'] ? 1 : 0;
    $_POST['zip_change'] = $_POST['zip_change'] ? 1 : 0;
    $_POST['jad_change'] = $_POST['jad_change'] ? 1 : 0;
    $_POST['zakaz_change'] = $_POST['zakaz_change'] ? 1 : 0;
    $_POST['buy_change'] = $_POST['buy_change'] ? 1 : 0;
    $_POST['cut_change'] = $_POST['cut_change'] ? 1 : 0;
    $_POST['lib_change'] = $_POST['lib_change'] ? 1 : 0;

    $_POST['screen_change'] = $_POST['screen_change'] ? 1 : 0;
    $_POST['screen_file_change'] = $_POST['screen_file_change'] ? 1 : 0;
    $_POST['swf_change'] = $_POST['swf_change'] ? 1 : 0;
    $_POST['swf_file_change'] = $_POST['swf_file_change'] ? 1 : 0;
    $_POST['jar_change'] = $_POST['jar_change'] ? 1 : 0;
    $_POST['jar_file_change'] = $_POST['jar_file_change'] ? 1 : 0;

    $_POST['anim_change'] = $_POST['anim_change'] ? 1 : 0;
    $_POST['prew'] = $_POST['prew'] ? 1 : 0;
    $_POST['lib_desc'] = $_POST['lib_desc'] ? 1 : 0;
    $_POST['ext'] = $_POST['ext'] ? 1 : 0;
    $_POST['prev_next'] = $_POST['prev_next'] ? 1 : 0;
    $_POST['style_change'] = $_POST['style_change'] ? 1 : 0;
    $_POST['service_change'] = $_POST['service_change'] ? 1 : 0;
    $_POST['service_change_advanced'] = $_POST['service_change_advanced'] ? 1 : 0;
    $_POST['abuse_change'] = $_POST['abuse_change'] ? 1 : 0;
    $_POST['exchanger_change'] = $_POST['exchanger_change'] ? 1 : 0;
    $_POST['send_email'] = $_POST['send_email'] ? 1 : 0;


    foreach ($_POST as $key => $value) {
    	if ($key == 'password' || $key == 'delete_dir' || $key == 'delete_file') {
    		error($setup['hackmess']);
    	}
    	mysql_query("REPLACE INTO `setting`(`name`, `value`) VALUES('" . mysql_real_escape_string($key, $mysql) . "', '" . intval($value) . "');", $mysql);
    }
    echo 'Список модулей изменен';
}
break;


########################################БЕЗОПАСНОСТЬ################################################
case 'sec':
if (!$_POST)
{
echo '<div class="mainzag">Безопасность:</div>
<form action="apanel.php?action=sec" method="post">
<div class="row">
Пароль(если не хотим менять оставляем пустым): <br/>
<input class="enter" name="password" type="password" value=""/>
</div><div class="row">
Число неверных попыток ввода пароля до блокировки: <br/>
<input class="enter" name="countban" type="text" value="' . $setup['countban'] . '"/>
</div><div class="row">
Время блокировки(сек.): <br/>
<input class="enter" name="timeban" type="text" value="' . $setup['timeban'] . '"/><br/>
<input name="autologin" type="checkbox" value="ON" ' . check($setup['autologin']) . '/>Автологин<br/>
<input name="delete_file" type="checkbox" value="ON" ' . check($setup['delete_file']) . '/>Функция удаления файлов<br/>
<input name="delete_dir" type="checkbox" value="ON" ' . check($setup['delete_dir']) . '/>Функция удаления каталогов
</div><div class="row">
Введите текущий пароль для подтверждения: <br/>
<input class="enter" name="pwd" type="password" value=""/><br/>
<input class="buttom" type="submit" value="Сохранить"/>
</div>
</form>';
}
else
{
    $_POST['autologin'] = $_POST['autologin'] ? 1 : 0;
    $_POST['delete_dir'] = $_POST['delete_dir'] ? 1 : 0;
    $_POST['delete_file'] = $_POST['delete_file'] ? 1 : 0;
    is_num($_POST['countban'], 'countban');
    is_num($_POST['timeban'], 'timeban');

    if (!$_POST['pwd'] || md5($_POST['pwd']) != $setup['password']) {
        error($setup['hackmess']);
    }

    foreach ($_POST as $key => $value) {
        if ($value == '' && $key != 'password' && $key != 'autologin' && $key != 'delete_dir' && $key != 'delete_file') {
    	    error('Не заполнено одно из полей.');
        }
    }
    if ($_POST['password'] != '') {
        $_SESSION['autorise'] = md5($_POST['password']);
        mysql_query("UPDATE `setting` SET `value` = '" . md5($_POST['password']) . "' WHERE `name` = 'password';", $mysql);
    }
    mysql_query("UPDATE `setting` SET `value` = '" . $_POST['countban'] . "' WHERE `name` = 'countban';", $mysql);
    mysql_query("UPDATE `setting` SET `value` = '" . $_POST['timeban'] . "' WHERE `name` = 'timeban';", $mysql);
    mysql_query("UPDATE `setting` SET `value` = '" . $_POST['autologin'] . "' WHERE `name` = 'autologin';", $mysql);
    mysql_query("UPDATE `setting` SET `value` = '" . $_POST['delete_file'] . "' WHERE `name` = 'delete_file';", $mysql);
    mysql_query("UPDATE `setting` SET `value` = '" . $_POST['delete_dir'] . "' WHERE `name` = 'delete_dir';", $mysql);
    echo 'Настройки изменены.';
}
break;


########################################НАСТРОЙКИ СКРИПТА################################################
case 'setting':
if (!$_POST)
{
echo '<div class="mainzag">Настройки загруз-центра:</div>
<form action="apanel.php?action=setting" method="post">
<div class="row">
Папка с файлами:<br/>
<input class="enter" name="path" type="text" value="' . $setup['path'] . '"/>
</div><div class="row">
Папка с описаниями:<br/>
<input class="enter" name="opath" type="text" value="' . $setup['opath'] . '"/>
</div><div class="row">
Папка с вложениями:<br/>
<input class="enter" name="apath" type="text" value="' . $setup['apath'] . '"/>
</div><div class="row">
Папка со скринами:<br/>
<input class="enter" name="spath" type="text" value="' . $setup['spath'] . '"/>
</div><div class="row">

Папка c JAVA книгами:<br/>
<input class="enter" name="jpath" type="text" value="' . $setup['jpath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['jpath'] . '">Очистить</a><br/>
</div><div class="row">

Папка c иконками из JAR файлов:<br/>
<input class="enter" name="ipath" type="text" value="' . $setup['ipath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['ipath'] . '">Очистить</a><br/>
</div><div class="row">

Папка c картинками из ZIP архивов:<br/>
<input class="enter" name="zppath" type="text" value="' . $setup['zppath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['zppath'] . '">Очистить</a><br/>
</div><div class="row">

Папка c ZIP книгами:<br/>
<input class="enter" name="zpath" type="text" value="' . $setup['zpath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['zpath'] . '">Очистить</a><br/>
</div><div class="row">

Папка co скриншотами тем:<br/>
<input class="enter" name="tpath" type="text" value="' . $setup['tpath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['tpath'] . '">Очистить</a><br/>
</div><div class="row">

Папка co скриншотами видео:<br/>
<input class="enter" name="ffmpegpath" type="text" value="' . $setup['ffmpegpath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['ffmpegpath'] . '">Очистить</a><br/>
</div><div class="row">

Папка c превьюшками картинок:<br/>
<input class="enter" name="picpath" type="text" value="' . $setup['picpath'] . '"/> <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['picpath'] . '">Очистить</a><br/>
</div><div class="row">

Папка для нарезок:<br/>
<input class="enter" name="mp3path" type="text" value="' . $setup['mp3path'] . '"/>  <a href="' . $_SERVER['PHP_SELF'] . '?action=clean_cache&amp;dir=' . $setup['mp3path'] . '">Очистить</a><br/>
</div><div class="row">
Лимит нарезок (Мб):<br/>
<input class="enter" name="limit" type="text" value="' . $setup['limit'] . '"/>
</div><div class="row">

Лимит комментариев к одному файлу:<br/>
<input class="enter" name="klimit" type="text" value="' . $setup['klimit'] . '"/>
</div><div class="row">
Кол-во комментариев в описании файла:<br/>
<input class="enter" name="komments_view" type="text" value="' . $setup['komments_view'] . '"/>
</div><div class="row">

Файлов на страницу по умолчанию:
<select class="enter" size="1" name="onpage">
<option ' . sel(5, $setup['onpage']) . '>5</option>
<option ' . sel(10, $setup['onpage']) . '>10</option>
<option ' . sel(15, $setup['onpage']) . '>15</option>
<option ' . sel(20, $setup['onpage']) . '>20</option>
<option ' . sel(25, $setup['onpage']) . '>25</option>
<option ' . sel(30, $setup['onpage']) . '>30</option>
</select>
</div><div class="row">

Стиль по умолчанию:
<select class="enter" size="1" name="css">';
foreach (glob('*.css', GLOB_NOESCAPE) as $v) {
    $value = pathinfo($v, PATHINFO_FILENAME);
    echo '<option value="' . htmlspecialchars($value) . '" ' . sel($value, $setup['css'])  . '>' . htmlspecialchars($value, ENT_NOQUOTES) . '</option>';
}
echo '</select>
</div><div class="row">

Язык по умолчанию:
<select class="enter" size="1" name="langpack">';
foreach (glob('moduls/language/*.dat') as $v) {
    $value = pathinfo($v, PATHINFO_FILENAME);
    echo '<option value="' . htmlspecialchars($value) . '" ' . sel($value, $setup['langpack']) . '>' . htmlspecialchars($value, ENT_NOQUOTES) . '</option>';
}
echo '</select>
</div><div class="row">

Размер превьюшек (например, 40*40):<br/>
<input class="enter" name="prev_size" type="text" value="' . $setup['prev_size'] . '"/>
</div><div class="row">

<div class="row">
Размеры картинок (например, 128*128,120*160,132*176,240*320):<br/>
<input class="enter" name="view_size" type="text" value="' . $setup['view_size'] . '"/>
</div><div class="row">

Число отображаемых символов описания в общем просмотре файлов:<br/>
<input class="enter" name="desc" type="text" value="' . $setup['desc'] . '"/>
</div><div class="row">

Номер фрейма для превью видео:<br/>
<input class="enter" name="ffmpeg_frame" type="text" value="' . $setup['ffmpeg_frame'] . '"/>
</div><div class="row">

Номера фреймов для превью видео в просмотре файла (например, 25,120,250):<br/>
<input class="enter" name="ffmpeg_frames" type="text" value="' . $setup['ffmpeg_frames'] . '"/>
</div><div class="row">

Время новых файлов (дней, 0 - выключено):<br/>
<input class="enter" name="day_new" type="text" value="' . $setup['day_new'] . '"/>
</div><div class="row">
Время онлайна (сек.):<br/>
<input class="enter" name="online_time" type="text" value="' . $setup['online_time'] . '"/>
</div><div class="row">
Число страниц после которого появляется возможность ручного ввода страниц: <br/>
<input class="enter" name="pagehand" type="text" value="' . $setup['pagehand'] . '"/>
</div><div class="row">
Число ТОП файлов:<br/>
<input class="enter" name="top_num" type="text" value="' . $setup['top_num'] . '"/>
</div><div class="row">
Сообщение для особо одаренных:<br/>
<input class="enter" name="hackmess" type="text" value="' . $setup['hackmess'] . '"/>
</div><div class="row">
Сортировка по умолчанию:
<select class="enter" size="1" name="sort">
<option value="name" ' . sel('name', $setup['sort']) . '>Имя</option>
<option value="size" ' . sel('size', $setup['sort']) . '>Размер</option>
<option value="data" ' . sel('data', $setup['sort']) . '>Дата</option>
<option value="load" ' . sel('load', $setup['sort']) . '>Популярность</option>
<option value="eval" ' . sel('eval', $setup['sort']) . '>Рейтинг</option>
</select></div>
<div class="row">
Заголовок:<br/>
<input class="enter" name="zag" type="text" value="' . $setup['zag'] . '"/>
</div><div class="row">
Главная сайта (c http://):<br/>
<input class="enter" name="site_url" type="text" value="' . $setup['site_url'] . '"/><br/>
E-mail админа:<br/>
<input class="enter" name="zakaz_email" type="text" value="' . $setup['zakaz_email'] . '"/><br/>
<br/>
<input class="buttom" type="submit" value="Сохранить"/>
</div>
</form>';
}
else
{
    if ($_POST['password'] || $_POST['delete_dir'] || $_POST['delete_file']) {
    	error($setup['hackmess']);
    }
    foreach ($_POST as $key => $value) {
    	if ($value == '') {
    		error('Не заполнено одно из полей.');
    	}
    	mysql_query("REPLACE INTO `setting`(`name`,`value`) VALUES('" . mysql_real_escape_string($key, $mysql) . "', '" . mysql_real_escape_string($value, $mysql) . "');", $mysql);
    	//print mysql_error($mysql);
    }
    echo 'Настройки сохранены';
}
break;


case 'del_news_komm':
if (mysql_query('DELETE FROM `news_komments` WHERE `id` = ' . intval($_GET['news_komm']), $mysql)) {
	echo 'Ok<br/>';
} else {
	error('Ошибка: ' . mysql_error($mysql));
}
break;


case 'del_komm':
if (mysql_query('DELETE FROM `komments` WHERE `id` = ' . intval($_GET['komm']), $mysql)) {
	echo 'Ok<br/>';
} else {
	error('Ошибка: ' . mysql_error($mysql));
}
break;
}


######################################НОСОК##################################################
if ($action) {
    echo '<div class="row"><a href="apanel.php">Админка</a></div>';
}


require 'moduls/foot.php';

?>
