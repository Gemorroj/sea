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
class Helper
{
    /**
     * Получаем настройки постраничной навигации
     *
     * @param int $items
     *
     * @return array
     */
    public static function getPaginatorConf($items)
    {
        $onpage = self::get2ses('onpage');
        $items = intval($items);
        $page = abs(Http_Request::get('page', 1));

        if (Config::get('ignore_index_pages') && defined('IS_INDEX') && IS_INDEX === true) {
            // переопределяем пагинацию, если это главная
            return array(
                'start' => 0,
                'page' => 1,
                'pages' => 1,
                'onpage' => $items,
                'items' => $items,
            );
        }

        $onpage = $onpage > $items ? $items : $onpage;
        if ($onpage < 3) {
            $onpage = Config::get('onpage');
        }

        $pages = ceil($items / $onpage);

        if ($pages < 1) {
            $pages = 1;
        }
        if ($page > $pages || $page < 1) {
            $page = 1;
        }

        $start = ($page - 1) * $onpage;
        if ($start > $items || $start < 0) {
            $start = 0;
        }

        return array(
            'start' => (int)$start,
            'page' => (int)$page,
            'pages' => (int)$pages,
            'onpage' => (int)$onpage,
            'items' => (int)$items,
        );
    }


    /**
     * Часть SQL запроса для сортировки ORDER BY
     *
     * @param string $prefix
     *
     * @return string
     */
    public static function getSortMode($prefix = null)
    {
        $sort = self::get2ses('sort');
        $prefix = ($prefix === null ? '' : '`' . $prefix . '`.');

        if ($sort === 'date') {
            $mode = $prefix . '`priority` DESC, ' . $prefix . '`timeupload` DESC';
        } elseif ($sort === 'size') {
            $mode = $prefix . '`priority` DESC, ' . $prefix . '`size` ASC';
        } elseif ($sort === 'load') {
            $mode = $prefix . '`priority` DESC, ' . $prefix . '`loads` DESC';
        } elseif ($sort === 'eval' && Config::get('eval_change')) {
            $mode = $prefix . '`priority` DESC, ' . $prefix . '`yes` DESC , ' . $prefix . '`no` ASC';
        } else {
            $mode = $prefix . '`priority` DESC, ' . $prefix . '`name` ASC';
        }

        return $prefix . '`dir` DESC, ' . $mode;
    }


    /**
     * Создает файл
     * Последний элемент в path считается файлом. Директория согласно функции pathinfo
     *
     * @param string $path
     * @param int    $chmodDir
     * @param int    $chmodFile
     *
     * @return bool
     */
    public static function touch($path = '', $chmodDir = 0777, $chmodFile = 0666)
    {
        @mkdir(pathinfo($path, PATHINFO_DIRNAME), $chmodDir, true);
        file_put_contents($path, '');

        return chmod($path, $chmodFile);
    }


    /**
     * @param string $hex
     *
     * @return array
     */
    public static function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
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

    /**
     * @param string $str
     *
     * @return string
     */
    public static function removeSchema($str)
    {
        return preg_replace('/^(?:.*:\/\/)/', '', $str);
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public static function isValidEmail($email)
    {
        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function isValidUrl($url)
    {
        return (bool)filter_var('http://' . $url, FILTER_VALIDATE_URL);
    }

    /**
     * @param string $style
     *
     * @return bool
     */
    public static function isValidStyle($style)
    {
        return (bool)filter_var('http://' . $style, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
    }

    /**
     * Конвертируем из Windows-1251 в UTF-8
     *
     * @param string $str
     *
     * @return string
     */
    public static function str2utf8($str)
    {
        if (@mb_convert_encoding($str, 'UTF-8', 'UTF-8') !== $str) {
            $str = mb_convert_encoding($str, 'UTF-8', 'Windows-1251');
        }

        return $str;
    }


    /**
     * Возвращает случайный пароль
     *
     * @param int $min
     * @param int $max
     *
     * @return string random password 6-8 symbols
     */
    public static function getRandPass($min = 6, $max = 8)
    {
        return substr(
            str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWZYZ0123456789'),
            0,
            mt_rand($min, $max)
        );
    }


    /**
     * $_GET -> $_SESSION
     *
     * @param string $name
     *
     * @return string
     */
    public static function get2ses($name)
    {
        if (isset($_SESSION[$name]) === false) {
            $_SESSION[$name] = Config::get($name);
        }
        if (Http_Request::get($name) !== null) {
            $_SESSION[$name] = Http_Request::get($name);
        }

        // да, именно переменная переменных
        return $$name = $_SESSION[$name];
    }


    /**
     * Проверяем расширение по черному списку
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isBlockedExt($ext)
    {
        return in_array(
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
     * Возвращает MIME файла по его расширению
     *
     * @param  string $ext  расширение
     *
     * @return string       MIME тип
     */
    public static function ext2mime($ext = null)
    {
        switch (strtolower($ext)) {
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
            case 'cfg':
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
            case 'ini':
            case 'log':
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
                return 'application/msword';
                break;

            case 'docx':
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                break;

            case 'xls':
                return 'application/vnd.ms-excel';
                break;

            case 'xlsx':
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                break;

            case 'swf':
                return 'application/x-shockwave-flash';
                break;

            case 'apk':
                return 'application/vnd.android.package-archive';
                break;

            case 'webm':
                return 'video/webm';
                break;

            case 'ogg':
                return 'audio/ogg';
                break;

            default:
                return 'application/octet-stream';
                break;

        }
    }
}
