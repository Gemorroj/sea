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



/**
 * Отображение ошибок
 */
function error($str = '')
{
    global $template; // for included files
    $dir = dirname(__FILE__);

    require_once $dir . '/../header.php';

    $template->setTemplate('message.tpl');
    $template->assign('isError', true);
    $template->assign('message', is_array($str) ? $str : array($str));

    if ($template->getVariable('breadcrumbs') instanceof Undefined_Smarty_Variable) {
        $template->assign('breadcrumbs', array());
    }

    $template->send();
    exit;
}

/**
 * Отображение сообщений
 */
function message($str = '')
{
    global $template; // for included files
    $dir = dirname(__FILE__);

    require_once $dir . '/../header.php';

    $template->setTemplate('message.tpl');
    $template->assign('isError', false);
    $template->assign('message', is_array($str) ? $str : array($str));

    if ($template->getVariable('breadcrumbs') instanceof Undefined_Smarty_Variable) {
        $template->assign('breadcrumbs', array());
    }

    $template->send();
    exit;
}


/**
 * Редирект
 */
function redirect($url, $httpCode = 302)
{
    header('Content-type: text/html; charset=utf-8');
    header('Location: ' . $url, true, $httpCode);

    exit('<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Переход</title>
        <meta http-equiv="refresh" content="0; url=' . htmlspecialchars($url) . '" />
    </head>
    <body>
        <div>
            <a href="' . htmlspecialchars($url) . '">Перейти</a>
        </div>
    </body>
</html>');
}


/**
 * @param string $file
 * @param string $screen
 *
 * @return array
 */
function addScreen($file, $screen)
{
    $error = array();
    $message = array();

    $file = strstr($file, '/'); // убираем папку с загрузками
    $to = Config::get('spath') . $file . '.gif'; // имя конечного файла
    $thumb = Config::get('spath') . $file . '.thumb.gif'; // имя конечного файла

    $ext = strtolower(pathinfo($screen, PATHINFO_EXTENSION));

    if ($ext !== 'gif' && $ext !== 'jpg' && $ext !== 'jpe' && $ext !== 'jpeg' && $ext !== 'png') {
        $error[] = 'Поддерживаются скриншоты только gif, jpeg и png форматов';
    }

    Helper::touch($to);

    if (copy($screen, $to) === true) {
        if ($ext === 'jpg' || $ext === 'jpe' || $ext === 'jpeg') {
            $im = imagecreatefromjpeg($to);
            imagegif($im, $to);
            imagedestroy($im);
        } elseif ($ext === 'png') {
            $im = imagecreatefrompng($to);
            imagegif($im, $to);
            imagedestroy($im);
        }
        Image::resize($to, $thumb, 0, 0, Config::get('marker'));

        $message[] = 'Скриншот ' . $to . ' добавлен';
    } else {
        $err = error_get_last();
        $error[] = $err['message'];
    }

    return array('message' => $message, 'error' => $error);
}


/**
 * @param string $file
 * @param string $aboutStr
 *
 * @return array
 */
function addAbout($file, $aboutStr)
{
    $error = array();
    $message = array();

    $aboutStr = trim($aboutStr);
    $to = Config::get('opath') . strstr($file, '/') . '.txt'; // имя конечного файла

    Helper::touch($to);

    if ($aboutStr == '') {
        if (@unlink($to) === true) {
            $message[] = 'Описание ' . $to . ' удалено';
        } else {
            $err = error_get_last();
            $error[] = $err['message'];
        }
    } else {
        if (file_put_contents($to, $aboutStr) > 0) {
            $message[] = 'Описание ' . $to . ' изменено';
        } else {
            $err = error_get_last();
            $error[] = $err['message'];
        }
    }

    return array('message' => $message, 'error' => $error);
}


/**
 * @param string $file
 * @param int    $id
 * @param string $attachFile
 * @param array  $attachedArray
 *
 * @return array
 */
function addAttach($file, $id, $attachFile, $attachedArray = array())
{
    $error = array();
    $message = array();

    if (Helper::isBlockedExt(pathinfo($attachFile, PATHINFO_EXTENSION))) {
        $error[] = 'Недоступное расширение вложения';
    }

    if (!$error) {
        $key = sizeof($attachedArray);
        $to = Config::get('apath') . dirname(strstr($file, '/'));

        list(, $name) = explode('_', basename($attachFile), 2);

        if (copy($attachFile, $to . '/' . $id . '_' . $key . '_' . $name) === true) {
            $attachedArray[$key] = $name;
            $q = Db_Mysql::getInstance()->prepare('UPDATE `files` SET `attach` = ? WHERE `id` = ?');
            $result = $q->execute(array(serialize($attachedArray), $id));
            if ($result === true) {
                $message[] = 'Вложение ' . $to . '/' . $id . '_' . $key . '_' . $name . ' добавлено';
            } else {
                $error[] = implode("\n", $q->errorInfo());
            }
        } else {
            $err = error_get_last();
            $error[] = $err['message'];
        }
    }

    return array('message' => $message, 'error' => $error);
}


/**
 * @param string $realname
 * @param string $topath
 * @param string $name
 * @param string $rus_name
 * @param string $aze_name
 * @param string $tur_name
 *
 * @return array
 */
function addDir($realname, $topath, $name, $rus_name, $aze_name, $tur_name)
{
    $message = array();
    $error = array();

    if (!preg_match('/^[A-Z0-9_\-]+$/i', $realname)) {
        $error[] = 'Не указано имя директории или оно содержит недопустимые символы. Разрешены [A-Za-z0-9_-]';
    }
    if ($name == '') {
        $error[] = 'english: Укажите название директории';
    }
    if ($rus_name == '') {
        $error[] = 'russian: Укажите название директории';
    }
    if ($aze_name == '') {
        $error[] = 'azerbaijan: Укажите название директории';
    }
    if ($tur_name == '') {
        $error[] = 'turkey: Укажите название директории';
    }
    if ($topath == '') {
        $error[] = 'Укажите родительскую директорию';
    }
    if (is_writable($topath) === false) {
        $error[] = 'Директория ' . $topath . ' недоступна для записи';
    }

    if (!$error) {
        $directory = $topath . $realname . '/';

        $temp = strstr($directory, '/');

        //скриншоты
        $screen = Config::get('spath') . $temp;
        // описания
        $desc = Config::get('opath') . $temp;
        // вложения
        $attach = Config::get('apath') . $temp;

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
        $q = Db_Mysql::getInstance()->prepare('
            INSERT INTO `files` (
                `dir`, `dir_count`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `timeupload`
            ) VALUES (?, ? , ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP())
        ');
        $result = $q->execute(array(
            '1',
            '0',
            $directory,
            $name,
            $rus_name,
            $aze_name,
            $tur_name,
            $topath
        ));
        if ($result) {
            dir_count($topath, true);
            $message[] = 'Директория ' . $directory . ' создана';
        } else {
            $error[] = implode("\n", $q->errorInfo());
        }
    }

    return array('message' => $message, 'error' => $error);
}


/**
 * @param string $newpath
 *
 * @return array
 */
function uploadUrls($newpath)
{
    $db = Db_Mysql::getInstance();
    $message = array();
    $error = array();

    ini_set('user_agent', $_SERVER['HTTP_USER_AGENT']);
    $q = $db->prepare('
        INSERT INTO `files` (
            `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`
        ) VALUES (
            "0", ?, ?, ?, ?, ?, ?, ?, ?
        )
    ');

    foreach (explode("\n", trim($_POST['files'])) as $text) {
        $parameter = explode('#', trim($text));
        $parameter[0] = trim($parameter[0]);
        if (isset($parameter[1]) === false) {
            $parameter[1] = basename($parameter[0]);
        } else {
            $parameter[1] = trim($parameter[1]);
        }
        $to = $newpath . $parameter[1];

        if (Helper::isBlockedExt(pathinfo($parameter[0], PATHINFO_EXTENSION))) {
            $error[] = 'Загрузка файла ' . $parameter[0] . ' окончилась неудачно: недоступное расширение';
            continue;
        }
        if (file_exists($to) === true) {
            $error[] = 'Загрузка файла ' . $parameter[0] . ' окончилась неудачно: файл ' . $to . ' уже существует';
            continue;
        }

        if (copy($parameter[0], $to) === true) {
            $aze_name = $tur_name = $rus_name = $name = basename($to, '.' . pathinfo($to, PATHINFO_EXTENSION));

            $infolder = dirname($to) . '/';

            $q->execute(array(
                 $to,
                 $name,
                 $rus_name,
                 $aze_name,
                 $tur_name,
                 $infolder,
                 filesize($to),
                 filectime($to)
            ));
            dir_count($infolder, true);
            chmod($to, 0644);
            $message[] = 'Загрузка файла ' . $parameter[0] . ' прошла успешно';
        } else {
            $err = error_get_last();
            $error[] = 'Загрузка файла ' . $parameter[0] . ' окончилась неудачно: ' . $err['message'];
        }
    }

    return array('message' => $message, 'error' => $error);
}


/**
 * @param string $newpath
 *
 * @return array
 */
function uploadFiles($newpath)
{
    $db = Db_Mysql::getInstance();
    $message = array();
    $error = array();

    $q = $db->prepare('
        INSERT INTO `files` (
            `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`
        ) VALUES (
            "0", ?, ?, ?, ?, ?, ?, ?, ?
        )
    ');

    $infolder = rtrim($newpath, '/') . '/';

    for ($i = 0, $l = sizeof($_FILES['userfile']['name']); $i < $l; ++$i) {
        if (empty($_FILES['userfile']['name'][$i]) === true) {
            continue;
        }
        $name = $_FILES['userfile']['name'][$i];
        $to = $newpath . $name;

        if (Helper::isBlockedExt(pathinfo($name, PATHINFO_EXTENSION))) {
            $error[] = 'Загрузка файла ' . $name . ' окончилась неудачно: недоступное расширение';
            continue;
        }
        if (file_exists($to) === true) {
            $error[] = 'Загрузка файла ' . $name . ' окончилась неудачно: файл ' . $to . ' уже существует';
            continue;
        }

        if (move_uploaded_file($_FILES['userfile']['tmp_name'][$i], $to) === true) {
            $aze_name = $tur_name = $rus_name = $name = basename($to, '.' . pathinfo($to, PATHINFO_EXTENSION));
            // транслит
            if ($name[0] === '!') {
                $aze_name = $tur_name = $rus_name = $name = substr($name, 1);
                $rus_name = Translit::trans($rus_name);
            }

            $q->execute(array(
                 $to,
                 $name,
                 $rus_name,
                 $aze_name,
                 $tur_name,
                 $infolder,
                 filesize($to),
                 filectime($to)
            ));

            chmod($to, 0644);
            $message[] = 'Загрузка файла ' . $name . ' прошла успешно';
        } else {
            $err = error_get_last();
            $error[] = 'Загрузка файла ' . $name . ' окончилась неудачно: ' . $err['message'];
        }
    }
    dir_count($newpath, true);

    return array('message' => $message, 'error' => $error);
}


/**
 * Удаление вложений
 *
 * @param string $folder
 * @param int    $id
 * @param array  $files
 *
 * @return bool
 */
function del_attach($folder, $id, $files)
{
    $attach = Config::get('apath') . strstr($folder, '/') . '/';
    foreach ($files as $k => $v) {
        unlink($attach . $id . '_' . $k . '_' . $v);
    }

    return true;
}


/**
 * Обновление данных файлов в БД
 *
 * @param string $path
 * @param string $name
 * @param string $rus_name
 * @param string $aze_name
 * @param string $tur_name
 * @param bool   $dir
 * @param bool   $insert
 *
 * @return PDOStatement
 */
function _scannerDb($path, $name, $rus_name, $aze_name, $tur_name, $dir = true, $insert = true)
{
    static $preparedQueryInsert = null;
    static $preparedQueryUpdate = null;

    if ($preparedQueryInsert === null) {
        $preparedQueryInsert = Db_Mysql::getInstance()->prepare('
            INSERT INTO `files` (
                `dir`, `size`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `timeupload`
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ');
    }
    if ($preparedQueryUpdate === null) {
        $preparedQueryUpdate = Db_Mysql::getInstance()->prepare('
            UPDATE `files`
            SET `name` = IF(`name` <> "", `name`, ?),
            `rus_name` = IF(`rus_name` <> "", `rus_name`, ?),
            `aze_name` = IF(`aze_name` <> "", `aze_name`, ?),
            `tur_name` = IF(`tur_name` <> "", `tur_name`, ?)
            WHERE `path` = ?
        ');
    }

    if ($insert) {
        if ($dir) {
            $preparedQueryInsert->bindValue(1, '1');
            $preparedQueryInsert->bindValue(2, 0);
        } else {
            $preparedQueryInsert->bindValue(1, '0');
            $preparedQueryInsert->bindValue(2, filesize($path));
        }
        $preparedQueryInsert->bindValue(3, $path);
        $preparedQueryInsert->bindValue(4, $name);
        $preparedQueryInsert->bindValue(5, $rus_name);
        $preparedQueryInsert->bindValue(6, $aze_name);
        $preparedQueryInsert->bindValue(7, $tur_name);
        $preparedQueryInsert->bindValue(8, dirname($path) . '/');
        $preparedQueryInsert->bindValue(9, filectime($path));

        return $preparedQueryInsert;
    } else {
        $preparedQueryUpdate->bindValue(1, $name);
        $preparedQueryUpdate->bindValue(2, $rus_name);
        $preparedQueryUpdate->bindValue(3, $aze_name);
        $preparedQueryUpdate->bindValue(4, $tur_name);
        $preparedQueryUpdate->bindValue(5, $path);

        return $preparedQueryUpdate;
    }
}


/**
 * Обновление данных загрузок в БД
 *
 * @param string $path
 * @param string $cont
 *
 * @return array
 */
function scanner($path = '', $cont = 'folder.png')
{
    static $folders = 0;
    static $files = 0;
    static $errors = array();
    static $preparedQuery = null;

    if ($preparedQuery === null) {
        $preparedQuery = Db_Mysql::getInstance()->prepare('SELECT `name`, `rus_name`, `aze_name`, `tur_name` FROM `files` WHERE `path` = ?');
    }


    if (is_readable($path) === false) {
        $errors[] = $path . ': не доступно для чтения. Вероятно, не хватает прав.';
        return array('folders' => $folders, 'files' => $files, 'errors' => $errors);
    }

    chmod($path, 0777);

    foreach (array_diff(scandir($path, 0), array('.', '..')) as $file) {
        if ($file[0] === '.') {
            continue;
        }

        $f = str_replace('//', '/', $path . '/' . $file);
        $pathinfo = pathinfo($f);

        $is_dir = is_dir($f);
        if ($is_dir === true) {
            $f .= '/';
        }
        $is_file = ($is_dir === false && $pathinfo['basename'] != $cont && is_file($f) === true);

        if (!$preparedQuery->execute(array($f))) {
            $errors[] = implode("\n", $preparedQuery->errorInfo());
            continue;
        }

        $insert = true;
        if ($preparedQuery->rowCount() > 0) {
            $insert = false;
            $row = $preparedQuery->fetch();
            if ($row['name'] != '' && $row['rus_name'] != '' && $row['aze_name'] != '' && $row['tur_name'] != '') {
                if ($is_dir === true) {
                    $folders++;
                    scanner($f);
                } elseif ($is_file === true) {
                    $files++;
                }
                continue;
            }
        }



        $aze_name = $tur_name = $rus_name = $name = ($is_dir === true ? $pathinfo['basename'] : $pathinfo['filename']);
        if ($name == '') {
            $tmpErr = error_get_last();
            $errors[] = $tmpErr['message'];
            continue;
        }

        // транслит
        if ($name[0] === '!') {
            $aze_name = $tur_name = $rus_name = $name = substr($name, 1);
            $rus_name = Translit::trans($rus_name);
        }

        if ($is_dir === true) {
            // скриншоты
            $screen = Config::get('spath') . mb_substr($f, mb_strlen(Config::get('path')));
            if (file_exists($screen) === false) {
                mkdir($screen, 0777);
            }
            chmod($screen, 0777);

            // описания
            $desc = Config::get('opath') . mb_substr($f, mb_strlen(Config::get('path')));
            if (file_exists($desc) === false) {
                mkdir($desc, 0777);
            }
            chmod($desc, 0777);

            // вложения
            $attach = Config::get('apath') . mb_substr($f, mb_strlen(Config::get('path')));
            if (file_exists($attach) === false) {
                mkdir($attach, 0777);
            }
            chmod($attach, 0777);

            $q = _scannerDb($f, $name, $rus_name, $aze_name, $tur_name, true, $insert);
            if (!$q->execute()) {
                $errors[] = implode("\n", $q->errorInfo());
            }

            $folders++;
            scanner($f);
        } elseif ($is_file === true) {
            $files++;
            $q = _scannerDb($f, $name, $rus_name, $aze_name, $tur_name, false, $insert);
            if (!$q->execute()) {
                $errors[] = implode("\n", $q->errorInfo());
            }
        }
    }

    return array('folders' => $folders, 'files' => $files, 'errors' => $errors);
}


/**
 * Обновление количества файлов в директориях
 */
function scannerCount()
{
    $db = Db_Mysql::getInstance();

    $q1 = $db->prepare('SELECT COUNT(1) FROM `files` WHERE `infolder` LIKE ? AND `hidden` = "0"');
    $q2 = $db->prepare('UPDATE `files` SET `dir_count` = ? WHERE `path` = ?');

    foreach ($db->query('SELECT `path` FROM `files` WHERE `dir` = "1" GROUP BY `path`') as $dir) {
        $q1->execute(array($db->escapeLike($dir['path']) . '%'));
        $count = $q1->fetchColumn();

        $q2->execute(array($count, $dir['path']));
    }
}


/**
 * Массив всех директорий
 *
 * @return array
 */
function getAllDirs()
{
    $dirs = array('/' => '/');

    foreach (Db_Mysql::getInstance()->query('SELECT SUBSTR(`path`, ' . (strlen(Config::get('path')) + 1) . ') AS `path` FROM `files` WHERE `dir` = "1"') as $item) {
        $dirs[$item['path']] = $item['path'];
    }

    return $dirs;
}


/**
 * Изменение количества файлов в директориях
 *
 * @param string $path      директория
 * @param bool   $increment инкремент или декремент
 *
 * @return bool
 */
function dir_count($path = '', $increment = true)
{
    $in = array();
    $arr = explode('/', $path);
    $all = sizeof($arr);
    for ($i = 0; $i < $all; ++$i) {
        if ($i > 0) {
            $in[$i] = $in[$i - 1] . $arr[$i] . '/';
        } else {
            $in[$i] = $arr[$i] . '/';
        }
    }

    return Db_Mysql::getInstance()->prepare('
        UPDATE `files`
        SET `dir_count` = `dir_count` ' . ($increment ? '+' : '-') . ' 1
        WHERE `path` IN (' . rtrim(str_repeat('?,', $all), ',') . ')
    ')->execute($in);
}


/**
 * Получаем данные из тем
 *
 * @param int $id
 * @param string $path
 * @return array
 */
function getThmInfo($id, $path = '')
{
    if (file_exists(CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
        return unserialize(file_get_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat'));
    }

    $ver_thm = array(
        1 => 'T68, T230, T290, T300, T310',
        '1.0' => 'T68, T230, T290, T300, T310',
        '1.1' => 'T68, T230, T290, T300, T310',
        '1.2' => 'T68, T230, T290, T300, T310',
        '1.3' => 'T68, T230, T290, T300, T310',
        '1.4' => 'T68, T230, T290, T300, T310',
        '1.5' => 'T68, T230, T290, T300, T310',
        '1.6' => 'T68, T230, T290, T300, T310',
        '1.7' => 'T68, T230, T290, T300, T310',
        '1.8' => 'T68, T230, T290, T300, T310',
        '1.9' => 'T68, T230, T290, T300, T310',
        2 => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.0' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.1' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.2' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.3' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.4' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.5' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.6' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.7' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.8' => 'J210, J220, J230, T610, T630, Z600, Z300',
        '2.9' => 'J210, J220, J230, T610, T630, Z600, Z300',
        3 => 'J300, K300, K500, K700, S700, Z1010',
        '3.0' => 'J300, K300, K500, K700, S700, Z1010',
        '3.1' => 'V800, Z800',
        '3.2' => 'V800, Z800',
        4 => 'K600, K750, W700, W800, Z520, Z525',
        '4.0' => 'K600, K750, W700, W800, Z520, Z525',
        '4.1' => 'K310, K320, K510,W200, W300, Z530, W550, W600, W810, Z550, Z558, W900',
        '4.5' => 'Z250, Z310, Z320, K550, K610, Z610, Z710, W610, W660, W710, K790, K800, K810, S500, W580, W830, W850, T650, K770, W880',
        '4.6' => 'K630, K660, K850, R300, R306, V640, W760, W890, W910, Z750',
        '4.7' => 'C702, C902, W760, W980, Z780',
        'UIQ3' => 'M600, P1, W950, W960, P990',
    );

    $thm = new Archive_Tar($path);


    if (!$file = $thm->extractInString(pathinfo($path, PATHINFO_FILENAME) . '.xml')) {
        $file = $thm->extractInString('Theme.xml');
    }

    if (!$file) {
        $list = $thm->listContent();
        $all = sizeof($list);
        for ($i = 0; $i < $all; ++$i) {
            if (pathinfo($list[$i]['filename'], PATHINFO_EXTENSION) === 'xml') {
                $file = $thm->extractInString($list[$i]['filename']);
                break;
            }
        }
    }


    // fix bug in Tar.php
    if (!$file) {
        preg_match('/<\?\s*xml\s*version\s*=\s*"1\.0"\s*\?>(.*)<\/.+>/isU', file_get_contents($path), $arr);
        $file = trim($arr[0]);
    }


    $load = simplexml_load_string($file);

    $out = array('author' => '', 'version' => '', 'models' => '');
    if ($load->Author_organization['Value']) {
        $out['author'] = (string)$load->Author_organization['Value'];
    }

    if ($load['version']) {
        $out['version'] = (string)$load['version'];

        if (in_array($load['version'], array_keys($ver_thm))) {
            $out['models'] = $ver_thm[(string)$load['version']];
        }
    }

    file_put_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($out));
    return $out;
}


/**
 * Иконки из JAR файлов
 */
function jar_ico($jar, $f)
{
    $icon = array();
    $archive = new PclZip($jar);

    $list = $archive->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);


    if (@$list[0]['content']) {
        if (!$icon) {
            preg_match('/MIDlet\-Icon:[\s*](.*)/iux', $list[0]['content'], $arr);

            if (@$arr[1]) {
                foreach (explode(',', $arr[1]) as $v) {
                    $v = trim(trim($v), '/');
                    if (strtolower(pathinfo($v, PATHINFO_EXTENSION)) === 'png') {
                        $icon = $archive->extract(PCLZIP_OPT_BY_NAME, $v, PCLZIP_OPT_EXTRACT_AS_STRING);
                        break;
                    }
                }
            }
        }

        if (!$icon) {
            preg_match('/MIDlet\-1:[\s*](.*)/iux', $list[0]['content'], $arr);

            if (@$arr[1]) {
                foreach (explode(',', $arr[1]) as $v) {
                    $v = trim(trim($v), '/');
                    if (strtolower(pathinfo($v, PATHINFO_EXTENSION)) === 'png') {
                        $icon = $archive->extract(PCLZIP_OPT_BY_NAME, $v, PCLZIP_OPT_EXTRACT_AS_STRING);
                        break;
                    }
                }
            }
        }
    }


    return (@$icon[0]['content'] && file_put_contents($f, $icon[0]['content']));
}



/**
 * Данные об аудио файле
 *
 * @param int $id
 * @param string $path
 *
 * @return array
 */
function getMusicInfo($id, $path)
{
    if (file_exists(CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
        return unserialize(file_get_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat'));
    }
    $path = CORE_DIRECTORY . '/../' . $path;

    $tmpa = array();
    $filename = pathinfo($path);
    $ext = strtolower($filename['extension']);

    if ($ext === 'mp3' || $ext === 'wav') {
        $audio = new AudioFile;
        $audio->loadFile($path);

        if ($audio->wave_length > 0) {
            $length = $audio->wave_length;
        } else {
            $mp3 = new mp3($path);
            $mp3->setFileInfoExact();
            $length = $mp3->time;
        }
        $comments = array();

        if (isset($audio->id3v2->APIC) && $audio->id3v2->APIC) {
            $apic = $audio->id3v2->APIC;
            $pos = strpos($apic,  "\0") + 1;
            $apic = substr($apic, $pos);
            $pos = strpos($apic,  "\0") + 1;
            $apic = substr($apic, $pos);


            function apicFix($apic)
            {
                // fix 1
                $pos = strpos($apic,  "\0") + 1;
                $apic = substr($apic, $pos);

                $apic = str_replace("\xFF\x00\x00", "\xFF\x00", $apic);
                // end fix 1
                return $apic;
            }

            function apicCheckFix($apic)
            {
                // fix 2
                $tmp = @imagecreatefromstring($apic);
                if ($tmp) {
                    ob_start();
                    imagejpeg($tmp);
                    $apic = ob_get_contents();
                    ob_end_clean();
                    imagedestroy($tmp);
                } else {
                    $apic = false;
                }
                // end fix 2
               return $apic;
            }

            $fixApic = apicCheckFix($apic);
            if (!$fixApic) {
                $fixApic = apicFix($apic);
                if ($fixApic) {
                    $fixApic = apicCheckFix($fixApic);
                }
            }

            $comments['APIC'] = $fixApic;
        } else {
            $comments['APIC'] = false;
        }
        if (isset($audio->id3_title)) {
            $comments['TITLE'] = Helper::str2utf8($audio->id3_title);
        } else {
            $comments['TITLE'] = '';
        }
        if (isset($audio->id3_artist)) {
            $comments['ARTIST'] = Helper::str2utf8($audio->id3_artist);
        } else {
            $comments['ARTIST'] = '';
        }
        if (isset($audio->id3_album)) {
            $comments['ALBUM'] = Helper::str2utf8($audio->id3_album);
        } else {
            $comments['ALBUM'] = '';
        }
        if (isset($audio->id3_year)) {
            $comments['DATE'] = Helper::str2utf8($audio->id3_year);
        } else {
            $comments['DATE'] = '';
        }
        if (isset($audio->id3_genre)) {
            $comments['GENRE'] = Helper::str2utf8($audio->id3_genre);
        } else {
            $comments['GENRE'] = '';
        }
        if (isset($audio->id3_comment)) {
            $comments['COMMENT'] = Helper::str2utf8($audio->id3_comment);
        } else {
            $comments['COMMENT'] = '';
        }

        $tmpa = array(
            'channels' => $audio->wave_channels,
            'sampleRate' => $audio->wave_framerate,
            'avgBitrate' => intval($audio->wave_byterate) * 1024,
            'streamLength' => $length,
            'tag' => array(
                'title' => trim(str_replace(array(chr(0), chr(1)), '', $comments['TITLE'])),
                'artist' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ARTIST'])),
                'album' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ALBUM'])),
                'date' => $comments['DATE'],
                'genre' => $comments['GENRE'],
                'comment' => trim(str_replace(array(chr(0), chr(1)), '', $comments['COMMENT'])),
                'apic' => $comments['APIC']
            )
        );
    } elseif ($ext === 'ogg') {
        try {
            $ogg = new File_Ogg($path);
            $obj = & current($ogg->_streams);
            $comments = array();

            if (isset($obj->_comments['TITLE'])) {
                $comments['TITLE'] = Helper::str2utf8($obj->_comments['TITLE']);
            } else {
                $comments['TITLE'] = '';
            }
            if (isset($obj->_comments['ARTIST'])) {
                $comments['ARTIST'] = Helper::str2utf8($obj->_comments['ARTIST']);
            } else {
                $comments['ARTIST'] = '';
            }
            if (isset($obj->_comments['ALBUM'])) {
                $comments['ALBUM'] = Helper::str2utf8($obj->_comments['ALBUM']);
            } else {
                $comments['ALBUM'] = '';
            }
            if (isset($obj->_comments['DATE'])) {
                $comments['DATE'] = Helper::str2utf8($obj->_comments['DATE']);
            } else {
                $comments['DATE'] = '';
            }
            if (isset($obj->_comments['GENRE'])) {
                $comments['GENRE'] = Helper::str2utf8($obj->_comments['GENRE']);
            } else {
                $comments['GENRE'] = '';
            }
            if (isset($obj->_comments['COMMENT'])) {
                $comments['COMMENT'] = Helper::str2utf8($obj->_comments['COMMENT']);
            } else {
                $comments['COMMENT'] = '';
            }

            $tmpa = array(
                'channels' => $obj->_channels,
                'sampleRate' => $obj->_sampleRate,
                'avgBitrate' => $obj->_avgBitrate,
                'streamLength' => $obj->_streamLength,
                'tag' => array(
                    'title' => trim(str_replace(array(chr(0), chr(1)), '', $comments['TITLE'])),
                    'artist' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ARTIST'])),
                    'album' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ALBUM'])),
                    'date' => $comments['DATE'],
                    'genre' => $comments['GENRE'],
                    'comment' => trim(str_replace(array(chr(0), chr(1)), '', $comments['COMMENT'])),
                    'apic' => false
                )
            );
        } catch (Exception $e) {}
    }

    file_put_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($tmpa));
    return $tmpa;
}


/**
 * Данные об видео файле
 *
 * @param int $id
 * @param string $path
 *
 * @return array
 */
function getVideoInfo($id, $path)
{
    if (file_exists(CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
        return unserialize(file_get_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat'));
    }

    $tmpa = array();
    $mov = new ffmpeg_movie($path, false);
    if ($mov) {
        $tmpa = array(
            'getVideoCodec' => $mov->getVideoCodec(),
            'GetFrameWidth' => $mov->GetFrameWidth(),
            'GetFrameHeight' => $mov->GetFrameHeight(),
            'getDuration' => $mov->getDuration(),
            'getBitRate' => $mov->getBitRate()
        );
        file_put_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($tmpa));
    }

    return $tmpa;
}


/**
 * Данные файла из БД
 *
 * @param int $id
 *
 * @return array
 */
function getFileInfo ($id)
{
    $q = Db_Mysql::getInstance()->prepare('
        SELECT *, ' . Language::buildFilesQuery() . '
        FROM `files`
        WHERE `id` = ?
        ' . (IS_ADMIN !== true ? 'AND `hidden` = "0"' : '')
    );
    $q->execute(array($id));
    return $q->fetch();
}


/**
 * Данные новости из БД
 *
 * @param int $id
 *
 * @return array
 */
function getNewsInfo ($id)
{
    $q = Db_Mysql::getInstance()->prepare('
        SELECT *, ' . Language::buildNewsQuery() . '
        FROM `news`
        WHERE `id` = ?
    ');
    $q->execute(array($id));
    return $q->fetch();
}


/**
 * Обновляем счетчик скачиваний
 *
 * @param int $id
 *
 * @return bool
 */
function updFileLoad ($id)
{
    return Db_Mysql::getInstance()->prepare('
        UPDATE `files`
        SET `loads` = `loads` + 1,
        `timeload` = ?
        WHERE `id` = ?
    ')->execute(array($_SERVER['REQUEST_TIME'], $id));
}
