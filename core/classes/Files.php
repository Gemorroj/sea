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
class Files
{
    /**
     * @param string $file
     * @param string $screen
     *
     * @return array
     */
    public static function addScreen($file, $screen)
    {
        $error = array();
        $message = array();

        $file = strstr($file, '/'); // убираем папку с загрузками
        $to = Config::get('spath') . $file . '.gif'; // имя конечного файла
        $thumb = Config::get('spath') . $file . '.thumb.gif'; // имя конечного файла

        $ext = strtolower(pathinfo($screen, PATHINFO_EXTENSION));

        if (!Media_Image::isSupported($ext)) {
            $error[] = 'Поддерживаются скриншоты только gif, jpeg, png и bmp форматов';
        } else {
            Helper::touch($to);

            if (copy($screen, $to) === true) {
                Image::resize($to, $thumb, 0, 0, Config::get('marker'));
                $message[] = 'Скриншот ' . $to . ' добавлен';
            } else {
                $err = error_get_last();
                $error[] = $err['message'];
            }
        }

        return array('message' => $message, 'error' => $error);
    }


    /**
     * @param string $file
     * @param string $aboutStr
     *
     * @return array
     */
    public static function addAbout($file, $aboutStr)
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
    public static function addAttach($file, $id, $attachFile, $attachedArray = array())
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
    public static function addDir($realname, $topath, $name, $rus_name, $aze_name, $tur_name)
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

            // скриншоты
            mkdir($screen, 0777);

            // описания
            mkdir($desc, 0777);

            // вложения
            mkdir($attach, 0777);


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
                self::updateDirCount($topath, true);
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
    public static function uploadUrls($newpath)
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

        foreach (explode("\n", trim(Http_Request::post('files'))) as $text) {
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
            if (is_file($to) === true) {
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
                self::updateDirCount($infolder, true);
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
    public static function uploadFiles($newpath)
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
        $userfile = Http_Request::file('userfile');

        for ($i = 0, $l = sizeof($userfile['name']); $i < $l; ++$i) {
            if (empty($userfile['name'][$i]) === true) {
                continue;
            }
            $name = $userfile['name'][$i];
            $to = $newpath . $name;

            if (Helper::isBlockedExt(pathinfo($name, PATHINFO_EXTENSION))) {
                $error[] = 'Загрузка файла ' . $name . ' окончилась неудачно: недоступное расширение';
                continue;
            }
            if (is_file($to) === true) {
                $error[] = 'Загрузка файла ' . $name . ' окончилась неудачно: файл ' . $to . ' уже существует';
                continue;
            }

            if (move_uploaded_file($userfile['tmp_name'][$i], $to) === true) {
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
                $message[] = 'Загрузка файла ' . $name . ' прошла успешно';
            } else {
                $err = error_get_last();
                $error[] = 'Загрузка файла ' . $name . ' окончилась неудачно: ' . $err['message'];
            }
        }
        self::updateDirCount($newpath, true);

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
    public static function delAttach($folder, $id, $files)
    {
        $attach = Config::get('apath') . strstr($folder, '/') . '/';
        foreach ($files as $k => $v) {
            unlink($attach . $id . '_' . $k . '_' . $v);
        }

        return true;
    }


    /**
     * Массив всех директорий
     *
     * @return array
     */
    public static function getAllDirs()
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
    public static function updateDirCount($path = '', $increment = true)
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
     * Обновляем счетчик скачиваний
     *
     * @param int $id
     *
     * @return bool
     */
    public static function updateFileLoad ($id)
    {
        return Db_Mysql::getInstance()->prepare('
            UPDATE `files`
            SET `loads` = `loads` + 1,
            `timeload` = ?
            WHERE `id` = ?
        ')->execute(array($_SERVER['REQUEST_TIME'], $id));
    }


    /**
     * Данные файла из БД
     *
     * @param int $id
     *
     * @return array
     */
    public static function getFileInfo ($id)
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
}
