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
 * Транслит с латиницы на русский
 *
 * @param string $t
 *
 * @return string
 */
function trans($t)
{
    $a = array(
        '_',
        'YA',
        'Ya',
        'ya',
        'yee',
        'YO',
        'yo',
        'Yo',
        'ZH',
        'zh',
        'Zh',
        'Z',
        'z',
        'CH',
        'ch',
        'Ch',
        'SH',
        'sh',
        'Sh',
        'YE',
        'ye',
        'Ye',
        'YU',
        'yu',
        'Yu',
        'JA',
        'ja',
        'Ja',
        'A',
        'a',
        'B',
        'b',
        'V',
        'v',
        'G',
        'g',
        'D',
        'd',
        'E',
        'e',
        'I',
        'i',
        'J',
        'j',
        'K',
        'k',
        'L',
        'l',
        'M',
        'm',
        'N',
        'n',
        'O',
        'o',
        'P',
        'p',
        'R',
        'r',
        'S',
        's',
        'T',
        't',
        'U',
        'u',
        'F',
        'f',
        'H',
        'h',
        'W',
        'w',
        'x',
        'q',
        'Y',
        'y',
        'C',
        'c'
    );
    $b = array(
        ' ',
        'Я',
        'Я',
        'я',
        'ые',
        'Ё',
        'ё',
        'Ё',
        'Ж',
        'ж',
        'Ж',
        'З',
        'з',
        'Ч',
        'ч',
        'Ch',
        'Ш',
        'ш',
        'Ш',
        'Э',
        'э',
        'Э',
        'Ю',
        'ю',
        'Ю',
        'Я',
        'я',
        'Я',
        'А',
        'а',
        'Б',
        'б',
        'В',
        'в',
        'Г',
        'г',
        'Д',
        'д',
        'Е',
        'е',
        'И',
        'и',
        'Й',
        'й',
        'К',
        'к',
        'Л',
        'л',
        'М',
        'м',
        'Н',
        'н',
        'О',
        'о',
        'П',
        'п',
        'Р',
        'р',
        'С',
        'с',
        'Т',
        'т',
        'У',
        'у',
        'Ф',
        'ф',
        'Х',
        'х',
        'Щ',
        'щ',
        'ъ',
        'ь',
        'Ы',
        'ы',
        'Ц',
        'ц',
        ''
    );

    return str_replace($a, $b, $t);
}


/**
 * Транслит с русского на латиницу
 *
 * @param string $t
 *
 * @return string
 */
function retrans($t)
{
    $a = array(
        '_',
        'YA',
        'Ya',
        'ya',
        'yee',
        'YO',
        'yo',
        'Yo',
        'ZH',
        'zh',
        'Zh',
        'Z',
        'z',
        'CH',
        'ch',
        'Ch',
        'SH',
        'sh',
        'Sh',
        'YE',
        'ye',
        'Ye',
        'YU',
        'yu',
        'Yu',
        'JA',
        'ja',
        'Ja',
        'A',
        'a',
        'B',
        'b',
        'V',
        'v',
        'G',
        'g',
        'D',
        'd',
        'E',
        'e',
        'I',
        'i',
        'J',
        'j',
        'K',
        'k',
        'L',
        'l',
        'M',
        'm',
        'N',
        'n',
        'O',
        'o',
        'P',
        'p',
        'R',
        'r',
        'S',
        's',
        'T',
        't',
        'U',
        'u',
        'F',
        'f',
        'H',
        'h',
        'W',
        'w',
        'x',
        'q',
        'Y',
        'y',
        'C',
        'c'
    );
    $b = array(
        ' ',
        'Я',
        'Я',
        'я',
        'ые',
        'Ё',
        'ё',
        'Ё',
        'Ж',
        'ж',
        'Ж',
        'З',
        'з',
        'Ч',
        'ч',
        'Ch',
        'Ш',
        'ш',
        'Ш',
        'Э',
        'э',
        'Э',
        'Ю',
        'ю',
        'Ю',
        'Я',
        'я',
        'Я',
        'А',
        'а',
        'Б',
        'б',
        'В',
        'в',
        'Г',
        'г',
        'Д',
        'д',
        'Е',
        'е',
        'И',
        'и',
        'Й',
        'й',
        'К',
        'к',
        'Л',
        'л',
        'М',
        'м',
        'Н',
        'н',
        'О',
        'о',
        'П',
        'п',
        'Р',
        'р',
        'С',
        'с',
        'Т',
        'т',
        'У',
        'у',
        'Ф',
        'ф',
        'Х',
        'х',
        'Щ',
        'щ',
        'ъ',
        'ь',
        'Ы',
        'ы',
        'Ц',
        'ц'
    );

    return str_replace($b, $a, $t);
}


/**
 * $_GET -> $_SESSION
 */
function get2ses($name)
{
    if (!isset($_SESSION[$name])) {
        $_SESSION[$name] = $GLOBALS['setup'][$name];
    }
    if (isset($_GET[$name])) {
        $_SESSION[$name] = $_GET[$name];
    }

    // да, именно переменная переменных
    return $$name = $_SESSION[$name];
}


/**
 * Маркер картинок
 *
 * @param resource $image
 * @param resource $watermark
 *
 * @return resource
 */
function marker($image, $watermark)
{
    if (!is_resource($image) || !is_resource($watermark)) {
        return null;
    }


    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    $tmpW = $watermarkWidth = imagesx($watermark);
    $tmpH = $watermarkHeight = imagesy($watermark);

    if ($imageWidth < $watermarkWidth || $imageHeight < $watermarkHeight) {
        if ($imageWidth < $watermarkWidth) {
            $watermarkWidth = $imageWidth;
            $watermarkHeight *= $watermarkWidth / $tmpW;
        } else {
            $watermarkHeight = $imageHeight / 2;
            $watermarkWidth *= $watermarkHeight / $tmpH;
        }

        $f = imagecreatetruecolor($watermarkWidth, $watermarkHeight);

        $transparencyIndex = imagecolortransparent($watermark);
        $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

        if ($transparencyIndex >= 0) {
            $transparencyColor = imagecolorsforindex($watermark, $transparencyIndex);
        }

        $transparencyIndex = imagecolorallocate(
            $f,
            $transparencyColor['red'],
            $transparencyColor['green'],
            $transparencyColor['blue']
        );
        imagefill($f, 0, 0, $transparencyIndex);
        imagecolortransparent($f, $transparencyIndex);

        imagecopyresampled($f, $watermark, 0, 0, 0, 0, $watermarkWidth, $watermarkHeight, $tmpW, $tmpH);
        $watermark = & $f;
    }

    $new = imagecreatetruecolor($imageWidth, $imageHeight);
    $footH = $imageHeight - $watermarkHeight;

    for ($j = 0; $j < $imageHeight; ++$j) {
        for ($i = 0; $i < $imageWidth; ++$i) {
            $rgb = imagecolorsforindex($image, imagecolorat($image, $i, $j));

            if ($GLOBALS['setup']['marker_where'] == 'top' && $j < $watermarkHeight && $i < $watermarkWidth) {
                $rgb2 = imagecolorsforindex($watermark, @imagecolorat($watermark, $i, $j));
                if ($rgb2['alpha'] != 127) {
                    $rgb['red'] = intval(($rgb['red'] + $rgb2['red']) / 2);
                    $rgb['green'] = intval(($rgb['green'] + $rgb2['green']) / 2);
                    $rgb['blue'] = intval(($rgb['blue'] + $rgb2['blue']) / 2);
                }
            } else {
                if ($GLOBALS['setup']['marker_where'] == 'foot' && $j >= $footH && $i < $watermarkWidth) {
                    $rgb2 = imagecolorsforindex($watermark, @imagecolorat($watermark, $i, $j - $footH));
                    if ($rgb2['alpha'] != 127) {
                        $rgb['red'] = intval(($rgb['red'] + $rgb2['red']) / 2);
                        $rgb['green'] = intval(($rgb['green'] + $rgb2['green']) / 2);
                        $rgb['blue'] = intval(($rgb['blue'] + $rgb2['blue']) / 2);
                    }
                }
            }

            $ind = imagecolorexact($new, $rgb['red'], $rgb['green'], $rgb['blue']);
            if ($ind < 1) {
                $ind = imagecolorallocate($new, $rgb['red'], $rgb['green'], $rgb['blue']);
                if ($ind < 1) {
                    $ind = imagecolorclosest($new, $rgb['red'], $rgb['green'], $rgb['blue']);
                }
            }
            imagesetpixel($new, $i, $j, $ind);
        }
    }


    return $new;
}


/**
 * Добавление вложений
 *
 * @param string $folder
 * @param int    $id
 * @param array  $files
 *
 * @return bool
 */
function add_attach($folder, $id, $files)
{
    $attach = $GLOBALS['setup']['apath'] . mb_substr($folder . '/', mb_strlen($GLOBALS['setup']['path']));
    foreach ($files as $k => $v) {
        move_uploaded_file($v['tmp_name'], $attach . $id . '_' . $k . '_' . $v['name']);
    }

    return true;
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
    $attach = $GLOBALS['setup']['apath'] . mb_substr($folder . '/', mb_strlen($GLOBALS['setup']['path']));
    foreach ($files as $k => $v) {
        unlink($attach . $id . '_' . $k . '_' . $v);
    }

    return true;
}


/**
 * Проверяем расширение по черному списку
 *
 * @param string $ext
 *
 * @return bool
 */
function checkExt($ext)
{
    return !in_array(
        strtolower($ext),
        array(
            'php',
            'php3',
            'php4',
            'php5',
            'php6',
            'html',
            'htm',
            'wml',
            'phtml',
            'phtm',
            'cgi',
            'asp',
            'js',
            'py',
            'pl',
            'jsp',
            'ry',
            'shtm',
            'shtml'
        )
    );
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
 * @return bool
 */
function _scannerDb($path, $name, $rus_name, $aze_name, $tur_name, $dir = true, $insert = true)
{
    if ($dir) {
        if ($insert) {
            $q = mysql_query(
                '
                INSERT INTO `files` (
                    `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size` ,`timeupload`
                ) VALUES (
                    "1",
                    "' . mysql_real_escape_string($path, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($rus_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($aze_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($tur_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string(dirname($path) . '/', $GLOBALS['mysql']) . '",
                    0,
                    "' . filectime($path) . '"
                )
            ',
                $GLOBALS['mysql']
            );
        } else {
            $q = mysql_query(
                '
                UPDATE `files`
                SET `name` = IF(`name` <> "", `name`, "' . mysql_real_escape_string($name, $GLOBALS['mysql']) . '"),
                `rus_name` = IF(`rus_name` <> "", `rus_name`, "' . mysql_real_escape_string(
                    $rus_name,
                    $GLOBALS['mysql']
                ) . '"),
                `aze_name` = IF(`aze_name` <> "", `aze_name`, "' . mysql_real_escape_string(
                    $aze_name,
                    $GLOBALS['mysql']
                ) . '"),
                `tur_name` = IF(`tur_name` <> "", `tur_name`, "' . mysql_real_escape_string(
                    $tur_name,
                    $GLOBALS['mysql']
                ) . '")
                WHERE `path` = "' . mysql_real_escape_string($path, $GLOBALS['mysql']) . '"
            ',
                $GLOBALS['mysql']
            );
        }
    } else {
        if ($insert) {
            $q = mysql_query(
                '
                INSERT INTO `files` (
                    `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size` ,`timeupload`
                ) VALUES (
                    "0",
                    "' . mysql_real_escape_string($path, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($rus_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($aze_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($tur_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string(dirname($path) . '/', $GLOBALS['mysql']) . '",
                    "' . filesize($path) . '",
                    "' . filectime($path) . '"
                )
            ',
                $GLOBALS['mysql']
            );
        } else {
            $q = mysql_query(
                '
                UPDATE `files`
                SET `name` = IF(`name` <> "", `name`, "' . mysql_real_escape_string($name, $GLOBALS['mysql']) . '"),
                `rus_name` = IF(`rus_name` <> "", `rus_name`, "' . mysql_real_escape_string(
                    $rus_name,
                    $GLOBALS['mysql']
                ) . '"),
                `aze_name` = IF(`aze_name` <> "", `aze_name`, "' . mysql_real_escape_string(
                    $aze_name,
                    $GLOBALS['mysql']
                ) . '"),
                `tur_name` = IF(`tur_name` <> "", `tur_name`, "' . mysql_real_escape_string(
                    $tur_name,
                    $GLOBALS['mysql']
                ) . '")
                WHERE `path` = "' . mysql_real_escape_string($path, $GLOBALS['mysql']) . '"
            ',
                $GLOBALS['mysql']
            );
        }
    }

    return $q;
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
        $is_file = ($pathinfo['basename'] != $cont && is_file($f) === true);


        $q = mysql_query(
            'SELECT `name`, `rus_name`, `aze_name`, `tur_name` FROM `files` WHERE `path` = "'
                . mysql_real_escape_string($f, $GLOBALS['mysql']) . '"'
        );

        if (!$q) {
            $errors[] = mysql_error($GLOBALS['mysql']);
            continue;
        }

        $insert = true;
        if (mysql_num_rows($q) > 0) {
            $insert = false;
            $row = mysql_fetch_assoc($q);
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



        $aze_name = $tur_name = $rus_name = $name = $pathinfo['filename'];
        if ($name == '') {
            $tmpErr = error_get_last();
            $errors[] = $tmpErr['message'];
            continue;
        }

        // транслит
        if ($name[0] === '!') {
            $aze_name = $tur_name = $rus_name = $name = substr($name, 1);
            $rus_name = trans($rus_name);
        }

        if ($is_dir) {
            // скриншоты
            $screen = $GLOBALS['setup']['spath'] . mb_substr($f, mb_strlen($GLOBALS['setup']['path']));
            if (!file_exists($screen)) {
                mkdir($screen, 0777);
            }
            chmod($screen, 0777);

            // описания
            $desc = $GLOBALS['setup']['opath'] . mb_substr($f, mb_strlen($GLOBALS['setup']['path']));
            if (!file_exists($desc)) {
                mkdir($desc, 0777);
            }
            chmod($desc, 0777);

            // вложения
            $attach = $GLOBALS['setup']['apath'] . mb_substr($f, mb_strlen($GLOBALS['setup']['path']));
            if (file_exists($attach) === false) {
                mkdir($attach, 0777);
            }
            chmod($attach, 0777);

            if (!_scannerDb($f, $name, $rus_name, $aze_name, $tur_name, true, $insert)) {
                $errors[] = mysql_error($GLOBALS['mysql']);
            }

            $folders++;
            scanner($f);
        } elseif ($is_file === true) {
            $files++;
            if (!_scannerDb($f, $name, $rus_name, $aze_name, $tur_name, false, $insert)) {
                $errors[] = mysql_error($GLOBALS['mysql']);
            }
        }
    }

    return array('folders' => $folders, 'files' => $files, 'errors' => $errors);
}


/**
 * Обновление кол-ва файлов в директориях
 */
function scannerCount()
{
    $res = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "1" GROUP BY `path`', $GLOBALS['mysql']);
    while ($dir = mysql_fetch_row($res)) {
        $dir = mysql_real_escape_string($dir[0], $GLOBALS['mysql']);

        mysql_query(
            'UPDATE `files` SET `dir_count` = ' . intval(
                mysql_result(
                    mysql_query(
                        'SELECT COUNT(1) FROM `files` WHERE `infolder` LIKE "' . str_replace(array('_', '%'), array('\_', '\%'), $dir) . '%" AND `hidden` = "0"',
                        $GLOBALS['mysql']
                    ),
                    0
                )
            ) . ' WHERE `path`="' . $dir . '"',
            $GLOBALS['mysql']
        );
    }
}


/**
 * Массив всех директорий
 *
 * @return array
 */
function getAllDirs()
{
    $dirs = array($GLOBALS['setup']['path'] . '/' => $GLOBALS['setup']['path'] . '/');

    $q = mysql_query('SELECT `path` FROM `files` WHERE `dir` = "1"', $GLOBALS['mysql']);
    while ($item = mysql_fetch_assoc($q)) {
        $dirs[$item['path']] = $item['path'];
    }

    return $dirs;
}


/**
 * Бредкрамбсы для директорий или файлов
 *
 * @param array $info
 * @param bool $is_dir
 *
 * @return array
 */
function getBreadcrumbs($info, $is_dir = false)
{
    global $seo;

    $ex = explode('/', rtrim($info['path'], '/'));
    $l = sizeof($ex);

    $breadcrumbs = array();
    if ($l > 1) {
        $path = '';

        $sql = '
        SELECT `id`,
        ' . Language::getInstance()->buildFilesQuery() . '
        FROM `files`
        WHERE `path` IN(';

        for ($i = 0; $i < $l; ++$i) {
            if (!$is_dir && ($i + 1) === $l) {
                $path .= $ex[$i];
            } else {
                $path .= $ex[$i] . '/';
            }

            $sql .= '"' . mysql_real_escape_string($path, $GLOBALS['mysql']) . '",';
        }
        $sql = rtrim($sql, ',') . ')';

        $q = mysql_query($sql, $GLOBALS['mysql']);
        while ($s = mysql_fetch_assoc($q)) {
            $breadcrumbs[$s['id']] = $s['name'];
        }
    }

    if (isset($seo['title']) === false || $seo['title'] == '') {
        $seo['title'] = implode(' / ', $breadcrumbs);
    }

    return $breadcrumbs;
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
    for ($i = 0, $all = sizeof($arr); $i < $all; ++$i) {
        if ($i > 0) {
            $in[$i] = $in[$i - 1] . mysql_real_escape_string($arr[$i], $GLOBALS['mysql']) . '/';
        } else {
            $in[$i] = mysql_real_escape_string($arr[$i], $GLOBALS['mysql']) . '/';
        }
    }

    return mysql_query(
        '
        UPDATE `files`
        SET `dir_count` = `dir_count` ' . ($increment ? '+' : '-') . ' 1
        WHERE `path` IN ("' . implode('","', $in) . '")',
        $GLOBALS['mysql']
    );
}


/**
 * Создает файл
 * Последний элемент в path считается файлом. Директория согласно функции pathinfo
 *
 * @param string $path
 * @param int    $chmod_dir
 * @param int    $chmod_file
 *
 * @return bool
 */
function chmods($path = '', $chmod_dir = 0777, $chmod_file = 0666)
{
    @mkdir(pathinfo($path, PATHINFO_DIRNAME), $chmod_dir, true);
    file_put_contents($path, '');

    return chmod($path, $chmod_file);
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
    if (file_exists(dirname(__FILE__) . '/../cache/' . $id . '.dat')) {
        return unserialize(file_get_contents(dirname(__FILE__) . '/../cache/' . $id . '.dat'));
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

    include_once dirname(__FILE__) . '/../PEAR/Archive/Tar.php';

    $thm = new Archive_Tar($path);


    if (!$file = $thm->extractInString(pathinfo($path, PATHINFO_FILENAME) . '.xml')) {
        $file = $thm->extractInString('Theme.xml');
    }

    if (!$file) {
        $list = $thm->listContent();
        $all = sizeof($list);
        for ($i = 0; $i < $all; ++$i) {
            if (pathinfo($list[$i]['filename'], PATHINFO_EXTENSION) == 'xml') {
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

    file_put_contents(dirname(__FILE__) . '/../cache/' . $id . '.dat', serialize($out));
    return $out;
}


/**
 * Упрощенный ресайзер картинок
 *
 * @param resource $data
 * @return resource
 */
function simple_resize($data)
{
    if (!is_resource($data)) {
        return false;
    }

    $hn = imagesy($data);
    $wn = imagesx($data);

    list($w, $h) = explode('*', $GLOBALS['setup']['prev_size']);

    $sxy = round($wn / $hn, 3);
    if ($sxy < 1) {
        $w = intval($h * $sxy);
    } else {
        $h = intval($w / $sxy);
    }

    $im = imagecreatetruecolor($w, $h);
    imagecopyresampled($im, $data, 0, 0, 0, 0, $w, $h, $wn, $hn);

    return $im;
}


/**
 * Ресайзер картинок
 *
 * @param string $in
 * @param string $out
 * @param int $w
 * @param int $h
 * @param bool $marker
 *
 * @return bool
 */
function img_resize($in = '', $out = '', $w = 0, $h = 0, $marker = false)
{
    if (!is_writable(dirname($out))) {
        return false;
    }

    //$out = pathinfo($out);
    //$out = realpath($out['dirname']) . '/' . $out['basename'];

    if (!$w || !$h) {
        list($w, $h) = explode('*', $GLOBALS['setup']['prev_size']);
    }


    list($wn, $hn, $type) = getimagesize($in);


    $sxy = round($wn / $hn, 3);
    if ($sxy < 1) {
        $w = intval($h * $sxy);
    } else {
        $h = intval($w / $sxy);
    }


    $dir = dirname(__FILE__);

    switch ($type) {
        case 1:
            if ($GLOBALS['setup']['anim_change']) {
                ini_set('memory_limit', '256M');

                // GIF Поддержка анимации    
                include_once $dir . '/GIFDecoder.class.php';
                include_once $dir . '/GIFEncoder.class.php';

                $gif = new GIFDecoder(file_get_contents($in));

                $arr = $gif->GIFGetFrames();
                $dly = $gif->GIFGetDelays();
                $frames = $framed = array();

                $a = sizeof($arr);
                for ($i = 0; $i < $a; ++$i) {
                    $tmp1 = $dir . '/../cache/' . uniqid() . '.gif';
                    $tmp2 = $dir . '/../cache/' . uniqid() . '.gif';

                    file_put_contents($tmp1, $arr[$i]);
                    $resize = imagecreatefromgif($tmp1);

                    $image_p = imagecreatetruecolor($w, $h);
                    imagecopyresampled($image_p, $resize, 0, 0, 0, 0, $w, $h, $wn, $hn);


                    if ($marker) {
                        $image_p = marker($image_p, imagecreatefrompng($dir . '/../marker.png'));
                    }

                    imagegif($image_p, $tmp2);
                    imagedestroy($image_p);
                    imagedestroy($resize);

                    $frames[] = file_get_contents($tmp2);
                    $framed[] = $dly[$i];

                    unlink($tmp1);
                    unlink($tmp2);
                }
                unset($gif, $arr, $dly);

                $gif = new GIFEncoder(
                    $frames,
                    $framed,
                    0,
                    2,
                    0, 0, 0,
                    0,
                    'bin'
                );

                unset($frames, $framed);

                return file_put_contents($out, $gif->GetAnimation());
                break;
            } else {
                // GIF
                $old = imagecreatefromgif($in);
            }
            break;


        case 2:
            // JPEG
            $old = imagecreatefromjpeg($in);
            break;


        case 3:
            // PNG
            $old = imagecreatefrompng($in);
            break;


        case 4:
        case 13:
            // SWF
            rename($in, $out);

            return true;
            break;


        case 6:
            // BMP
            include_once $dir . '/bmp.php';
            $old = imagecreatefrombmp($in, $dir . '/../cache/');
            break;


        default:
            return false;
            break;
    }


    $new = imagecreatetruecolor($w, $h);
    imagecopyresampled($new, $old, 0, 0, 0, 0, $w, $h, $wn, $hn);

    if ($marker) {
        $new = marker($new, imagecreatefrompng($dir . '/../marker.png'));
    }


    $f = imagegif($new, $out);
    imagedestroy($old);
    imagedestroy($new);

    return $f;
}


/**
 * Иконки из JAR файлов
 */
function jar_ico($jar, $f)
{
    include_once dirname(__FILE__) . '/../PEAR/pclzip.lib.php';

    $icon = array();
    $archive = new PclZip($jar);

    $list = $archive->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);


    if (@$list[0]['content']) {
        if (!$icon) {
            preg_match('/MIDlet\-Icon:[\s*](.*)/iux', $list[0]['content'], $arr);

            if (@$arr[1]) {
                foreach (explode(',', $arr[1]) as $v) {
                    $v = trim(trim($v), '/');
                    if (strtolower(pathinfo($v, PATHINFO_EXTENSION)) == 'png') {
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
                    if (strtolower(pathinfo($v, PATHINFO_EXTENSION)) == 'png') {
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
 * Отображение ошибок
 */
function error($str = '')
{
    global $template, $setup, $mysql;
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
    global $template, $setup, $mysql;
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
 * Возвращает случайный пароль
 *
 * @param int $min
 * @param int $max
 *
 * @return string random password 6-8 symbols
 */
function pass($min = 6, $max = 8)
{
    return substr(
        str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWZYZ0123456789'),
        0,
        mt_rand($min, $max)
    );
}


/**
 * Конвертируем из Windows-1251 в UTF-8
 *
 * @param string $str
 *
 * @return string
 */
function str_to_utf8($str)
{
    if (@mb_convert_encoding($str, 'UTF-8', 'UTF-8') != $str) {
        $str = mb_convert_encoding($str, 'UTF-8', 'Windows-1251');
    }

    return $str;
}


/**
 * Постраничная навигация
 *
 * @param int     $pg  текущая страница
 * @param int     $all всего страниц
 * @param string  $str url страницы, к нему прибавится /номер
 *
 * @return string      html с навигацией
 */
function go($pg = 0, $all = 0, $str)
{
    $go = '';

    $page1 = $pg - 2;
    $page2 = $pg - 1;
    $page3 = $pg + 1;
    $page4 = $pg + 2;

    if ($page1 > 0) {
        $go .= '<a href="' . $str . '/' . $page1 . '">' . $page1 . '</a> ';
    }

    if ($page2 > 0) {
        $go .= '<a href="' . $str . '/' . $page2 . '">' . $page2 . '</a> ';
    }

    $go .= $pg . ' ';

    if ($page3 <= $all) {
        $go .= '<a href="' . $str . '/' . $page3 . '">' . $page3 . '</a> ';
    }
    if ($page4 <= $all) {
        $go .= '<a href="' . $str . '/' . $page4 . '">' . $page4 . '</a> ';
    }

    if ($all > 3 && $all > $page4) {
        $go .= '... <a href="' . $str . '/' . $all . '">' . $all . '</a>';
    }

    if ($page1 > 1) {
        $go = '<a href="' . $str . '/1">1</a> ... ' . $go;
    }

    if ($go == $pg . ' ') {
        return '';
    } else {
        return '<div class="row">&#160;' . $go . '</div>';
    }
}


/**
 * Возвращает MIME файла по его расширению
 *
 * @param  string $ext  расширение
 *
 * @return string       MIME тип
 */
function ext_to_mime($ext = '')
{
    switch (strtolower($ext)) {
        default:
            return 'application/octet-stream';
            break;

        case 'jar':
            return 'application/java-archive';
            break;

        case 'jad':
            return 'text/vnd.sun.j2me.app-descriptor';
            break;

        case 'cab':
            return 'application/vnd.ms-cab-compressed';
            break;

        case 'sis':
            return 'application/vnd.symbian.install';
            break;

        case 'zip':
            return 'application/x-zip';
            break;

        case 'rar':
            return 'application/x-rar-compressed';
            break;

        case '7z':
            return 'application/x-7z-compressed';
            break;

        case 'gz':
        case 'tgz':
            return 'application/x-gzip';
            break;

        case 'bz':
        case 'bz2':
            return 'application/x-bzip';
            break;

        case 'jpg':
        case 'jpe':
        case 'jpeg':
            return 'image/jpeg';
            break;

        case 'gif':
            return 'image/gif';
            break;

        case 'png':
            return 'image/png';
            break;

        case 'bmp':
            return 'image/bmp';
            break;

        case 'txt':
        case 'dat':
        case 'php':
        case 'php4':
        case 'php5':
        case 'phtml':
        case 'htm':
        case 'html':
        case 'shtm':
        case 'shtml':
        case 'wml':
        case 'css':
        case 'js':
        case 'xml':
        case 'sql':
        case 'tpl':
            return 'text/plain';
            break;

        case 'mmf':
            return 'application/x-smaf';
            break;

        case 'mid':
            return 'audio/mid';
            break;

        case 'mp3':
            return 'audio/mpeg';
            break;

        case 'amr':
            return 'audio/amr';
            break;

        case 'wav':
            return 'audio/x-wav';
            break;

        case 'mp4':
            return 'video/mp4';
            break;

        case 'wmv':
            return 'video/x-ms-wmv';
            break;

        case '3gp':
            return 'video/3gpp';
            break;

        case 'flv':
            return 'video/x-flv';
            break;

        case 'avi':
            return 'video/x-msvideo';
            break;

        case 'mpg':
        case 'mpe':
        case 'mpeg':
            return 'video/mpeg';
            break;

        case 'pdf':
            return 'application/pdf';
            break;

        case 'doc':
        case 'docx':
            return 'application/msword';
            break;

        case 'swf':
            return 'application/x-shockwave-flash';
            break;

        case 'apk':
            return 'application/vnd.android.package-archive';
            break;
    }
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
    if (file_exists(dirname(__FILE__) . '/../cache/' . $id . '.dat')) {
        return unserialize(file_get_contents(dirname(__FILE__) . '/../cache/' . $id . '.dat'));
    }

    $tmpa = array();
    $filename = pathinfo($path);
    $ext = strtolower($filename['extension']);

    if ($ext == 'mp3' || $ext == 'wav') {
        include dirname(__FILE__) . '/classAudioFile.php';

        $audio = new AudioFile;
        $audio->loadFile($path);

        if ($audio->wave_length > 0) {
            $length = $audio->wave_length;
        } else {
            include dirname(__FILE__) . '/mp3.class.php';
            $mp3 = new mp3($path);
            $mp3->setFileInfoExact();
            $length = $mp3->time;
        }
        $comments = array();

        if (isset($audio->id3v2->APIC) && $audio->id3v2->APIC) {
            $comments['APIC'] = $audio->id3v2->APIC;
        } else {
            $comments['APIC'] = false;
        }
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
    } elseif ($ext == 'ogg') {
        include dirname(__FILE__) . '/../PEAR/File/Ogg.php';
        try {
            $ogg = new File_Ogg($path);
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

    file_put_contents(dirname(__FILE__) . '/../cache/' . $id . '.dat', serialize($tmpa));
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
    if (file_exists(dirname(__FILE__) . '/../cache/' . $id . '.dat')) {
        return unserialize(file_get_contents(dirname(__FILE__) . '/../cache/' . $id . '.dat'));
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
        file_put_contents(dirname(__FILE__) . '/../cache/' . $id . '.dat', serialize($tmpa));
    }

    return $tmpa;
}


/**
 * @param string $email
 *
 * @return bool
 */
function isValidEmail ($email)
{
    return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
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
    return mysql_fetch_assoc(
        mysql_query('
            SELECT *, ' . Language::getInstance()->buildFilesQuery() . '
            FROM `files`
            WHERE `id` = ' . intval($id) . '
            ' . (IS_ADMIN !== true ? 'AND `hidden` = "0"' : '') . '
        ',
            $GLOBALS['mysql']
        )
    );
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
    return mysql_unbuffered_query('
        UPDATE `files`
        SET `loads` = `loads` + 1,
        `timeload` = ' . $_SERVER['REQUEST_TIME'] . '
        WHERE `id` = ' . intval($id),
        $GLOBALS['mysql']
    );
}


function hex2rgb($hex)
{
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }

    return array($r, $g, $b);
}
