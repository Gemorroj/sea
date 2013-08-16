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
@set_time_limit(99999);
ignore_user_abort(true);
$HeadTime = microtime(true);
chdir('../');

require 'core/header.php';

$template = Http_Response::getInstance()->getTemplate();
$template->setTemplate('apanel/index.tpl');
$db = Db_Mysql::getInstance();


$db->exec('REPLACE INTO `loginlog` SET `time` = UNIX_TIMESTAMP(), `access_num` = 0, `id` = 1');
###################################################
if (!$_SESSION) {
    exit('Не запущена сессия');
}
if (!isset($_SESSION['authorise']) || !isset($_SESSION['ipu'])) {
    exit('В сессии недостаточно данных для авторизации');
}
if ($_SESSION['authorise'] != Config::get('password') || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    exit('Авторизация не пройдена');
}


switch (isset($_GET['action']) ? $_GET['action'] : null) {
    case 'add_attach':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            Http_Response::getInstance()->render();
        }

        if (!$_FILES || !isset($_FILES['attach'])) {
            $template->assign('error', 'Нет загружаемого файла');
            Http_Response::getInstance()->render();
        }
        if ($_FILES['attach']['error']) {
            $template->assign('error', 'Ошибка при загрузке файла. Код ошибки: ' . $_FILES['attach']['error']);
            Http_Response::getInstance()->render();
        }

        $tmp = CORE_DIRECTORY . '/tmp/attach_' . $_FILES['attach']['name'];
        if (move_uploaded_file($_FILES['attach']['tmp_name'], $tmp) === false) {
            $err = error_get_last();
            $template->assign('error', $err['message']);
            Http_Response::getInstance()->render();
        }

        $result = Files::addAttach($file['path'], $id, $tmp, ($file['attach'] ? unserialize($file['attach']) : array()));
        unlink($tmp);

        if ($result['message']) {
            $template->assign('message', implode("\n", $result['message']));
        }
        if ($result['error']) {
            $template->assign('error', implode("\n", $result['error']));
        }
        break;


    case 'del_attach':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            Http_Response::getInstance()->render();
        }

        $attach = unserialize($file['attach']);
        $name = '';
        if (isset($attach[$_GET['attach']])) {
            $name = $attach[$_GET['attach']];
            unset($attach[$_GET['attach']]);
        }

        $q = $db->prepare('UPDATE `files` SET `attach` = ? WHERE `id` = ?');
        if (!$attach) {
            $q->bindValue(1, null, PDO::PARAM_NULL);
            $q->bindValue(2, $id, PDO::PARAM_INT);
        } else {
            $q->bindValue(1, serialize($attach));
            $q->bindValue(2, $id, PDO::PARAM_INT);
        }

        if ($q->execute()) {
            Files::delAttach($file['infolder'], $id, array($_GET['attach'] => $name));
            $template->assign('message', 'Вложение удалено');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'move':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            Http_Response::getInstance()->render();
        }

        $folder = Config::get('path') . trim($_POST['topath']);
        $filename = basename($file['path']);

        if (!is_dir($folder)) {
            $template->assign('error', 'Указанной директории не существует');
            Http_Response::getInstance()->render();
        }
        if (!is_writeable($folder)) {
            $template->assign('error', 'Указанная директория не доступна для записи');
            Http_Response::getInstance()->render();
        }
        if (file_exists($folder . $filename)) {
            $template->assign('error', 'Файл с таким именем в указанной директории уже есть');
            Http_Response::getInstance()->render();
        }
        if (!rename($file['path'], $folder . $filename)) {
            $err = error_get_last();
            $template->assign('error', $err['message']);
            Http_Response::getInstance()->render();
        }

        $q = $db->prepare('UPDATE `files` SET `path` = ?, `infolder` = ? WHERE `id` = ?');
        if ($q->execute(array($folder . $filename, $folder, $id))) {
            Files::updateDirCount($folder, false);
            Files::updateDirCount($file['path'], true);

            $path1 = strstr($file['path'], '/'); // убираем папку с загрузками
            $path2 = strstr($folder, '/'); // убираем папку с загрузками

            // перемещаем скриншоты и описания
            if (is_file(Config::get('spath') . $path1 . '.gif')) {
                rename(Config::get('spath') . $path1 . '.gif', Config::get('spath') . $path2 . $filename . '.gif');
            }
            if (is_file(Config::get('spath') . $path1 . '.jpg')) {
                rename(Config::get('spath') . $path1 . '.jpg', Config::get('spath') . $path2 . $filename . '.jpg');
            }
            if (is_file(Config::get('spath') . $path1 . '.png')) {
                rename(Config::get('spath') . $path1 . '.png', Config::get('spath') . $path2 . $filename . '.png');
            }
            if (is_file(Config::get('opath') . $path1 . '.txt')) {
                rename(Config::get('opath') . $path1 . '.txt', Config::get('opath') . $path2 . $filename . '.txt');
            }
            if (is_file(Config::get('spath') . $path1 . '.thumb.gif')) {
                rename(Config::get('spath') . $path1 . '.thumb.gif', Config::get('spath') . $path2 . $filename . '.thumb.gif');
            }

            // перемещаем вложения
            $globDir1 = Config::get('apath') . dirname($path1) . '/';
            $globDir2 = Config::get('apath') . $path2;
            foreach (glob($globDir1 . $id . '_*') as $v) {
                $v = basename($v);
                rename($globDir1 . $v, $globDir2 . $v);
            }

            $template->assign('message', 'Файл перемещен');
        } else {
            rename($folder . $filename, $file['path']); // переименовываем обратно
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'hidden':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Директория или файл не найдены');
            Http_Response::getInstance()->render();
        }

        $q = $db->prepare('UPDATE `files` SET `hidden` = ? WHERE `id` = ?');
        if ($_GET['hide'] == '1') {
            $result = $q->execute(array('1', $id));
        } else {
            $result = $q->execute(array('0', $id));
        }

        if ($result) {
            $template->assign('message', 'Видимость изменена');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'rename':
        $template->setTemplate('apanel/files/rename.tpl');

        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Директория или файл не найдены');
            Http_Response::getInstance()->render();
        }

        $template->assign('info', $file);

        $langpacks = Language::getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            foreach ($_POST['new'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': укажите название');
                    Http_Response::getInstance()->render();
                }
            }

            $q = $db->prepare('UPDATE `files` SET name = ?, rus_name = ?, aze_name = ?, tur_name = ? WHERE `id` = ?');
            $result = $q->execute(array(
                 $_POST['new']['english'],
                 $_POST['new']['russian'],
                 $_POST['new']['azerbaijan'],
                 $_POST['new']['turkey'],
                 $id
            ));

            if (!$result) {
                $template->assign('error', implode("\n", $q->errorInfo()));
            } else {
                $template->assign('message', 'Название изменено');
            }
        }
        break;


    case 'del_dir':
        if (!Config::get('delete_dir')) {
            $template->assign('error', 'Error');
            Http_Response::getInstance()->render();
        }

        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Директория не найдена');
            Http_Response::getInstance()->render();
        }

        $ex = explode('/', $file['path']);
        $f_chmod = '';
        foreach ($ex as $chmod) {
            $f_chmod .= $chmod . '/';
            chmod($f_chmod, 0777);
        }

        foreach (glob($file['path'] . '*') as $vv) {
            if (is_dir($vv)) {
                $template->assign('error', 'Разрешено удалять только директории с одним уровнем вложенности');
                Http_Response::getInstance()->render();
            } else {
                if (!unlink($vv)) {
                    $err = error_get_last();
                    $template->assign('error', $err['message']);
                    Http_Response::getInstance()->render();
                }
            }
        }

        $q = $db->prepare('DELETE FROM `files` WHERE `infolder` = ?');
        $result = $q->execute(array($file['path']));
        if (!$result) {
            $template->assign('error', implode("\n", $q->errorInfo()));
            Http_Response::getInstance()->render();
        }

        if (!rmdir($file['path'])) {
            $err = error_get_last();
            $template->assign('error', $err['message']);
            Http_Response::getInstance()->render();
        }

        $q = $db->prepare('DELETE FROM `files` WHERE `id` = ?');
        if (!$q->execute(array($id))) {
            $template->assign('error', implode("\n", $q->errorInfo()));
            Http_Response::getInstance()->render();
        }


        $f_chmod = '';
        foreach ($ex as $chmod) {
            $f_chmod .= $chmod . '/';
            chmod($f_chmod . '/', 0777);
        }

        $scanner = new Scanner();
        $scanner->scanCount();

        $template->assign('message', 'Каталог удален');
        break;


    case 'del_file':
        if (!Config::get('delete_dir')) {
            $template->assign('error', 'Error');
            Http_Response::getInstance()->render();
        }
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            Http_Response::getInstance()->render();
        }

        $ex = explode('/', $file['path']);
        $f_chmod = '';
        foreach ($ex as $chmod) {
            $f_chmod .= $chmod;
            if (is_dir($f_chmod)) {
                $f_chmod = $f_chmod . '/';
            }

            @chmod($f_chmod, 0777);
        }

        $q = $db->prepare('DELETE FROM `files` WHERE `id` = ?');
        if (!$q->execute(array($id))) {
            $template->assign('error', implode("\n", $q->errorInfo()));
            Http_Response::getInstance()->render();
        }

        if (!unlink($file['path'])) {
            $err = error_get_last();
            $template->assign('error', $err['message']);
            Http_Response::getInstance()->render();
        }

        if ($file['attach']) {
            Files::delAttach($file['infolder'], $id, unserialize($file['attach']));
        }

        Files::updateDirCount($file['path'], false);

        $template->assign('message', 'Файл удален');
        break;


    case 'priority':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Директория не найдена');
            Http_Response::getInstance()->render();
        }

        if ($_GET['to'] == 'down') {
            $q = $db->prepare('UPDATE `files` SET `priority` = `priority` - 1 WHERE `id` = ?');
        } else {
            $q = $db->prepare('UPDATE `files` SET `priority` = `priority` + 1 WHERE `id` = ?');
        }

        if ($q->execute(array($id))) {
            $template->assign('message', 'Приоритет директории изменен');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'about':
        $template->setTemplate('apanel/files/about.tpl');

        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            Http_Response::getInstance()->render();
        }

        if ($_POST) {
            $result = Files::addAbout($file['path'], $_POST['about']);

            if ($result['message']) {
                $template->assign('message', implode("\n", $result['message']));
            }
            if ($result['error']) {
                $template->assign('error', implode("\n", $result['error']));
            }
        }

        $template->assign('about', file_get_contents(Config::get('opath') . strstr($file['path'], '/') . '.txt'));
        break;


    case 'seo':
        $template->setTemplate('apanel/files/seo.tpl');

        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            Http_Response::getInstance()->render();
        }

        $seo = unserialize($file['seo']);

        if ($_POST) {
            $seo = array(
                 'title' => $_POST['title'],
                 'keywords' => $_POST['keywords'],
                 'description' => $_POST['description']
            );

            $q = $db->prepare('UPDATE `files` SET `seo` = ? WHERE `id` = ?');
            if ($q->execute(array(serialize($seo), $id))) {
                $template->assign('message', 'Данные изменены');
            } else {
                $template->assign('error', implode("\n", $q->errorInfo()));
            }
        }

        $template->assign('file', $file);
        $template->assign('seo', $seo);
        break;


    case 'add_screen':
        $template->setTemplate('apanel/files/add_screen.tpl');

        if ($_FILES) {
            $file = Files::getFileInfo($id);
            if (!$file) {
                $template->assign('error', 'Не найден файл');
                Http_Response::getInstance()->render();
            }

            $tmp = CORE_DIRECTORY . '/tmp/screen_' . $_FILES['screen']['name'];
            if (move_uploaded_file($_FILES['screen']['tmp_name'], $tmp) === false) {
                $err = error_get_last();
                $template->assign('error', $err['message']);
                Http_Response::getInstance()->render();
            }

            $result = Files::addScreen($file['path'], $tmp);
            unlink($tmp);

            if ($result['message']) {
                $template->assign('message', implode("\n", $result['message']));
            }
            if ($result['error']) {
                $template->assign('error', implode("\n", $result['error']));
            }
        }
        break;


    case 'del_screen':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Не найдена директория');
            Http_Response::getInstance()->render();
        }

        $path = strstr($file['path'], '/'); // убираем папку с загрузками

        $a = @unlink(Config::get('spath') . $path . '.gif');
        $b = @unlink(Config::get('spath') . $path . '.thumb.gif');
        $c = @unlink(Config::get('spath') . $path . '.jpg');
        $d = @unlink(Config::get('spath') . $path . '.png');

        if ($a || $b || $c || $d) {
            $template->assign('message', 'Скриншот удален');
        } else {
            $err = error_get_last();
            $template->assign('error', $err['message']);
        }
        break;


    case 'add_ico':
        $template->setTemplate('apanel/files/add_ico.tpl');

        if ($_FILES) {
            $file = Files::getFileInfo($id);
            if (!$file) {
                $template->assign('error', 'Не найдена директория');
                Http_Response::getInstance()->render();
            }

            $to = $file['path'] . 'folder.png';
            if (file_exists($to)) {
                $template->assign('error', 'Иконка уже существует');
                Http_Response::getInstance()->render();
            }

            $ext = strtolower(pathinfo($_FILES['ico']['name'], PATHINFO_EXTENSION));
            if (!Media_Image::isSupported($ext)) {
                $template->assign('error', 'Поддерживаются иконки jpeg, gif, png и bmp формата');
                Http_Response::getInstance()->render();
            }

            $tmp_file = CORE_DIRECTORY . '/tmp/' . uniqid('addico_') . '.png';
            if (!move_uploaded_file($_FILES['ico']['tmp_name'], $tmp_file)) {
                $template->assign('error', 'Не удалось переместить иконку');
                Http_Response::getInstance()->render();
            }

            Media_Image::toPng($tmp_file);

            if (rename($tmp_file, $to)) {
                chmod($to, 0644);
                $template->assign('message', 'Загрузка иконки прошла успешно');
            } else {
                $template->assign('error', 'Загрузка иконки окончилась неудачно');
            }
        }
        break;


    case 'del_ico':
        $file = Files::getFileInfo($id);
        if (!$file) {
            $template->assign('error', 'Не найдена директория');
            Http_Response::getInstance()->render();
        }

        if (!file_exists($file['path'] . 'folder.png')) {
            $template->assign('error', 'Иконки к данной папке не существует');
            Http_Response::getInstance()->render();
        }

        if (unlink($file['path'] . 'folder.png')) {
            $template->assign('message', 'Удаление иконки прошло успешно');
        } else {
            $template->assign('error', 'Удаление иконки окончилось неудачно');
        }
        break;


    case 'add_dir':
        $template->setTemplate('apanel/files/add_dir.tpl');

        $langpacks = Language::getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            $result = Files::addDir(
                $_POST['realname'],
                Config::get('path') . trim($_POST['topath']),
                $_POST['dir']['english'],
                $_POST['dir']['russian'],
                $_POST['dir']['azerbaijan'],
                $_POST['dir']['turkey']
            );

            if ($result['message']) {
                $template->assign('message', implode("\n", $result['message']));
            }
            if ($result['error']) {
                $template->assign('error', implode("\n", $result['error']));
            }
        }

        $template->assign('dirs', Files::getAllDirs());
        break;


    case 'edit_news':
        $template->setTemplate('apanel/news/edit.tpl');

        $langpacks = Language::getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            foreach ($_POST['new'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': введите текст новости.');
                    Http_Response::getInstance()->render();
                }
            }

            $q = $db->prepare('UPDATE `news` SET `news` = ?, `rus_news` = ?, `aze_news` = ?, `tur_news` = ? WHERE `id` = ?');
            $result = $q->execute(array(
                $_POST['new']['english'],
                $_POST['new']['russian'],
                $_POST['new']['azerbaijan'],
                $_POST['new']['turkey'],
                $_GET['news']
            ));

            if (!$result) {
                $template->assign('error', implode("\n", $q->errorInfo()));
            } else {
                $template->assign('message', 'Новость изменена');
            }
        }

        $template->assign('news', News::getNewsInfo($_GET['news']));
        break;


    case 'add_news':
        $template->setTemplate('apanel/news/add.tpl');

        $langpacks = Language::getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            foreach ($_POST['new'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': введите текст новости.');
                    Http_Response::getInstance()->render();
                }
            }

            $q = $db->prepare('
                INSERT INTO `news` SET `news` = ?, `rus_news` = ?, `aze_news` = ?, `tur_news` = ?, `time` = UNIX_TIMESTAMP()
            ');
            $result = $q->execute(array(
                $_POST['new']['english'],
                $_POST['new']['russian'],
                $_POST['new']['azerbaijan'],
                $_POST['new']['turkey']
            ));

            if (!$result) {
                $template->assign('error', implode("\n", $q->errorInfo()));
            } else {
                $template->assign('message', 'Новость добавлена');
            }
        }
        break;


    case 'scan':
        $template->setTemplate('apanel/scan.tpl');

        $scan = Config::get('path');

        if (isset($_GET['id'])) {
            $file = Files::getFileInfo($id);

            if (!$file || is_dir($file['path']) === false) {
                $template->assign('error', 'Такой категории не существует');
                Http_Response::getInstance()->render();
            } else {
                $scan = $file['path'];
            }
        }

        @set_time_limit(99999);
        @ini_set('max_execution_time', 99999);
        @ignore_user_abort(true);

        ini_set('memory_limit', '256M');


        $scanner = new Scanner();
        $data = $scanner->scan($scan);
        $scanner->scanCount();

        if ($data['errors']) {
            $template->assign('error', implode("\n", $data['errors']));
        }

        $template->assign('data', $data);
        break;


    case 'id3_file':
        $template->setTemplate('apanel/id3_file.tpl');

        $file = Files::getFileInfo($id);
        $idv2 = Media_Audio::getInfo($id, $file['path']);

        $id3 = new MP3_Id();
        $id3->read($file['path']);

        $genres = $id3->genres();
        $template->assign('genres', $genres);

        $template->assign('name', $idv2['tag']['title']);
        $template->assign('artists', $idv2['tag']['artist']);
        $template->assign('album', $idv2['tag']['album']);
        $template->assign('year', $idv2['tag']['date']);
        $template->assign('track', Helper::str2utf8($id3->track));
        $template->assign('genre', $idv2['tag']['genre']);
        $template->assign('comment', $idv2['tag']['comment']);

        if ($_POST) {
            @unlink(CORE_DIRECTORY . '/cache/' . $id . '.dat');

            $name = mb_convert_encoding($_POST['name'], 'windows-1251', 'utf-8');
            $artist = mb_convert_encoding($_POST['artists'], 'windows-1251', 'utf-8');
            $album = mb_convert_encoding($_POST['album'], 'windows-1251', 'utf-8');
            $year = mb_convert_encoding($_POST['year'], 'windows-1251', 'utf-8');
            $track = mb_convert_encoding($_POST['track'], 'windows-1251', 'utf-8');
            $genre = mb_convert_encoding($_POST['genre'], 'windows-1251', 'utf-8');
            $comment = mb_convert_encoding($_POST['comment'], 'windows-1251', 'utf-8');


            // Записываем Idv2 теги
            $mp3 = new mp3($file['path']);
            //$mp3->striptags(); // bug
            $mp3->setIdv3_2(
                $track,
                $name,
                $artist,
                $album,
                $year,
                $genre,
                $comment,
                $artist,
                $artist,
                $comment,
                'http://' . $_SERVER['HTTP_HOST'],
                ''
            );
            $mp3->save($file['path']);

            // записываем Idv1 теги
            $id3->name = $name;
            $id3->artists = $artist;
            $id3->album = $album;
            $id3->year = $year;
            $id3->track = $track;
            $id3->genre = $genre;
            $id3->comment = $comment;
            $id3->write();
            if (PEAR::isError($result) == false) {
                $template->assign('message', 'MP3 теги изменены');
            } else {
                $template->assign('error', 'MP3 теги не изменены');
            }
        }
        break;


    case 'id3':
        $template->setTemplate('apanel/id3.tpl');

        $id3 = new MP3_Id();

        $genres = $id3->genres();
        array_unshift($genres, '');

        $template->assign('genres', $genres);

        if ($_POST) {
            if ($_POST['name'] != '') {
                $_POST['name'] = mb_convert_encoding($_POST['name'], 'windows-1251', 'utf-8');
            }
            if ($_POST['artists'] != '') {
                $_POST['artists'] = mb_convert_encoding($_POST['artists'], 'windows-1251', 'utf-8');
            }
            if ($_POST['album'] != '') {
                $_POST['album'] = mb_convert_encoding($_POST['album'], 'windows-1251', 'utf-8');
            }
            if ($_POST['year'] != '') {
                $_POST['year'] = mb_convert_encoding($_POST['year'], 'windows-1251', 'utf-8');
            }
            if ($_POST['track'] != '') {
                $_POST['track'] = mb_convert_encoding($_POST['track'], 'windows-1251', 'utf-8');
            }
            if ($_POST['genre'] != '') {
                $_POST['genre'] = mb_convert_encoding($_POST['genre'], 'windows-1251', 'utf-8');
            }
            if ($_POST['comment'] != '') {
                $_POST['comment'] = mb_convert_encoding($_POST['comment'], 'windows-1251', 'utf-8');
            }

            $all = 0;
            $write = 0;
            $cacheDir = CORE_DIRECTORY . '/cache';

            $q = $db->query('SELECT * FROM `files` WHERE `dir` = "0" AND `path` LIKE("%.mp3")');
            foreach ($q as $f) {
                $all++;
                @unlink($cacheDir . '/' . $f['id'] . '.dat');

                $f = realpath($f['path']);

                // Записываем Idv2 теги
                $mp3 = new mp3($f);
                //$mp3->striptags(); // bug
                $mp3->setIdv3_2(
                    $_POST['track'],
                    $_POST['name'],
                    $_POST['artist'],
                    $_POST['album'],
                    $_POST['year'],
                    $_POST['genre'],
                    $_POST['comment'],
                    $_POST['artist'],
                    $_POST['artist'],
                    $_POST['comment'],
                    'http://' . $_SERVER['HTTP_HOST'],
                    ''
                );
                $mp3->save($f);


                $id3->read($f);
                if (PEAR::isError($id3->read($f))) {
                    continue;
                }


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
                $result = $id3->write();

                if (PEAR::isError($result) == false) {
                    $write++;
                }
            }

            $template->assign('message', 'Всего просканировано ' . $all . ' файлов. Теги заданы для ' . $write . ' файлов');
        }
        break;


    case 'mark':
        $template->setTemplate('apanel/mark.tpl');

        if ($_POST) {
            if (isset($_POST['marker'])) {
                $q = $db->prepare('REPLACE INTO setting(name, value) VALUES(?, ?)');
                $result1 = $q->execute(array('marker', $_POST['marker']));
                $result2 = $q->execute(array('marker_where', ($_POST['marker_where'] == 'top' ? 'top' : 'foot')));
                if ($result1 && $result2) {
                    $template->assign('message', 'Настройки маркера изменены');
                } else {
                    $template->assign('error', implode("\n", $q->errorInfo()));
                }
            } else {
                $q = $db->query('SELECT `path` FROM `files` WHERE `path` LIKE "%.jpg" OR `path` LIKE "%.jpe" OR `path` LIKE "%.jpeg" OR `path` LIKE "%.gif" OR `path` LIKE "%.png"');
                $all = $q->rowCount();
                $i = 0;
                $textLen = mb_strlen($_POST['text']);
                $rgb = Helper::hex2rgb($_POST['color']);

                foreach ($q as $arr) {
                    list($w, $h, $type) = getimagesize($arr['path']);


                    switch ($type) {
                        case IMAGETYPE_GIF:
                            $pic = imagecreatefromgif($arr['path']);
                            break;


                        case IMAGETYPE_JPEG:
                            $pic = imagecreatefromjpeg($arr['path']);
                            break;


                        case IMAGETYPE_PNG:
                            $pic = imagecreatefrompng($arr['path']);
                            break;

                        default:
                            $pic = false;
                            break;
                    }

                    if ($pic) {
                        $f = false;

                        // цвет
                        $color = imagecolorallocate($pic, $rgb[0], $rgb[1], $rgb[2]);

                        // верх/низ
                        if ($_POST['y'] == 'foot') {
                            $y = $h - ($_POST['size'] * 1.5);
                        } else {
                            $y = intval($_POST['size']);
                        }


                        // imagestring($pic, $_POST['size'], ($w/2)-(strlen($_POST['text'])*3), $y, $_POST['text'], $color);
                        imagettftext(
                            $pic,
                            $_POST['size'],
                            0,
                            ($w / 2) - ($textLen * 3),
                            $y,
                            $color,
                            CORE_DIRECTORY . '/resources/font.ttf',
                            $_POST['text']
                        );

                        switch ($type) {
                            case IMAGETYPE_GIF:
                                $f = imagegif($pic, $arr[0]);
                                break;


                            case IMAGETYPE_JPEG:
                                $f = imagejpeg($pic, $arr[0], 100);
                                break;


                            case IMAGETYPE_PNG:
                                $f = imagepng($pic, $arr[0], 100);
                                break;
                        }

                        if ($f) {
                            $i++;
                        }
                    }
                }
                $template->assign('message', 'Всего картинок: ' . $all . ', промаркированы: ' . $i);
            }
        }
        break;


    case 'import':
        $template->setTemplate('apanel/import.tpl');

        if ($_POST) {
            $import = new Import(Config::getAll());
            $result = $import->importFiles();

            if ($result['message']) {
                $template->assign('message', implode("\n", $result['message']));
            }
            if ($result['error']) {
                $template->assign('error', implode("\n", $result['error']));
            }
        }
        break;


    case 'upload':
        $template->setTemplate('apanel/upload.tpl');

        $template->assign('dirs', Files::getAllDirs());


        if ($_POST) {
            $newpath = Config::get('path') . trim($_POST['topath']);
            if ($newpath == '') {
                $template->assign('error', 'Нет конечного пути');
                Http_Response::getInstance()->render();
            }
            if (is_writable($newpath) === false) {
                $template->assign('error', 'Директория ' . $newpath . ' недоступна для записи');
                Http_Response::getInstance()->render();
            }
            @chmod($newpath, 0777);

            if ($_GET['type'] == 'url') {
                $result = Files::uploadUrls($newpath);
            } else {
                $result = Files::uploadFiles($newpath);
            }

            if ($result['message']) {
                $template->assign('message', implode("\n", $result['message']));
            }
            if ($result['error']) {
                $template->assign('error', implode("\n", $result['error']));
            }
        }
        break;


    case 'service':
        $template->setTemplate('apanel/service.tpl');

        $users = $db->query('SELECT COUNT(1) FROM `users_profiles`')->fetchColumn();
        $template->assign('users', $users);

        if ($_POST) {
            switch (@$_GET['mode']) {
                case 'del':
                    $q1 = $db->prepare('DELETE FROM `users_profiles` WHERE `id` = ?');
                    $q2 = $db->prepare('DELETE FROM `users_settings` WHERE `parent_id` = ?');

                    if ($q1->execute(array($_POST['user'])) && $q2->execute(array($_POST['user']))) {
                        $template->assign('message', 'Пользователь удален');
                    } else {
                        $template->assign('error', implode("\n", $q1->errorInfo()) . implode("\n", $q2->errorInfo()));
                    }
                    break;

                default:
                    $q = $db->prepare('REPLACE INTO setting(name, value) VALUES(?, ?)');

                    if ($q->execute(array('service_head', $_POST['service_head'])) && $q->execute(array('service_foot', $_POST['service_foot']))) {
                        $template->assign('message', 'Настройки сервиса изменены');
                    } else {
                        $template->assign('error', implode("\n", $q->errorInfo()));
                    }
                    break;
            }
        }
        break;


    case 'exchanger':
        $template->setTemplate('apanel/exchanger.tpl');

        if ($_POST) {
            $q = $db->prepare('REPLACE INTO setting(name, value) VALUES(?, ?)');

            if ($q->execute(array('exchanger_notice', ($_POST['exchanger_notice'] ? 1 : 0))) &&
                $q->execute(array('exchanger_extensions', $_POST['exchanger_extensions'])) &&
                $q->execute(array('exchanger_name', $_POST['exchanger_name'])) &&
                $q->execute(array('exchanger_hidden', ($_POST['exchanger_hidden'] ? 1 : 0)))) {
                $template->assign('message', 'Настройки обменника изменены');
            } else {
                $template->assign('error', implode("\n", $q->errorInfo()));
            }
        }
        break;


    case 'lib':
        $template->setTemplate('apanel/lib.tpl');

        if ($_POST) {
            $q = $db->prepare('REPLACE INTO setting(name, value) VALUES(?, ?)');

            if ($q->execute(array('lib', $_POST['lib'])) && $q->execute(array('lib_str', $_POST['lib_str']))) {
                $template->assign('message', 'Настройки библиотеки изменены');
            } else {
                $template->assign('error', implode("\n", $q->errorInfo()));
            }
        }
        break;


    case 'buy':
        $template->setTemplate('apanel/buy.tpl');

        if ($_POST) {
            $q = $db->prepare('REPLACE INTO setting(name, value) VALUES(?, ?)');

            if ($q->execute(array('buy', $_POST['buy'])) &&
                $q->execute(array('randbuy', ($_POST['randbuy'] ? 1 : 0))) &&
                $q->execute(array('countbuy', $_POST['countbuy'])) &&
                $q->execute(array('banner', $_POST['banner'])) &&
                $q->execute(array('randbanner', ($_POST['randbanner'] ? 1 : 0))) &&
                $q->execute(array('countbanner', $_POST['countbanner']))) {
                $template->assign('message', 'Настройки рекламы сохранены');
            } else {
                $template->assign('error', implode("\n", $q->errorInfo()));
            }
        }
        break;


    case 'log':
        $template->setTemplate('apanel/log.tpl');

        $logs = $db->query('SELECT * FROM `loginlog` WHERE `id` > 1 ORDER BY `time` DESC LIMIT 50')->fetchAll();

        $template->assign('logs', $logs);
        break;


    case 'sec':
        $template->setTemplate('apanel/sec.tpl');

        if ($_POST) {
            if (!$_POST['pwd'] || md5($_POST['pwd']) != Config::get('password')) {
                $template->assign('error', 'Неверный пароль');
                Http_Response::getInstance()->render();
            }

            $_POST['autologin'] = $_POST['autologin'] ? 1 : 0;
            $_POST['delete_dir'] = $_POST['delete_dir'] ? 1 : 0;
            $_POST['delete_file'] = $_POST['delete_file'] ? 1 : 0;

            foreach ($_POST as $key => $value) {
                if ($value == '' && $key != 'password' && $key != 'autologin' && $key != 'delete_dir' && $key != 'delete_file') {
                    $template->assign('error', 'Не заполнено одно из полей');
                    Http_Response::getInstance()->render();
                }
            }

            $q = $db->prepare('UPDATE `setting` SET `value` = ? WHERE `name` = ?');

            if ($_POST['password'] != '') {
                $_SESSION['authorise'] = md5($_POST['password']);
                $q->execute(array($_SESSION['authorise'], 'password'));
            }
            $q->execute(array($_POST['countban'], 'countban'));
            $q->execute(array($_POST['timeban'], 'timeban'));
            $q->execute(array($_POST['autologin'], 'autologin'));
            $q->execute(array($_POST['delete_file'], 'delete_file'));
            $q->execute(array($_POST['delete_dir'], 'delete_dir'));

            $template->assign('message', 'Настройки изменены');
        }
        break;


    case 'modules':
        $template->setTemplate('apanel/modules.tpl');

        if ($_POST) {
            $_POST['comments_change'] = $_POST['comments_change'] ? 1 : 0;
            $_POST['comments_captcha'] = $_POST['comments_captcha'] ? 1 : 0;
            $_POST['eval_change'] = $_POST['eval_change'] ? 1 : 0;
            $_POST['onpage_change'] = $_POST['onpage_change'] ? 1 : 0;
            $_POST['preview_change'] = $_POST['preview_change'] ? 1 : 0;
            $_POST['top_change'] = $_POST['top_change'] ? 1 : 0;
            $_POST['new_change'] = $_POST['new_change'] ? 1 : 0;
            $_POST['stat_change'] = $_POST['stat_change'] ? 1 : 0;
            $_POST['search_change'] = $_POST['search_change'] ? 1 : 0;
            $_POST['pagehand_change'] = $_POST['pagehand_change'] ? 1 : 0;
            $_POST['zip_change'] = $_POST['zip_change'] ? 1 : 0;
            $_POST['jad_change'] = $_POST['jad_change'] ? 1 : 0;
            $_POST['zakaz_change'] = $_POST['zakaz_change'] ? 1 : 0;
            $_POST['buy_change'] = $_POST['buy_change'] ? 1 : 0;
            $_POST['cut_change'] = $_POST['cut_change'] ? 1 : 0;
            $_POST['audio_player_change'] = $_POST['audio_player_change'] ? 1 : 0;
            $_POST['video_player_change'] = $_POST['video_player_change'] ? 1 : 0;
            $_POST['lib_change'] = $_POST['lib_change'] ? 1 : 0;

            $_POST['screen_change'] = $_POST['screen_change'] ? 1 : 0;
            $_POST['screen_file_change'] = $_POST['screen_file_change'] ? 1 : 0;
            $_POST['swf_change'] = $_POST['swf_change'] ? 1 : 0;
            $_POST['swf_file_change'] = $_POST['swf_file_change'] ? 1 : 0;
            $_POST['jar_change'] = $_POST['jar_change'] ? 1 : 0;
            $_POST['jar_file_change'] = $_POST['jar_file_change'] ? 1 : 0;

            $_POST['anim_change'] = $_POST['anim_change'] ? 1 : 0;
            $_POST['prev'] = $_POST['prev'] ? 1 : 0;
            $_POST['lib_desc'] = $_POST['lib_desc'] ? 1 : 0;
            $_POST['ext'] = $_POST['ext'] ? 1 : 0;
            $_POST['prev_next'] = $_POST['prev_next'] ? 1 : 0;
            $_POST['style_change'] = $_POST['style_change'] ? 1 : 0;
            $_POST['service_change'] = $_POST['service_change'] ? 1 : 0;
            $_POST['service_change_advanced'] = $_POST['service_change_advanced'] ? 1 : 0;
            $_POST['abuse_change'] = $_POST['abuse_change'] ? 1 : 0;
            $_POST['exchanger_change'] = $_POST['exchanger_change'] ? 1 : 0;
            $_POST['send_email'] = $_POST['send_email'] ? 1 : 0;
            $_POST['ignore_index_breadcrumbs'] = $_POST['ignore_index_breadcrumbs'] ? 1 : 0;
            $_POST['ignore_index_pages'] = $_POST['ignore_index_pages'] ? 1 : 0;

            $q = $db->prepare('REPLACE INTO `setting`(`name`, `value`) VALUES (?, ?)');
            foreach ($_POST as $key => $value) {
                if ($key == 'password' || $key == 'delete_dir' || $key == 'delete_file') {
                    $template->assign('error', 'Error');
                    break;
                }
                $q->execute(array($key, $value));
            }
            $template->assign('message', 'Список модулей изменен');
        }
        break;


    case 'setting':
        $template->setTemplate('apanel/setting.tpl');

        if ($_POST) {
            $_POST['site_url'] = str_ireplace('http://', '', $_POST['site_url']);

            $q = $db->prepare('REPLACE INTO `setting`(`name`, `value`) VALUES (?, ?)');
            foreach ($_POST as $key => $value) {
                if ($value == '') {
                    $template->assign('error', 'Не заполнено одно из полей');
                    break;
                }
                $q->execute(array($key, $value));
            }
            $template->assign('message', 'Настройки сохранены');
        }

        $styles = array();
        foreach (glob(CORE_DIRECTORY . '/../style/*.css', GLOB_NOESCAPE) as $v) {
            $styles[] = pathinfo($v, PATHINFO_FILENAME);
        }

        $langpacks = Language::getLangpacks();

        $template->assign('styles', $styles);
        $template->assign('langpacks', $langpacks);
        break;


    case 'del_news':
        $q = $db->prepare('DELETE FROM `news` WHERE `id` = ?');

        if ($q->execute(array($_GET['news']))) {
            $template->assign('message', 'Новость удалена');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'del_comment_news_comments':
        $q = $db->prepare('DELETE FROM `news_comments` WHERE `id` = ?');

        if ($q->execute(array($_GET['comment']))) {
            $template->assign('message', 'Комментарий удален');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'del_comment_view_comments':
        $q = $db->prepare('DELETE FROM `comments` WHERE `id` = ?');

        if ($q->execute(array($_GET['comment']))) {
            $template->assign('message', 'Комментарий удален');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'clearcomm':
        $q = $db->prepare('DELETE FROM `comments` WHERE `file_id` = ?');

        if ($q->execute(array($id))) {
            $template->assign('message', 'Комментарии удалены');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'clearrate':
        $q = $db->prepare('UPDATE `files` SET `ips` = "", `yes` = 0, `no` = 0 WHERE `id` = ?');

        if ($q->execute(array($id))) {
            $template->assign('message', 'Рейтинг сброшен');
        } else {
            $template->assign('error', implode("\n", $q->errorInfo()));
        }
        break;


    case 'optm':
        if ($db->exec('OPTIMIZE TABLE `comments`, `files`, `loginlog`, `news`, `news_comments`, `online`, `setting`, `users_profiles`, `users_settings`') !== false) {
            $template->assign('message', 'Таблицы оптимизированы');
        } else {
            $template->assign('error', 'Ошибка при оптимизации таблиц');
        }
        break;


    case 'clean':
        $template->setTemplate('apanel/clean.tpl');
        break;


    case 'cleantrash':
        $d = 0;

        $q1 = $db->prepare('DELETE FROM `files` WHERE `id` = ?');
        $q2 = $db->prepare('DELETE FROM `comments` WHERE `file_id` = ?');
        foreach ($db->query('SELECT `id`, `path` FROM `files`') as $row) {
            if (!file_exists($row['path'])) {
                $q1->execute(array($row['id']));
                $q2->execute(array($row['id']));

                Files::updateDirCount($row['path'], false);

                $d++;
            }
        }

        $template->assign('message', 'Удалено неверных записей: ' . $d);
        break;


    case 'cleandb':
        if ($db->exec('TRUNCATE TABLE `files`') !== false && $db->exec('TRUNCATE TABLE `comments`') !== false) {
            $template->assign('message', 'Таблицы очищены');
        } else {
            $template->assign('error', 'Ошибка при очистке таблиц');
        }
        break;


    case 'cleannews':
        if ($db->exec('TRUNCATE TABLE `news`') !== false && $db->exec('TRUNCATE TABLE `news_comments`') !== false) {
            $template->assign('message', 'Таблицы очищены');
        } else {
            $template->assign('error', 'Ошибка при очистке таблиц');
        }
        break;


    case 'cleancomm':
        if ($db->exec('TRUNCATE TABLE `comments`') !== false) {
            $template->assign('message', 'Все комментарии к файлам удалены');
        } else {
            $template->assign('error', 'Ошибка при удалении комментариев к файлам');
        }
        break;


    case 'cleancomm_news':
        if ($db->exec('TRUNCATE TABLE `news_comments`') !== false) {
            $template->assign('message', 'Все комментарии к новостям удалены');
        } else {
            $template->assign('error', 'Ошибка при удалении комментариев к новостям');
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
                $err .= $_GET['dir'] . '/' . $f . "\n";
            }
        }

        if ($err) {
            $template->assign('error', 'Не удалось удалить следующие файлы: ' . $err);
        } else {
            $template->assign('message', 'Кэш очищен');
        }
        break;
}


Http_Response::getInstance()->render();
