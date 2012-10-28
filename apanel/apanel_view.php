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


define('APANEL', true);
chdir('../');
require 'moduls/config.php';
require 'moduls/header.php';


$HeadTime = microtime(true);


//=================================================================================================================
if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error($setup['hackmess']);
}
//=================================================================================================================

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;

$onpage = get2ses('onpage');
$prew = get2ses('prew');
$sort = get2ses('sort');

if ($onpage < 3) {
    $onpage = 3;
}

if ($prew != 0 && $prew != 1) {
    $prew = $setup['preview'];
}

//------------------------------------------------------------------------------------------
$file_info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
if (!is_file($file_info['path'])) {
    error('File not found!');
}


if (isset($_GET['del_attach']) && $file_info['attach']) {
    $attach = unserialize($file_info['attach']);
    $name = '';
    if (isset($attach[$_GET['del_attach']])) {
        $name = $attach[$_GET['del_attach']];
        unset($attach[$_GET['del_attach']]);
    }

    if (!$attach) {
        if (mysql_query('UPDATE `files` SET `attach` = NULL WHERE `id` = ' . $id, $mysql)) {
            del_attach($file_info['infolder'], $id, array($_GET['del_attach'] => $name));
            $file_info['attach'] = null;
        }
    } else {
        if (mysql_query('UPDATE `files` SET `attach` = "' . mysql_real_escape_string(serialize($attach)) . '" WHERE `id` = ' . $id, $mysql)) {
            del_attach($file_info['infolder'], $id, array($_GET['del_attach'] => $name));
            $file_info['attach'] = serialize($attach);
        }
    }
} else if ($_FILES && isset($_FILES['attach']) && !$_FILES['attach']['error'] && checkExt(pathinfo($_FILES['attach']['name'], PATHINFO_EXTENSION))) {
    if ($file_info['attach']) {
        $attach = unserialize($file_info['attach']);
        $key = sizeof($attach);
    } else {
        $attach = array();
        $key = 0;
    }

    if (add_attach($file_info['infolder'], $id, array($key => $_FILES['attach']))) {
        $attach[$key] = $_FILES['attach']['name'];
        if (mysql_query('UPDATE `files` SET `attach` = "' . mysql_real_escape_string(serialize($attach)) . '" WHERE `id` = ' . $id, $mysql)) {
            $file_info['attach'] = serialize($attach);
        }
    }
}


//------------------------------------------------------------------------------------------
$filename = pathinfo($file_info['path']);
$ext = strtolower($filename['extension']);
$dir = $filename['dirname'];
$filename = $filename['basename'];
$back = mysql_fetch_array(mysql_query("SELECT `id` FROM `files` WHERE `path` = '" . mysql_real_escape_string($dir, $mysql) . "'", $mysql));
//------------------------------------------------------------------------------------------


if (isset($_GET['hidden'])) {
    if ($_GET['hidden'] == 1 && $file_info['hidden'] == 0) {
        if (mysql_query('UPDATE `files` SET `hidden` = "1" WHERE `id` = ' . $id)) {
            $file_info['hidden'] = 1;
            dir_count($file_info['infolder'], false);
        }
    } else if ($_GET['hidden'] == 0 && $file_info['hidden'] == 1) {
        if (mysql_query('UPDATE `files` SET `hidden` = "0" WHERE `id` = ' . $id)) {
            $file_info['hidden'] = 0;
            dir_count($file_info['infolder'], true);
        }
    }
} else if (isset($_POST['folder'])) {
    $folder = mysql_fetch_assoc(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . intval($_POST['folder']), $mysql));

    if (!$folder) {
        echo '<div class="red">Указанной директории не существует. Файл не перемещен<br/></div>';
    } else {
        if (file_exists($folder['path'] . $filename)) {
            echo '<div class="red">Файл с таким именем в указанной директории уже есть. Файл не перемещен<br/></div>';
        } else {
            if (rename($file_info['path'], $folder['path'] . $filename)) {
                if (mysql_query('UPDATE `files` SET `path` = "' . mysql_real_escape_string($folder['path'] . $filename, $mysql) . '", `infolder` = "' . mysql_real_escape_string($folder['path'], $mysql) . '" WHERE `id` = ' . $id, $mysql)) {

                    if (!$file_info['hidden']) {
                        dir_count($dir, false);
                        dir_count($folder['path'], true);
                    }

                    $path1 = strstr($file_info['path'], '/'); // убираем папку с загрузками
                    $path2 = strstr($folder['path'], '/'); // убираем папку с загрузками

                    // перемещаем скриншоты и описания
                    if (is_file($setup['spath'] . $path1 . '.gif')) {
                        rename($setup['spath'] . $path1 . '.gif', $setup['spath'] . $path2 . $filename . '.gif');
                    }
                    if (is_file($setup['spath'] . $path1 . '.jpg')) {
                        rename($setup['spath'] . $path1 . '.jpg', $setup['spath'] . $path2 . $filename . '.jpg');
                    }
                    if (is_file($setup['opath'] . $path1 . '.txt')) {
                        rename($setup['opath'] . $path1 . '.txt', $setup['opath'] . $path2 . $filename . '.txt');
                    }
                    echo '<div class="green">Файл перемещен<br/></div>';
                } else {
                    rename($folder['path'] . $filename, $file_info['path']);
                    echo '<div class="red">Ошибка записи в БД<br/>' . mysql_error($mysql) . '</div>';
                }
            } else {
                echo '<div class="red">Ошибка переименования файла<br/></div>';
            }
        }
    }
    exit;
}



$all_komments = mysql_result(mysql_query('SELECT COUNT(1) FROM `komments` WHERE file_id = ' . $id, $mysql), 0);

$file_info['size'] = '(' . size($file_info['size']) . ')';

###############Вывод###################
echo '<div class="mblock">Досье на файл ' . htmlspecialchars($filename, ENT_NOQUOTES) . '</div>
<div class="row">
<strong>Размер:</strong> ' . $file_info['size'] . '<br/>
<strong>Скачано:</strong> ' . $file_info['loads'] . ' раз(а)<br/>';

###############Недавнее скачивание###################
if ($file_info['timeload']) {
    $file_info['timeload'] = tm($file_info['timeload']);
    echo '<strong>Недавнее скачивание:</strong><br/>' . $file_info['timeload'] . '<br/>';
}

$file_info['timeupload'] = tm($file_info['timeupload']);
###############Время добавления######################
echo '<strong>Время добавления:</strong><br/>' . $file_info['timeupload'];
###############Особый размер для картинок############
$prev_pic = str_replace('/', '--', mb_substr(strstr($file_info['path'], '/'), 1));

if ($ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'jpe' || $ext == 'png' || $ext == 'bmp') {
    $out = '<hr class="hr"/>';
    if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
        $out .= '<img src="../' . $setup['picpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/><br/>';
    } else {
        $out .= '<img src="../im/' . $id . '" alt=""/><br/>';
    }

    $size = getimagesize($file_info['path']);

    $out .= $size[0] . 'x' . $size[1] . '<br/><strong>Размер:</strong>';
    foreach (explode(',', $setup['view_size']) as $val) {
        $wh = explode('*', $val);
        $f = $setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . htmlspecialchars($prev_pic) . '.gif';
        if (file_exists($f)) {
            $out .= ' <a href="../' . $f . '">' . $val . '</a>';
        } else {
            $out .= ' <a href="../im.php?id=' . $id . '&amp;W=' . $wh[0] . '&amp;H=' . $wh[1] . '">' . $val . '</a>';
        }
    }
    echo $out . '<form action="../im.php?" method="post"><div class="row"><input type="hidden" name="id" value="' . $id . '"/><input type="text" size="3" name="W"/>x<input type="text" size="3" name="H"/><br/><input type="submit" value="Скачать"/></div></form>';
}

###############Инфа о mp3###########################
else if ($ext == 'mp3' || $ext == 'wav' || $ext == 'ogg') {
    $tmpa = array();

    if ($ext == 'mp3' || $ext == 'wav') {
        if (file_exists('moduls/cache/' . $id . '.dat')) {
            $tmpa = unserialize(file_get_contents('moduls/cache/' . $id . '.dat'));
        } else {
            include 'moduls/inc/classAudioFile.php';

            $audio = new AudioFile;
            $audio->loadFile($file_info['path']);

            if ($audio->wave_length) {
                $length = $audio->wave_length;
            } else {
                include 'moduls/inc/mp3.class.php';
                $mp3 = new mp3($file_info['path']);
                $mp3->setFileInfoExact();
                $length = $mp3->time;
            }
            $comments = array();

            if (isset($audio->id3_title)) {
                $comments['TITLE'] = str_to_utf8($audio->id3_title);
               } else {
                   $comments['TITLE'] = '';
              }
              if (isset($audio->id3_artist)) {
                $comments['ARTIST'] = str_to_utf8($audio->id3_artist);
               } else {
                   $comments['ARTIST'] = '';
              }
              if (isset($audio->id3_album)) {
                $comments['ALBUM'] = str_to_utf8($audio->id3_album);
               } else {
                   $comments['ALBUM'] = '';
              }
              if (isset($audio->id3_year)) {
                $comments['DATE'] = str_to_utf8($audio->id3_year);
               } else {
                   $comments['DATE'] = '';
              }
              if (isset($audio->id3_genre)) {
                $comments['GENRE'] = str_to_utf8($audio->id3_genre);
               } else {
                   $comments['GENRE'] = '';
              }
              if (isset($audio->id3_comment)) {
                $comments['COMMENT'] = str_to_utf8($audio->id3_comment);
               } else {
                   $comments['COMMENT'] = '';
              }

            $tmpa = array(
                'channels' => $audio->wave_channels,
                'sampleRate' => $audio->wave_framerate,
                'avgBitrate' => str_replace(' Kbps', '', $audio->wave_byterate) * 1024,
                'streamLength' => $length,
                'comments' => array(
                    'TITLE' => trim(str_replace(array(chr(0), chr(1)), '', $comments['TITLE'])),
                    'ARTIST' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ARTIST'])),
                    'ALBUM' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ALBUM'])),
                    'DATE' => $comments['DATE'],
                    'GENRE' => $comments['GENRE'],
                    'COMMENT' => trim(str_replace(array(chr(0), chr(1)), '', $comments['COMMENT']))
                )
            );
        }
    } else if ($ext == 'ogg') {
        if (file_exists('moduls/cache/' . $id . '.dat')) {
            $tmpa = unserialize(file_get_contents('moduls/cache/' . $id . '.dat'));
        } else {
            include 'moduls/PEAR/File/Ogg.php';
            try{
                $ogg = new File_Ogg($file_info['path']);
                $obj = & current($ogg->_streams);
                $comments = array();

                if (isset($obj->_comments['TITLE'])) {
                    $comments['TITLE'] = str_to_utf8($obj->_comments['TITLE']);
                   } else {
                       $comments['TITLE'] = '';
                  }
                  if (isset($obj->_comments['ARTIST'])) {
                    $comments['ARTIST'] = str_to_utf8($obj->_comments['ARTIST']);
                   } else {
                       $comments['ARTIST'] = '';
                  }
                  if (isset($obj->_comments['ALBUM'])) {
                    $comments['ALBUM'] = str_to_utf8($obj->_comments['ALBUM']);
                   } else {
                       $comments['ALBUM'] = '';
                  }
                  if (isset($obj->_comments['DATE'])) {
                    $comments['DATE'] = str_to_utf8($obj->_comments['DATE']);
                   } else {
                       $comments['DATE'] = '';
                  }
                  if (isset($obj->_comments['GENRE'])) {
                    $comments['GENRE'] = str_to_utf8($obj->_comments['GENRE']);
                   } else {
                       $comments['GENRE'] = '';
                  }
                  if (isset($obj->_comments['COMMENT'])) {
                    $comments['COMMENT'] = str_to_utf8($obj->_comments['COMMENT']);
                   } else {
                       $comments['COMMENT'] = '';
                  }

                $tmpa = array(
                    'channels' => $obj->_channels,
                    'sampleRate' => $obj->_sampleRate,
                    'avgBitrate' => $obj->_avgBitrate,
                    'streamLength' => $obj->_streamLength,
                    'comments' => array(
                        'TITLE' => trim(str_replace(array(chr(0), chr(1)), '', $comments['TITLE'])),
                        'ARTIST' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ARTIST'])),
                        'ALBUM' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ALBUM'])),
                        'DATE' => $comments['DATE'],
                        'GENRE' => $comments['GENRE'],
                        'COMMENT' => trim(str_replace(array(chr(0), chr(1)), '', $comments['COMMENT']))
                    )
                );
            } catch(Exception $e){
                //
            }
        }
    }

    file_put_contents('moduls/cache/' . $id . '.dat', serialize($tmpa));
    $out = '<hr class="hr"/><strong>Информация:</strong><br/>Каналов: ' . $tmpa['channels'] . '<br/>Частота: ' . $tmpa['sampleRate'] . ' Hz<br/>Битрейт: ' . round($tmpa['avgBitrate'] / 1024) . ' Kbps<br/>Длина: ' . date('H:i:s', mktime(0, 0, $tmpa['streamLength'])) . '<br/>';

    if ($tmpa['comments']['TITLE']) {
        $out .= 'Название: ' . htmlspecialchars($tmpa['comments']['TITLE'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['ARTIST']) {
        $out .= 'Исполнитель: ' . htmlspecialchars($tmpa['comments']['ARTIST'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['ALBUM']) {
        $out .= 'Альбом: ' . htmlspecialchars($tmpa['comments']['ALBUM'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['DATE']) {
        $out .= 'Год: ' . htmlspecialchars($tmpa['comments']['DATE'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['GENRE']) {
        $out .= 'Жанр: ' . htmlspecialchars($tmpa['comments']['GENRE'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['COMMENT']) {
        $out .= 'Комментарии: ' . htmlspecialchars($tmpa['comments']['COMMENT'], ENT_NOQUOTES) . '<br/>';
    }

    echo $out;
}

// Видео (ffmpeg)
else if (($ext == '3gp' || $ext == 'avi' || $ext == 'mp4' || $ext == 'flv') && extension_loaded('ffmpeg')) {
    if ($_GET['frame'] < 1) {
        $frame = 5;
    } else {
        $frame = $_GET['frame'];
    }
    // 80x80
    if (is_file($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif')) {
        $out = '<br/><img src="../' . $setup['ffmpegpath'] . '/' . htmlspecialchars($prev_pic) . '_frame_' . $frame . '.gif" alt=""/><br/>';
    } else {
        $out = '<br/><img src="../ffmpeg/' . $id . '/' . $frame . '" alt=""/><br/>';
    }

    if (file_exists('moduls/cache/' . $id . '.dat')) {
        $tmpa = unserialize(file_get_contents('moduls/cache/' . $id . '.dat'));
    } else {
        $mov = new ffmpeg_movie($file_info['path'], false);
        $tmpa = array(
            'getVideoCodec' => $mov->getVideoCodec(),
            'GetFrameWidth' => $mov->GetFrameWidth(),
            'GetFrameHeight' => $mov->GetFrameHeight(),
            'getDuration' => $mov->getDuration(),
            'getBitRate' => $mov->getBitRate()
        );
        file_put_contents('moduls/cache/' . $id . '.dat', serialize($tmpa));
    }

    $i = 0;
    foreach (explode(',', $setup['ffmpeg_frames']) as $fr) {
        $out .= '<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&amp;frame=' . $fr . '">[' . (++$i) . ']</a>, ';
    }
    $out = rtrim($out, ', ') . '<hr class="hr"/>Кодек: ' . htmlspecialchars($tmpa['getVideoCodec'], ENT_NOQUOTES) . '<br/>Разрешение: ' . intval($tmpa['GetFrameWidth']) . ' x ' . intval($tmpa['GetFrameHeight']) . '<br/>Длина: ' . date('H:i:s', mktime(0, 0, round($tmpa['getDuration']))) . '<br/>';


    if ($tmpa['getBitRate']) {
        $out .= 'Битрейт: ' . ceil($tmpa['getBitRate'] / 1024) . ' Kbps<br/>';
    }
    echo $out;
    
} else if ($ext == 'swf') {
    echo '<br/><object width="128" height="128"><param name="movie" value="../' . htmlspecialchars($file_info['path']) . '"><embed src="../' . htmlspecialchars($file_info['path']) . '" width="128" height="128"></embed></param></object>';
} else if ($ext == 'jar') {
    if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
        echo '<br/><img style="margin: 1px;" src="../' . $setup['ipath'] . '/' . htmlspecialchars($prev_pic) . '.png" alt=""/>';
    } else if (jar_ico($file_info['path'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
        echo '<br/><img style="margin: 1px;" src="../' . $setup['ipath'] . '/' . htmlspecialchars($prev_pic) . '.png" alt=""/>';
    }
}

$screen = strstr($file_info['path'], '/'); // убираем папку с загрузками
//Скиншот
if (is_file($setup['spath'] . $screen . '.gif')) {
    echo '<hr class="hr"/><strong>Скриншот:</strong><br/><img src="../' . $setup['spath'] . htmlspecialchars($screen) . '.gif" alt="screen"/><br/>[<strong><a href="apanel.php?action=del_screen&amp;id=' . $id . '">Удалить скриншот</a></strong>]';
} else if (is_file($setup['spath'] . $screen . '.jpg')) {
    echo '<hr class="hr"/><strong>Скриншот:</strong><br/><img src="../' . $setup['spath'] . htmlspecialchars($screen) . '.jpg" alt="screen"/><br/>[<strong><a href="apanel.php?action=del_screen&amp;id=' . $id . '">Удалить скриншот</a></strong>]';
} else {
    echo '<br/>[<strong><a href="apanel.php?action=screen&amp;id=' . $id . '">Добавить скриншот</a></strong>]';
}

//Описание
if (is_file($setup['opath'] . $screen . '.txt')) {
    echo '<hr class="hr"/><strong>Описание:</strong><br/>' . trim(file_get_contents($setup['opath'] . $screen . '.txt'));
} else if ($ext == 'txt' && $setup['lib_desc']) {
    $fp = fopen($file_info['path'], 'r');
    echo '<hr class="hr"/><strong>Описание:</strong><br/>' . trim(fgets($fp, 1024));
    fclose($fp);
}

if ($file_info['hidden']) {
    $tmp = '<a href="apanel_view.php?id=' . $id . '&amp;hidden=0">Видимый</a> / <strong>Не видимый</strong>';
} else {
    $tmp = '<strong>Видимый</strong> / <a href="apanel_view.php?id=' . $id . '&amp;hidden=1">Не видимый</a>';
}

echo '<br/>[<strong><a href="apanel.php?action=about&amp;id=' . $id . '">Добавить/изменить описание</a></strong>]';
echo '<hr class="hr"/><strong>Вложения:</strong><br/>';
if ($file_info['attach']) {
    $attach = unserialize($file_info['attach']);
    if ($attach) {
        foreach ($attach as $k => $val) {
            echo '<a href="../' . htmlspecialchars($setup['apath'] . dirname($screen) . '/' . $id . '_' . $k . '_' . $val) . '">' . htmlspecialchars($val, ENT_NOQUOTES) . '</a> [<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&amp;del_attach=' . $k . '" class="no">D</a>]<br/>';
        }
    }
}

echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '" method="post" enctype="multipart/form-data"><div>
<input name="attach" type="file" class="buttom"/><br/>
<input class="buttom" type="submit" value="Добавить"/>
</div></form>';

echo '<hr style="margin:2px"/>' . $tmp . '
<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '" method="post">
<div>Папка:
<select name="folder" class="buttom">
<option value="' . $setup['path'] . '/">/</option>';
$dirs = mysql_query('SELECT `id`, `path` FROM `files` WHERE `dir` = "1"', $mysql);
while ($item = mysql_fetch_assoc($dirs)) {
    echo '<option ' . ($item['path'] == $file_info['infolder'] ? 'selected="selected" ' : '') . ' value="' . $item['id'] . '">' . htmlspecialchars(substr(strstr($item['path'], '/'), 1), ENT_NOQUOTES) . '</option>';
}
echo '</select><br/>
<input type="submit" value="Переместить" class="buttom"/>
</div>
</form>';


// Голосование
if ($setup['eval_change']) {
    $i = $file_info['yes'] + $file_info['no'];
    $i = $i ? round($file_info['yes'] / $i * 100, 0) : 50;

    echo '<hr class="hr"/>
<strong>Рейтинг файла(+/-)</strong>: <span class="yes">' . $file_info['yes'] . '</span>/<span class="no">' . $file_info['no'] . '</span>[<a href="apanel.php?id=' . $file_info['id'] . '&amp;action=cleareval">Сбросить</a>]<br/>
<img src="../rate.php?i=' . $i . '" alt=""/><br/>';
}


###############Нарезка###########################
echo '</div><div class="iblock">';
if ($setup['cut_change'] && ($ext == 'mp3' || $ext == 'wav')) {
    echo '<strong><a href="../cut.php?id=' . $id . '">Нарезка</a></strong><br/>';
}

###############Просмотр архива####################
if ($setup['zip_change'] && $ext == 'zip') {
    echo '<strong><a href="../zip.php?id=' . $id . '">Просмотр архива</a></strong><br/>';
}

###############Комментарии#######################
if ($setup['komments_change']) {
    echo '<a href="../komm.php?id=' . $id . '"><strong>Комментарии [' . $all_komments . '</strong>]</a>[<a href="apanel.php?id=' . $file_info['id'] . '&amp;action=clearkomm">Очистить</a>]<br/>';
}


// txt файлы
if ($ext == 'txt') {
    if ($setup['lib_change']) {
        echo '<strong><a href="../read.php?id=' . $id . '">Читать</a></strong><br/>';
    }
    echo '<a href="../txt_zip.php?id=' . $id . '">Скачать [ZIP]</a><br/><a href="../txt_jar.php?id=' . $id . '">Скачать [JAR]</a><br/>';
}


echo '<strong><a href="../load.php?id=' . $id . '">Скачать [' . strtoupper($ext) . ']</a></strong><br/>';
if ($ext == 'jar' && $setup['jad_change']) {
    echo '<strong><a href="../jad.php?id=' . $id . '">Скачать [JAD]</a></strong><br/>';
}

echo '</div>
<div class="iblock">
- <a href="apanel_index.php?id=' . $back['id'] . '">Назад</a><br/>
- <a href="apanel.php">Админка</a>
</div>';
