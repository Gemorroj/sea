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

require 'moduls/header.php';

$template->setTemplate('apanel/index.tpl');
//TODO:breadcrumbs?
/*
$template->assign('breadcrumbs', array(
    'apanel/apanel.php' => 'Admin Panel',
    '*' => 'Сообщение',
));
*/



mysql_query('REPLACE INTO `loginlog` SET `time` = UNIX_TIMESTAMP(), `access_num` = 0, `id` = 1', $mysql);
if (mysql_result(mysql_query('SELECT COUNT(1) FROM `loginlog`', $mysql), 0) > 21) {
    mysql_query('DELETE FROM `loginlog` WHERE `id` <> 1 ORDER BY `id` LIMIT 1', $mysql);
}
###################################################
if (!$_SESSION) {
    exit('Не запущена сессия');
}
if (!isset($_SESSION['authorise']) || !isset($_SESSION['ipu'])) {
    exit('В сессии недостаточно данных для авторизации');
}
if ($_SESSION['authorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    exit('Авторизация не пройдена');
}


switch (isset($_GET['action']) ? $_GET['action'] : null) {
    case 'add_attach':
        $file = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            $template->send();
        }

        if (!$_FILES || !isset($_FILES['attach'])) {
            $template->assign('error', 'Нет загружаемого файла');
            $template->send();
        }
        if ($_FILES['attach']['error']) {
            $template->assign('error', 'Ошибка при загрузке файла. Код ошибки: ' . $_FILES['attach']['error']);
            $template->send();
        }
        if (!checkExt(pathinfo($_FILES['attach']['name'], PATHINFO_EXTENSION))) {
            $template->assign('error', 'Недоступное расширение для загрузки');
            $template->send();
        }

        if ($file['attach']) {
            $attach = unserialize($file['attach']);
            $key = sizeof($attach);
        } else {
            $attach = array();
            $key = 0;
        }

        if (add_attach($file['infolder'], $id, array($key => $_FILES['attach']))) {
            $attach[$key] = $_FILES['attach']['name'];
            if (mysql_query('UPDATE `files` SET `attach` = "' . mysql_real_escape_string(serialize($attach), $mysql) . '" WHERE `id` = ' . $id, $mysql)) {
                $template->assign('message', 'Вложение добавлено');
            } else {
                $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            }
        } else {
            $err = error_get_last();
            $template->assign('error', 'Ошибка: ' . $err['message']);
        }
        break;


    case 'del_attach':
        $file = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            $template->send();
        }

        $attach = unserialize($file['attach']);
        $name = '';
        if (isset($attach[$_GET['attach']])) {
            $name = $attach[$_GET['attach']];
            unset($attach[$_GET['attach']]);
        }

        $result = null;
        if (!$attach) {
            $result = mysql_query('UPDATE `files` SET `attach` = NULL WHERE `id` = ' . $id, $mysql);
        } else {
            $result = mysql_query('
                UPDATE `files`
                SET `attach` = "' . mysql_real_escape_string(serialize($attach), $mysql) . '"
                WHERE `id` = ' . $id,
                $mysql
            );
        }

        if ($result) {
            del_attach($file['infolder'], $id, array($_GET['attach'] => $name));
            $template->assign('message', 'Вложение удалено');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'move':
        $file = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            $template->send();
        }

        $folder = $_POST['topath'];
        $filename = basename($file['path']);

        if (!is_dir($folder)) {
            $template->assign('error', 'Указанной директории не существует');
            $template->send();
        }
        if (!is_writeable($folder)) {
            $template->assign('error', 'Указанная директория не доступна для записи');
            $template->send();
        }
        if (file_exists($folder . $filename)) {
            $template->assign('error', 'Файл с таким именем в указанной директории уже есть');
            $template->send();
        }
        if (!rename($file['path'], $folder . $filename)) {
            $err = error_get_last();
            $template->assign('error', 'Ошибка: ' . $err['message']);
            $template->send();
        }

        if (mysql_query('
            UPDATE `files`
            SET `path` = "' . mysql_real_escape_string($folder . $filename, $mysql) . '",
            `infolder` = "' . mysql_real_escape_string($folder, $mysql) . '"
            WHERE `id` = ' . $id,
            $mysql
        )) {
            dir_count($folder, false);
            dir_count($folder['path'], true);

            $path1 = strstr($file['path'], '/'); // убираем папку с загрузками
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

            $template->assign('message', 'Файл перемещен');
        } else {
            rename($folder . $filename, $file['path']); // переименовываем обратно
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'hidden':
        $info = mysql_fetch_assoc(mysql_query('SELECT 1 FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$info) {
            $template->assign('error', 'Директория или файл не найдены');
            $template->send();
        }

        if ($_GET['hide'] == '1') {
            $query = 'UPDATE `files` SET `hidden` = "1" WHERE `id` = ' . $id;
        } else {
            $query = 'UPDATE `files` SET `hidden` = "0" WHERE `id` = ' . $id;
        }

        if (mysql_query($query, $mysql)) {
            $template->assign('message', 'Видимость изменена');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'rename':
        $template->setTemplate('apanel/files/rename.tpl');

        $info = mysql_fetch_assoc(
            mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql)
        );
        if (!$info) {
            $template->assign('error', 'Директория или файл не найдены');
            $template->send();
        }

        $template->assign('info', $info);

        $langpacks = Language::getInstance()->getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            foreach ($_POST['new'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': укажите название');
                    $template->send();
                }
            }
            $eng = mysql_real_escape_string($_POST['new']['english'], $mysql);
            $rus = mysql_real_escape_string($_POST['new']['russian'], $mysql);
            $aze = mysql_real_escape_string($_POST['new']['azerbaijan'], $mysql);
            $tur = mysql_real_escape_string($_POST['new']['turkey'], $mysql);

            mysql_query(
                "
                UPDATE `files`
                SET name = '" . $eng . "',
                rus_name = '" . $rus . "',
                aze_name = '" . $aze . "',
                tur_name = '" . $tur . "'
                WHERE `id` = " . $id
                ,
                $mysql
            );
            $error = mysql_error($mysql);
            if ($error) {
                $template->assign('error', 'Ошибка: ' . $error);
            } else {
                $template->assign('message', 'Название изменено');
            }
        }
        break;


    case 'del_dir':
        if (!$setup['delete_dir']) {
            $template->assign('error', 'Error');
            $template->send();
        }

        $info = mysql_fetch_assoc(
            mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql)
        );
        if (!$info) {
            $template->assign('error', 'Директория не найдена');
            $template->send();
        }

        $ex = explode('/', $info['path']);
        $f_chmod = '';
        foreach ($ex as $chmod) {
            $f_chmod .= $chmod . '/';
            chmod($f_chmod, 0777);
        }

        foreach (glob($info['path'] . '*') as $vv) {
            if (is_dir($vv)) {
                $template->assign('error', 'Разрешено удалять только директории с одним уровнем вложенности');
                $template->send();
            } else {
                if (!unlink($vv)) {
                    $err = error_get_last();
                    $template->assign('error', 'Ошибка: ' . $err['message']);
                    $template->send();
                }
            }
        }
        if (!mysql_query(
            "DELETE FROM `files` WHERE `infolder` = '" . mysql_real_escape_string($info['path'], $mysql) . "'",
            $mysql
        )
        ) {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            $template->send();
        }

        if (!rmdir($info['path'])) {
            $err = error_get_last();
            $template->assign('error', 'Ошибка: ' . $err['message']);
            $template->send();
        }

        if (!mysql_query('DELETE FROM `files` WHERE `id` = ' . $id, $mysql)) {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            $template->send();
        }


        $f_chmod = '';
        foreach ($ex as $chmod) {
            $f_chmod .= $chmod . '/';
            chmod($f_chmod . '/', 0777);
        }

        scannerCount();

        $template->assign('message', 'Каталог удален');
        break;


    case 'del_file':
        if (!$setup['delete_dir']) {
            $template->assign('error', 'Error');
            $template->send();
        }
        $file = mysql_fetch_assoc(
            mysql_query('SELECT `path`, `infolder`, `attach` FROM `files` WHERE `id` = ' . $id, $mysql)
        );
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            $template->send();
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

        if (!mysql_query('DELETE FROM `files` WHERE `id` = ' . $id, $mysql)) {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            $template->send();
        }

        if (!unlink($file['path'])) {
            $err = error_get_last();
            $template->assign('error', 'Ошибка: ' . $err['message']);
            $template->send();
        }

        if ($file['attach']) {
            del_attach($file['infolder'], $id, unserialize($file['attach']));
        }

        dir_count($file['path'], false);

        $template->assign('message', 'Файл удален');
        break;


    case 'priority':
        $info = mysql_fetch_assoc(mysql_query('SELECT 1 FROM `files` WHERE `dir` = "1" AND `id` = ' . $id, $mysql));
        if (!$info) {
            $template->assign('error', 'Директория не найдена');
            $template->send();
        }

        if ($_GET['to'] == 'down') {
            $query = 'UPDATE `files` SET `priority` = `priority` - 1 WHERE `id` = ' . $id;
        } else {
            $query = 'UPDATE `files` SET `priority` = `priority` + 1 WHERE `id` = ' . $id;
        }

        if (mysql_query($query, $mysql)) {
            $template->assign('message', 'Приоритет директории изменен');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'about':
        $template->setTemplate('apanel/files/about.tpl');

        $file = mysql_fetch_assoc(mysql_query('SELECT `name`, `path` FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            $template->send();
        }

        $about = $setup['opath'] . mb_substr($file['path'], mb_strlen($setup['path'])) . '.txt';

        if ($_POST) {
            chmods($about);

            if ($_POST['about'] == '') {
                if (unlink($about)) {
                    $template->assign('message', 'Описание удалено');
                } else {
                    $err = error_get_last();
                    $template->assign('error', 'Ошибка: ' . $err['message']);
                }
            } else {
                if (file_put_contents($about, trim($_POST['about']))) {
                    $template->assign('message', 'Описание изменено');
                } else {
                    $err = error_get_last();
                    $template->assign('error', 'Ошибка: ' . $err['message']);
                }
            }
        }

        $template->assign('about', file_get_contents($about));
        break;


    case 'seo':
        $template->setTemplate('apanel/files/seo.tpl');

        if ($_POST) {
            $seo = serialize(
                array(
                     'title' => $_POST['title'],
                     'keywords' => $_POST['keywords'],
                     'description' => $_POST['description']
                )
            );
            if (mysql_query(
                'UPDATE `files` SET `seo` = "' . mysql_real_escape_string($seo, $mysql) . '" WHERE `id` = ' . $id,
                $mysql
            )
            ) {
                $template->assign('message', 'Данные изменены');
            } else {
                $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            }
        }

        $file = mysql_fetch_assoc(mysql_query('SELECT `name`, `seo` FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$file) {
            $template->assign('error', 'Файл не найден');
            $template->send();
        }

        $seo = unserialize($file['seo']);

        $template->assign('file', $file);
        $template->assign('seo', $seo);
        break;


    case 'add_screen':
        $template->setTemplate('apanel/files/add_screen.tpl');

        if ($_FILES) {
            $info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
            if (!$info) {
                $template->assign('error', 'Не найдена директория');
                $template->send();
            }

            $info['path'] = strstr($info['path'], '/'); // убираем папку с загрузками
            $to = $setup['spath'] . $info['path'] . '.gif'; // имя конечного файла
            $thumb = $setup['spath'] . $info['path'] . '.thumb.gif'; // имя конечного файла

            $ex = pathinfo($_FILES['screen']['name']);
            $ext = strtolower($ex['extension']);

            if ($ext != 'gif' && $ext != 'jpg' && $ext != 'jpe' && $ext != 'jpeg' && $ext != 'png') {
                $template->assign('error', 'Поддерживаются скриншоты только gif, jpeg и png форматов');
                $template->send();
            }

            chmods($to);

            if (move_uploaded_file($_FILES['screen']['tmp_name'], $to)) {
                if ($ext == 'jpg' || $ext == 'jpe' || $ext == 'jpeg') {
                    $im = imagecreatefromjpeg($to);
                    imagegif($im, $to);
                    imagedestroy($im);
                } elseif ($ext == 'png') {
                    $im = imagecreatefrompng($to);
                    imagegif($im, $to);
                    imagedestroy($im);
                }
                img_resize($to, $thumb, 0, 0, $setup['marker']);

                $template->assign('message', 'Скриншот добавлен');
            } else {
                $err = error_get_last();
                $template->assign('error', 'Ошибка: ' . $err['message']);
            }
        }
        break;


    case 'del_screen':
        $info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$info) {
            $template->assign('error', 'Не найдена директория');
            $template->send();
        }

        $info['path'] = strstr($info['path'], '/'); // убираем папку с загрузками
        $to = $setup['spath'] . $info['path'] . '.gif'; // имя конечного файла
        $to2 = $setup['spath'] . $info['path'] . '.jpg'; // имя конечного файла

        if (unlink($to) || unlink($to2)) {
            $template->assign('message', 'Скриншот удален');
        } else {
            $err = error_get_last();
            $template->assign('error', 'Ошибка: ' . $err['message']);
        }
        break;


    case 'add_ico':
        $template->setTemplate('apanel/files/add_ico.tpl');

        if ($_FILES) {
            $info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
            if (!$info) {
                $template->assign('error', 'Не найдена директория');
                $template->send();
            }

            $to = $info['path'] . 'folder.png';

            if (strtolower(pathinfo($_FILES['ico']['name'], PATHINFO_EXTENSION)) != 'png') {
                $template->assign('error', 'Поддерживаются иконки только png формата');
                $template->send();
            }
            if (file_exists($to)) {
                $template->assign('error', 'Иконка уже существует');
                $template->send();
            }
            if (move_uploaded_file($_FILES['ico']['tmp_name'], $to)) {
                chmod($to, 0644);
                $template->assign('message', 'Загрузка иконки прошла успешно');
            } else {
                $template->assign('error', 'Загрузка иконки окончилась неудачно');
            }
        }
        break;


    case 'del_ico':
        $info = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
        if (!$info) {
            $template->assign('error', 'Не найдена директория');
            $template->send();
        }

        if (!file_exists($info['path'] . 'folder.png')) {
            $template->assign('error', 'Иконки к данной папке не существует');
            $template->send();
        }

        if (unlink($info['path'] . 'folder.png')) {
            $template->assign('message', 'Удаление иконки прошло успешно');
        } else {
            $template->assign('error', 'Удаление иконки окончилось неудачно');
        }
        break;


    case 'add_dir':
        $template->setTemplate('apanel/files/add_dir.tpl');

        $langpacks = Language::getInstance()->getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            if (!preg_match('/^[A-Z0-9_\-]+$/i', $_POST['realname'])) {
                $template->assign('error', 'Не указано имя папки или оно содержит недопустимые символы. Разрешены [A-Za-z0-9_-]');
                $template->send();
            }
            foreach ($_POST['dir'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': укажите название директории.');
                    $template->send();
                }
            }
            if ($_POST['topath'] == '') {
                $template->assign('error', $k . ': укажите название директории.');
                $template->send();
            }


            $newpath = trim($_POST['topath']);
            if ($newpath == '') {
                $template->assign('error', 'Нет конечного пути!');
                $template->send();
            }
            if (!is_writable($newpath)) {
                $template->assign('error', 'Директория ' . $newpath . ' недоступна для записи');
                $template->send();
            }

            $directory = $newpath . $_POST['realname'] . '/';

            $temp = mb_substr($directory, mb_strlen($setup['path']), mb_strlen($directory));

            //скриншоты
            $screen = $setup['spath'] . '/' . $temp;
            // описания
            $desc = $setup['opath'] . '/' . $temp;
            // вложения
            $attach = $setup['apath'] . '/' . $temp;

            $dirnew = array();
            $dirnew['english'] = mysql_real_escape_string($_POST['dir']['english'], $mysql);
            $dirnew['russian'] = mysql_real_escape_string($_POST['dir']['russian'], $mysql);
            $dirnew['azerbaijan'] = mysql_real_escape_string($_POST['dir']['azerbaijan'], $mysql);
            $dirnew['turkey'] = mysql_real_escape_string($_POST['dir']['turkey'], $mysql);


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
            if (mysql_query(
                "INSERT INTO `files`
                (`dir`, `dir_count`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `timeupload`)
                VALUES (
                    '1',
                    0,
                    '" . mysql_real_escape_string($directory, $mysql) . "',
                    '" . $dirnew['english'] . "',
                    '" . $dirnew['russian'] . "',
                    '" . $dirnew['azerbaijan'] . "',
                    '" . $dirnew['turkey'] . "',
                    '" . mysql_real_escape_string($newpath, $mysql) . "',
                    " . $_SERVER['REQUEST_TIME'] . "
                );",
                $mysql
            )) {
                dir_count($newpath, true);
                $template->assign('message', 'Новый каталог создан');
            } else {
                $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            }
        }

        $template->assign('dirs', getAllDirs());
        break;


    case 'edit_news':
        $template->setTemplate('apanel/news/edit.tpl');

        $langpacks = Language::getInstance()->getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            foreach ($_POST['new'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': введите текст новости.');
                    $template->send();
                }
            }

            $eng = mysql_real_escape_string($_POST['new']['english'], $mysql);
            $rus = mysql_real_escape_string($_POST['new']['russian'], $mysql);
            $aze = mysql_real_escape_string($_POST['new']['azerbaijan'], $mysql);
            $tur = mysql_real_escape_string($_POST['new']['turkey'], $mysql);

            mysql_query(
                "
                UPDATE `news`
                SET `news` = '" . $eng . "',
                `rus_news` = '" . $rus . "',
                `aze_news` = '" . $aze . "',
                `tur_news` = '" . $tur . "'
                WHERE `id` = " . intval($_GET['news']),
                $mysql
            );

            if ($err = mysql_error($mysql)) {
                $template->assign('error', 'Ошибка: ' . $err);
            } else {
                $template->assign('message', 'Новость изменена');
            }
        }

        $q = mysql_query('
            SELECT *, ' . Language::getInstance()->buildNewsQuery() . '
            FROM `news`
            WHERE `id` = ' . intval($_GET['news'])
        );
        $news = mysql_fetch_assoc($q);
        $template->assign('news', $news);

        break;


    case 'add_news':
        $template->setTemplate('apanel/news/add.tpl');

        $langpacks = Language::getInstance()->getLangpacks();
        $template->assign('langpacks', $langpacks);

        if ($_POST) {
            foreach ($_POST['new'] as $k => $v) {
                if ($v == '') {
                    $template->assign('error', $k . ': введите текст новости.');
                    $template->send();
                }
            }

            $eng = mysql_real_escape_string($_POST['new']['english'], $mysql);
            $rus = mysql_real_escape_string($_POST['new']['russian'], $mysql);
            $aze = mysql_real_escape_string($_POST['new']['azerbaijan'], $mysql);
            $tur = mysql_real_escape_string($_POST['new']['turkey'], $mysql);

            mysql_query(
                "
                INSERT INTO `news` (
                    `news`, `rus_news`, `aze_news`, `tur_news`, `time`
                ) VALUES (
                    '" . $eng . "', '" . $rus . "', '" . $aze . "', '" . $tur . "', " . $_SERVER['REQUEST_TIME'] . "
                )",
                $mysql
            );

            if ($err = mysql_error($mysql)) {
                $template->assign('error', 'Ошибка: ' . $err);
            } else {
                $template->assign('message', 'Новость добавлена');
            }
        }
        break;


    case 'scan':
        $template->setTemplate('apanel/scan.tpl');

        $scan = $setup['path'];

        if (isset($_GET['id'])) {
            $info = mysql_fetch_assoc(
                mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id . ' AND `dir` = "1"', $mysql)
            );

            if (!$info || is_dir($info['path']) === false) {
                $template->assign('error', 'Такой категории не существует');
                $template->send();
            } else {
                $scan = $info['path'];
            }
        }

        @set_time_limit(99999);
        @ini_set('max_execution_time', 99999);
        @ignore_user_abort(true);

        ini_set('memory_limit', '256M');


        $data = scanner($scan);
        scannerCount();

        if ($data['errors']) {
            $template->assign('error', implode("\n", $data['errors']));
        }

        $template->assign('data', $data);
        break;


    case 'id3_file':
        $template->setTemplate('apanel/id3_file.tpl');

        include 'moduls/PEAR/MP3/Id.php';
        include 'moduls/inc/mp3.class.php';
        $id3 = new MP3_Id();

        $genres = $id3->genres();
        $template->assign('genres', $genres);


        $tmp = mysql_fetch_row(mysql_query('SELECT `path` FROM `files` WHERE `id`=' . $id, $mysql));

        $id3->read($tmp[0]);

        $name = str_to_utf8($id3->name);
        $artists = str_to_utf8($id3->artists);
        $album = str_to_utf8($id3->album);
        $year = str_to_utf8($id3->year);
        $track = str_to_utf8($id3->track);
        $genre = str_to_utf8($id3->genre);
        $comment = str_to_utf8($id3->comment);

        $template->assign('name', $name);
        $template->assign('artists', $artists);
        $template->assign('album', $album);
        $template->assign('year', $year);
        $template->assign('track', $track);
        $template->assign('genre', $genre);
        $template->assign('comment', $comment);


        if ($_POST) {
            @unlink(dirname(__FILE__) . '/../moduls/cache/' . $id . '.dat');

            $name = mb_convert_encoding($_POST['name'], 'windows-1251', 'utf-8');
            $artist = mb_convert_encoding($_POST['artists'], 'windows-1251', 'utf-8');
            $album = mb_convert_encoding($_POST['album'], 'windows-1251', 'utf-8');
            $year = mb_convert_encoding($_POST['year'], 'windows-1251', 'utf-8');
            $track = mb_convert_encoding($_POST['track'], 'windows-1251', 'utf-8');
            $genre = mb_convert_encoding($_POST['genre'], 'windows-1251', 'utf-8');
            $comment = mb_convert_encoding($_POST['comment'], 'windows-1251', 'utf-8');


            // Записываем Idv2 теги
            $mp3 = new mp3($tmp[0]);
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
            if (PEAR::isError($result) == false) {
                $template->assign('message', 'MP3 теги изменены');
            } else {
                $template->assign('error', 'MP3 теги не изменены');
            }
        }
        break;


    case 'id3':
        $template->setTemplate('apanel/id3.tpl');

        include 'moduls/PEAR/MP3/Id.php';
        include 'moduls/inc/mp3.class.php';
        $id3 = new MP3_Id();

        $genres = $id3->genres();
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
            $cacheDir = dirname(__FILE__) . '/../moduls/cache';
            $q = mysql_query('SELECT `path`, `id` FROM `files` WHERE `dir` = "0" AND `path` LIKE("%.mp3")', $mysql);
            while ($f = mysql_fetch_assoc($q)) {
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
                if (
                    mysql_query(
                        'REPLACE INTO setting(name, value) VALUES("marker", "' . intval($_POST['marker']) . '")',
                        $mysql
                    )
                    && mysql_query(
                        'REPLACE INTO setting(name, value) VALUES("marker_where", "' . (
                        $_POST['marker_where'] == 'top' ? 'top' : 'foot') . '")',
                        $mysql
                    )
                ) {
                    $template->assign('message', 'Настройки маркера изменены');
                } else {
                    $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
                }
            } else {
                $q = mysql_query(
                    'SELECT `path` FROM `files` WHERE `path` LIKE "%.jpg" OR `path` LIKE "%.jpe" OR `path` LIKE "%.jpeg" OR `path` LIKE "%.gif" OR `path` LIKE "%.png"',
                    $mysql
                );
                $all = mysql_num_rows($q);
                $i = 0;
                $textLen = mb_strlen($_POST['text']);
                $rgb = hex2rgb($_POST['color']);

                while ($arr = mysql_fetch_row($q)) {
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
                            'moduls/font.ttf',
                            $_POST['text']
                        );

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
                $template->assign('message', 'Всего картинок: ' . $all . ', промаркированы: ' . $i);
            }
        }
        break;


    case 'import':
        $template->setTemplate('apanel/import.tpl');

        $template->assign('dirs', getAllDirs());


        if ($_POST) {
            $message = array();
            $error = array();

            $newpath = trim($_POST['topath']);
            if ($newpath == '') {
                $template->assign('error', 'Нет конечного пути!');
                $template->send();
            }
            if (!is_writable($newpath)) {
                $template->assign('error', 'Директория ' . $newpath . ' недоступна для записи');
                $template->send();
            }

            $text = explode("\n", $_POST['files']);
            $a = sizeof($text);
            for ($i = 0; $i < $a; ++$i) {
                $parametr = explode('#', trim($text[$i]));
                if (!isset($parametr[1])) {
                    $parametr[1] = basename(trim($parametr[0]));
                }
                $to = $newpath . trim($parametr[1]);

                if (!checkExt(pathinfo(trim($parametr[0]), PATHINFO_EXTENSION))) {
                    $error[] = 'Закачка файла ' . $parametr[0] . ' окончилась неудачно: недоступное расширение';
                    continue;
                }
                if (file_exists($to)) {
                    $error[] = 'Файл ' . $to . ' уже существует';
                    continue;
                }

                ini_set('user_agent', $_SERVER['HTTP_USER_AGENT']);
                if (copy(trim($parametr[0]), $to)) {
                    $aze_name = $tur_name = $rus_name = $name = basename($to, '.' . pathinfo($to, PATHINFO_EXTENSION));

                    $infolder = dirname($to) . '/';
                    mysql_query(
                        "
                    INSERT INTO `files` (
                        `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`
                    ) VALUES (
                        '" . mysql_real_escape_string($to, $mysql) . "',
                        '" . mysql_real_escape_string($name, $mysql) . "',
                        '" . mysql_real_escape_string($rus_name, $mysql) . "',
                        '" . mysql_real_escape_string($aze_name, $mysql) . "',
                        '" . mysql_real_escape_string($tur_name, $mysql) . "',
                        '" . mysql_real_escape_string($infolder, $mysql) . "',
                        " . filesize($to) . ",
                        " . filectime($to) . "
                    )",
                        $mysql
                    );
                    dir_count($infolder, true);

                    $message[] = 'Импорт файла ' . $parametr[1] . ' удался';
                } else {
                    $err = error_get_last();
                    $error[] = 'Импорт файла ' . $parametr[1] . ' не удался: ' . $err['message'];
                }
            }
            chmod($newpath, 0777);

            if ($message) {
                $template->assign('message', implode("\n", $message));
            }
            if ($error) {
                $template->assign('error', implode("\n", $error));
            }
        }
        break;


    case 'upload':
        $template->setTemplate('apanel/upload.tpl');

        $template->assign('dirs', getAllDirs());


        if ($_POST) {
            $message = array();
            $error = array();

            $newpath = trim($_POST['topath']);
            if ($newpath == '') {
                $template->assign('error', 'Нет конечного пути');
                $template->send();
            }
            if (!is_writable($newpath)) {
                $template->assign('error', 'Директория ' . $newpath . ' недоступна для записи');
                $template->send();
            }

            $a = sizeof($_FILES['userfile']['name']);
            for ($i = 0; $i < $a; ++$i) {
                if (empty($_FILES['userfile']['name'][$i])) {
                    continue;
                }
                $name = $_FILES['userfile']['name'][$i];
                $to = $newpath . $name;

                if (!checkExt(pathinfo($name, PATHINFO_EXTENSION))) {
                    $error[] = 'Закачка файла ' . $name . ' окончилась неудачно: недоступное расширение';
                    continue;
                }
                if (file_exists($to)) {
                    $error[] = 'Файл ' . $to . ' уже существует';
                    continue;
                }

                if (move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $to)) {
                    $aze_name = $tur_name = $rus_name = $name = basename($to, '.' . pathinfo($to, PATHINFO_EXTENSION));
                    $infolder = dirname($to) . '/';


                    $files = $dbFiles = array();

                    mysql_query(
                        "
                    INSERT INTO `files` (
                        `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`, `attach`
                    ) VALUES (
                        '0',
                        '" . mysql_real_escape_string($to, $mysql) . "',
                        '" . mysql_real_escape_string($name, $mysql) . "',
                        '" . mysql_real_escape_string($rus_name, $mysql) . "',
                        '" . mysql_real_escape_string($aze_name, $mysql) . "',
                        '" . mysql_real_escape_string($tur_name, $mysql) . "',
                        '" . mysql_real_escape_string($infolder, $mysql) . "' ,
                        " . filesize($to) . ",
                        " . filectime($to) . ",
                        " . ($dbFiles ? "'" . mysql_real_escape_string(serialize($dbFiles), $mysql) . "'" : 'NULL') . "
                    );
                    ",
                        $mysql
                    );
                    $id = mysql_insert_id($mysql);
                    if ($files) {
                        add_attach($newpath, $id, $files);
                    }

                    dir_count($infolder, true);

                    chmod($to, 0644);

                    $message[] = 'Закачка файла ' . $name . ' прошла успешно';
                } else {
                    $error[] = 'Закачка файла ' . $name . ' окончилась неудачно';
                }
            }
            chmod($newpath, 0777);

            if ($message) {
                $template->assign('message', implode("\n", $message));
            }
            if ($error) {
                $template->assign('error', implode("\n", $error));
            }
        }
        break;


    case 'service':
        $template->setTemplate('apanel/service.tpl');

        $users =  mysql_result(mysql_query('SELECT COUNT(1) FROM `users_profiles`', $mysql), 0);
        $template->assign('users', $users);

        if ($_POST) {
            switch ($_GET['mode']) {
                case 'del':
                    $user = intval($_POST['user']);
                    if (
                        mysql_query('DELETE FROM `users_profiles` WHERE `id` = ' . $user, $mysql)
                        && mysql_query('DELETE FROM `users_settings` WHERE `parent_id` = ' . $user, $mysql)
                    ) {
                        $template->assign('message', 'Пользователь удален');
                    } else {
                        $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
                    }
                    break;

                default:
                    if (
                        mysql_query(
                            'REPLACE INTO setting(name, value) VALUES("service_head", "' . abs($_POST['service_head']) . '")',
                            $mysql
                        )
                        && mysql_query(
                            'REPLACE INTO setting(name, value) VALUES("service_foot", "' . abs($_POST['service_foot']) . '")',
                            $mysql
                        )
                    ) {
                        $template->assign('message', 'Настройки сервиса изменены');
                    } else {
                        $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
                    }
                    break;
            }
        }
        break;


    case 'exchanger':
        $template->setTemplate('apanel/exchanger.tpl');

        if ($_POST) {
            $exchanger_notice = $_POST['exchanger_notice'] ? 1 : 0;
            $exchanger_hidden = $_POST['exchanger_hidden'] ? 1 : 0;
            $exchanger_extensions = mysql_real_escape_string($_POST['exchanger_extensions'], $mysql);
            $exchanger_name = mysql_real_escape_string($_POST['exchanger_name'], $mysql);

            if (
                mysql_query(
                    'REPLACE INTO setting(name, value) VALUES("exchanger_notice", "' . $exchanger_notice . '")',
                    $mysql
                )
                && mysql_query(
                    'REPLACE INTO setting(name, value) VALUES("exchanger_extensions", "' . $exchanger_extensions . '")',
                    $mysql
                )
                && mysql_query(
                    'REPLACE INTO setting(name, value) VALUES("exchanger_name", "' . $exchanger_name . '")',
                    $mysql
                )
                && mysql_query(
                    'REPLACE INTO setting(name, value) VALUES("exchanger_hidden", "' . $exchanger_hidden . '")',
                    $mysql
                )
            ) {
                $template->assign('message', 'Настройки обменника изменены');
            } else {
                $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            }
        }
        break;


    case 'lib':
        $template->setTemplate('apanel/lib.tpl');

        if ($_POST) {
            $lib = abs($_POST['lib']);
            $lib_str = abs($_POST['lib_str']);

            if (
                mysql_query('REPLACE INTO setting(name, value) VALUES("lib", "' . $lib . '")', $mysql)
                && mysql_query('REPLACE INTO setting(name, value) VALUES("lib_str", "' . $lib_str . '")', $mysql)
            ) {
                $template->assign('message', 'Настройки библиотеки изменены');
            } else {
                $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            }
        }
        break;


    case 'buy':
        $template->setTemplate('apanel/buy.tpl');

        if ($_POST) {
            if (
                mysql_query(
                    "REPLACE INTO setting(name, value) VALUES('buy', '" . mysql_real_escape_string(
                        $_POST['text'],
                        $mysql
                    ) . "')",
                    $mysql
                )
                && mysql_query(
                    "REPLACE INTO setting(name, value) VALUES('randbuy', '" . ($_POST['randbuy'] ? 1 : 0) . "')",
                    $mysql
                )
                && mysql_query(
                    "REPLACE INTO setting(name, value) VALUES('countbuy', '" . abs($_POST['countbuy']) . "')",
                    $mysql
                )
                && mysql_query(
                    "REPLACE INTO setting(name, value) VALUES('banner', '" . mysql_real_escape_string(
                        $_POST['banner'],
                        $mysql
                    ) . "')",
                    $mysql
                )
                && mysql_query(
                    "REPLACE INTO setting(name, value) VALUES('randbanner', '" . ($_POST['randbanner'] ? 1 : 0) . "')",
                    $mysql
                )
                && mysql_query(
                    "REPLACE INTO setting(name, value) VALUES('countbanner', '" . abs($_POST['countbanner']) . "')",
                    $mysql
                )
            ) {
                $template->assign('message', 'Настройки рекламы сохранены');
            } else {
                $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
            }
        }
        break;


    case 'log':
        $template->setTemplate('apanel/log.tpl');

        $q = mysql_query('SELECT * FROM `loginlog` WHERE `id` > 1 ORDER BY `time` DESC LIMIT 50', $mysql);
        while ($log = mysql_fetch_assoc($q)) {
            $logs[] = $log;
        }

        $template->assign('logs', $logs);
        break;


    case 'sec':
        $template->setTemplate('apanel/sec.tpl');

        if ($_POST) {
            if (!$_POST['pwd'] || md5($_POST['pwd']) != $setup['password']) {
                $template->assign('error', 'Неверный пароль');
                $template->send();
            }

            $_POST['autologin'] = $_POST['autologin'] ? 1 : 0;
            $_POST['delete_dir'] = $_POST['delete_dir'] ? 1 : 0;
            $_POST['delete_file'] = $_POST['delete_file'] ? 1 : 0;
            $_POST['countban'] = intval($_POST['countban']);
            $_POST['timeban'] = intval($_POST['timeban']);


            foreach ($_POST as $key => $value) {
                if ($value == '' && $key != 'password' && $key != 'autologin' && $key != 'delete_dir' && $key != 'delete_file') {
                    $template->assign('error', 'Не заполнено одно из полей');
                    $template->send();
                }
            }
            if ($_POST['password'] != '') {
                $_SESSION['authorise'] = md5($_POST['password']);
                mysql_query(
                    "UPDATE `setting` SET `value` = '" . md5($_POST['password']) . "' WHERE `name` = 'password';",
                    $mysql
                );
            }
            mysql_query(
                "UPDATE `setting` SET `value` = '" . $_POST['countban'] . "' WHERE `name` = 'countban';",
                $mysql
            );
            mysql_query("UPDATE `setting` SET `value` = '" . $_POST['timeban'] . "' WHERE `name` = 'timeban';", $mysql);
            mysql_query(
                "UPDATE `setting` SET `value` = '" . $_POST['autologin'] . "' WHERE `name` = 'autologin';",
                $mysql
            );
            mysql_query(
                "UPDATE `setting` SET `value` = '" . $_POST['delete_file'] . "' WHERE `name` = 'delete_file';",
                $mysql
            );
            mysql_query(
                "UPDATE `setting` SET `value` = '" . $_POST['delete_dir'] . "' WHERE `name` = 'delete_dir';",
                $mysql
            );
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
                    $template->assign('error', 'Error');
                    break;
                }
                mysql_query(
                    "REPLACE INTO `setting`(`name`, `value`) VALUES('" . mysql_real_escape_string($key, $mysql) . "', '"
                        . intval($value) . "');",
                    $mysql
                );
            }
            $template->assign('message', 'Список модулей изменен');
        }
        break;


    case 'setting':
        $template->setTemplate('apanel/setting.tpl');

        if ($_POST) {
            $_POST['site_url'] = str_ireplace('http://', '', $_POST['site_url']);

            foreach ($_POST as $key => $value) {
                if ($value == '') {
                    $template->assign('error', 'Не заполнено одно из полей');
                    break;
                }
                mysql_query(
                    "REPLACE INTO `setting`(`name`,`value`) VALUES('" . mysql_real_escape_string($key, $mysql) . "', '"
                        . mysql_real_escape_string($value, $mysql) . "');",
                    $mysql
                );
            }
            $template->assign('message', 'Настройки сохранены');
        }

        $styles = array();
        foreach (glob(DIR . '/../style/*.css', GLOB_NOESCAPE) as $v) {
            $styles[] = pathinfo($v, PATHINFO_FILENAME);
        }

        $langpacks = Language::getInstance()->getLangpacks();

        $template->assign('styles', $styles);
        $template->assign('langpacks', $langpacks);
        break;


    case 'checkdb':
        $template->setTemplate('apanel/checkdb.tpl');

        $d = 0;
        $r = mysql_query('SELECT `id`, `path` FROM `files`', $mysql);
        while ($row = mysql_fetch_assoc($r)) {
            if (!file_exists($row['path'])) {
                mysql_query('DELETE FROM `files` WHERE `id` = ' . $row['id'], $mysql);
                mysql_query('DELETE FROM `comments` WHERE `file_id` = ' . $row['id'], $mysql);

                dir_count($row['path'], false);

                $d++;
            }
        }

        $template->assign('count', $d);
        break;


    case 'del_news':
        if (mysql_query('DELETE FROM `news` WHERE `id` = ' . intval($_GET['news']), $mysql)) {
            $template->assign('message', 'Новость удалена');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'del_comment_news_comments':
        if (mysql_query('DELETE FROM `news_comments` WHERE `id` = ' . intval($_GET['comment']), $mysql)) {
            $template->assign('message', 'Комментарий удален');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'del_comment_view_comments':
        if (mysql_query('DELETE FROM `comments` WHERE `id` = ' . intval($_GET['comment']), $mysql)) {
            $template->assign('message', 'Комментарий удален');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'clearcomm':
        if (mysql_query('DELETE FROM `comments` WHERE `file_id` = ' . $id, $mysql)) {
            $template->assign('message', 'Комментарии удалены');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'clearrate':
        if (mysql_query('UPDATE `files` SET `ips` = "", `yes` = 0, `no` = 0 WHERE `id` = ' . $id, $mysql)) {
            $template->assign('message', 'Рейтинг сброшен');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'optm':
        $q = mysql_query('SHOW TABLES', $mysql);
        while ($arr = mysql_fetch_row($q)) {
            mysql_query('OPTIMIZE TABLE `' . $arr[0] . '`;', $mysql);
        }
        $template->assign('message', 'Таблицы оптимизированы');
        break;


    case 'clean':
        if (mysql_query('TRUNCATE TABLE `files`;', $mysql) && mysql_query('TRUNCATE TABLE `comments`;', $mysql)) {
            $template->assign('message', 'Таблицы очищены');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'cleannews':
        if (mysql_query('TRUNCATE TABLE `news`;', $mysql) && mysql_query('TRUNCATE TABLE `news_comments`;', $mysql)) {
            $template->assign('message', 'Таблицы очищены');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'cleancomm':
        if (mysql_query('TRUNCATE TABLE `comments`;', $mysql)) {
            $template->assign('message', 'Все комментарии к файлам удалены');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
        }
        break;


    case 'cleancomm_news':
        if (mysql_query('TRUNCATE TABLE `news_comments`;', $mysql)) {
            $template->assign('message', 'Все комментарии к новостям удалены');
        } else {
            $template->assign('error', 'Ошибка: ' . mysql_error($mysql));
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


$template->send();
