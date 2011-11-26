<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#             	 Автор  :  Sea                        #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#   		По всем вопросам пишите в ICQ.        #
#-----------------------------------------------------#

// mod Gemorroj

require_once DIR . '/language.php';


/**
 * Транслит с латниницы на русский
 * 
 * @param string $t
 * @return string
 */
function trans($t)
{
    $a = array('_', 'YA', 'Ya', 'ya', 'yee', 'YO', 'yo', 'Yo', 'ZH', 'zh', 'Zh', 'Z',
        'z', 'CH', 'ch', 'Ch', 'SH', 'sh', 'Sh', 'YE', 'ye', 'Ye', 'YU', 'yu', 'Yu',
        'JA', 'ja', 'Ja', 'A', 'a', 'B', 'b', 'V', 'v', 'G', 'g', 'D', 'd', 'E', 'e',
        'I', 'i', 'J', 'j', 'K', 'k', 'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o', 'P', 'p',
        'R', 'r', 'S', 's', 'T', 't', 'U', 'u', 'F', 'f', 'H', 'h', 'W', 'w', 'x', 'q',
        'Y', 'y', 'C', 'c');
    $b = array(' ', 'Я', 'Я', 'я', 'ые', 'Ё', 'ё', 'Ё', 'Ж', 'ж', 'Ж', 'З', 'з', 'Ч',
        'ч', 'Ch', 'Ш', 'ш', 'Ш', 'Э', 'э', 'Э', 'Ю', 'ю', 'Ю', 'Я', 'я', 'Я', 'А', 'а',
        'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'И', 'и', 'Й', 'й', 'К', 'к',
        'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т',
        'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Щ', 'щ', 'ъ', 'ь', 'Ы', 'ы', 'Ц', 'ц', '');
    return str_replace($a, $b, $t);
}


/**
 * Транслит с русского на английский
 * 
 * @param string $t
 * @return string
 */
function retrans($t)
{
    $a = array('_', 'YA', 'Ya', 'ya', 'yee', 'YO', 'yo', 'Yo', 'ZH', 'zh', 'Zh', 'Z',
        'z', 'CH', 'ch', 'Ch', 'SH', 'sh', 'Sh', 'YE', 'ye', 'Ye', 'YU', 'yu', 'Yu',
        'JA', 'ja', 'Ja', 'A', 'a', 'B', 'b', 'V', 'v', 'G', 'g', 'D', 'd', 'E', 'e',
        'I', 'i', 'J', 'j', 'K', 'k', 'L', 'l', 'M', 'm', 'N', 'n', 'O', 'o', 'P', 'p',
        'R', 'r', 'S', 's', 'T', 't', 'U', 'u', 'F', 'f', 'H', 'h', 'W', 'w', 'x', 'q',
        'Y', 'y', 'C', 'c');
    $b = array(' ', 'Я', 'Я', 'я', 'ые', 'Ё', 'ё', 'Ё', 'Ж', 'ж', 'Ж', 'З', 'з', 'Ч',
        'ч', 'Ch', 'Ш', 'ш', 'Ш', 'Э', 'э', 'Э', 'Ю', 'ю', 'Ю', 'Я', 'я', 'Я', 'А', 'а',
        'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'И', 'и', 'Й', 'й', 'К', 'к',
        'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т',
        'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Щ', 'щ', 'ъ', 'ь', 'Ы', 'ы', 'Ц', 'ц');
    return str_replace($b, $a, $t);
}


/**
 * Ббкод в html
 * 
 * @param string $text
 * @return string
 */
function bbcode($text = '')
{
    //конвертация кодов в теги регулярными выражениями
    // ББ коды
    $bbcode = array(
    	'/\[url\](.+)\[\/url\]/isU' => '<a href="$1">$1</a>',
    	'/\[url=(.+)\](.+)\[\/url\]/isU' => '<a href="$1">$2</a>',
    	'/\[i\](.+)\[\/i\]/isU' => '<em>$1</em>',
    	'/\[b\](.+)\[\/b\]/isU' => '<strong>$1</strong>',
    	'/\[u\](.+)\[\/u\]/isU' => '<span style="text-decoration:underline;">$1</span>',
    	'/\[big\](.+)\[\/big\]/isU' => '<span style="font-size:large;">$1</span>',
    	'/\[small\](.+)\[\/small\]/isU' => '<span style="font-size:small;">$1</span>',
    	'/\[code\](.+)\[\/code\]/isU' => '<code>$1</code>',
    	'/\[red\](.+)\[\/red\]/isU' => '<span style="color:#ff0000;">$1</span>',
    	'/\[yellow\](.+)\[\/yellow\]/isU' => '<span style="color:#ffff22;">$1</span>',
    	'/\[green\](.+)\[\/green\]/isU' => '<span style="color:#00bb00;">$1</span>',
    	'/\[blue\](.+)\[\/blue\]/isU' => '<span style="color:#0000bb;">$1</span>',
    	'/\[white\](.+)\[\/white\]/isU' => '<span style="color:#ffffff;">$1</span>',
    	'/\[color=(.+)\](.+)\[\/color\]/isU' => '<span style="color:$1;">$2</span>',
    	'/\[size=([0-9]+)\](.+)\[\/size\]/isU' => '<span style="font-size:$1px;">$2</span>',
    	'/\[img\](.+)\[\/img\]/isU' => '<img src="$1" alt=""/>',
        '/\[br\]/isU' => '<br />'
	);
    return preg_replace(array_keys($bbcode), array_values($bbcode), $text);
}


/**
 * Из html в ббкод
 * 
 * @param string $text
 * @retirn string
 */
function antibb($text = '')
{
	// обратное преобразование ббкодов
	$bbcode = array(
    	'/<a href="(.+)">(.+)<\/a>/isU' => '[url=$1]$2[/url]',
    	'/<em>(.+)<\/em>/isU' => '[i]$1[/i]',
    	'/<strong>(.+)<\/strong>/isU' => '[b]$1[/b]',
    	'/<span style="text-decoration:underline;">(.+)<\/span>/isU' => '[u]$1[/u]',
    	'/<span style="font-size:large;">(.+)<\/span>/isU' => '[big]$1[/big]',
    	'/<span style="font-size:small;">(.+)<\/span>/isU' => '[small]$1[/small]',
    	'/<code>(.+)<\/code>/isU' => '[code]$1[/code]',
    	'/<span style="color:#ff0000;">(.+)<\/span>/isU' => '[red]$1[/red]',
    	'/<span style="color:#ffff22;">(.+)<\/span>/isU' => '[yellow]$1[/yellow]',
    	'/<span style="color:#00bb00;">(.+)<\/span>/isU' => '[green]$1[/green]',
    	'/<span style="color:#0000bb;">(.+)<\/span>/isU' => '[blue]$1[/blue]',
    	'/<span style="color:#ffffff;">(.+)<\/span>/isU' => '[white]$1[/white]',
    	'/<span style="color:(.+);">(.+)<\/span>/isU' => '[color=$1]$2[/color]',
    	'/<span style="font-size:([0-9]+)px;">(.+)<\/span>/isU' => '[size=$1]$2[/size]',
    	'/<img src="(.+)" alt=""\/>/isU' => '[img]$1[/img]',
    	'/<br \/>/isU' => '[br]'
	);
	return preg_replace(array_keys($bbcode), array_values($bbcode), $text);
}


/**
 * Число ли
 */
function is_num($txt, $name)
{
    if (isset($_POST[$name])) {
        $txt = $_POST[$name];
    } else if (isset($_GET[$name])) {
        $txt = $_GET[$name];
    } else if (isset($_SESSION[$name])) {
        $txt = $_SESSION[$name];
    }

    if (intval($txt) < 0) {
        error($GLOBALS['setup']['hackmess']);
    }
    return;
}


/**
 * checked
 */
function check($value)
{
    if (!$value) {
        return;
    } else {
        return 'checked="checked"';
    }
}


/**
 * selected
 */
function sel($value, $real)
{
    if ($value != $real) {
        return;
    } else {
        return 'selected="selected"';
    }
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

    return $$name = $_SESSION[$name];
}


/**
 * Маркер картинок
 * 
 * @param resource $image
 * @param resource $watermark
 * @return resource
 */
function marker($image = '', $watermark = '')
{
    if (!is_resource($image) || !is_resource($watermark)) {
        return;
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

        $transparencyIndex = imagecolorallocate($f, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
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
            } else if ($GLOBALS['setup']['marker_where'] == 'foot' && $j >= $footH && $i < $watermarkWidth) {
                $rgb2 = imagecolorsforindex($watermark, @imagecolorat($watermark, $i, $j - $footH));
                if ($rgb2['alpha'] != 127) {
                    $rgb['red'] = intval(($rgb['red'] + $rgb2['red']) / 2);
                    $rgb['green'] = intval(($rgb['green'] + $rgb2['green']) / 2);
                    $rgb['blue'] = intval(($rgb['blue'] + $rgb2['blue']) / 2);
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
 * @param int $id
 * @param array $files
 * @return bool
 */
function add_attach ($folder, $id, $files)
{
    $attach = $GLOBALS['setup']['apath'] . iconv_substr($folder . '/', iconv_strlen($GLOBALS['setup']['path']));
    foreach ($files as $k => $v) {
        move_uploaded_file($v['tmp_name'], $attach . $id . '_' . $k . '_' . $v['name']);
    }

    return true;
}


/**
 * Удаление вложений
 * 
 * @param string $folder
 * @param int $id
 * @param array $files
 * @return bool
 */
function del_attach ($folder, $id, $files)
{
    $attach = $GLOBALS['setup']['apath'] . iconv_substr($folder . '/', iconv_strlen($GLOBALS['setup']['path']));
    foreach ($files as $k => $v) {
        unlink($attach . $id . '_' . $k . '_' . $v);
    }

    return true;
}


/**
 * Проверяем расширение по черному списку
 * 
 * @param string $ext
 * @return bool
 */
function checkExt ($ext)
{
    return !in_array(strtolower($ext), array('php', 'php3', 'php4', 'php5', 'php6', 'html', 'htm', 'wml', 'phtml', 'phtm', 'cgi', 'asp', 'js', 'py', 'pl', 'jsp', 'ry', 'shtm', 'shtml'));
}


/**
 * Обновление данных загрузок в БД
 * 
 * @return array
 */
function scaner($path = '', $cont = 'folder.png')
{
    static $folders = 0;
    static $files = 0;
    static $errors = array();

    // заглушка
    echo 'scan ' . htmlspecialchars($path, ENT_NOQUOTES) . '...' . str_repeat(' ', 56) . '<br/>';
    ob_flush();
    
    if (!is_readable($path)) {
    	echo 'Error<br/>';
    	return array();
   	}


    chmod($path, 0777);
    $tmp = 0;

	foreach (array_diff(scandir($path, 0), array('.', '..')) as $file) {
        if ($file[0] == '.') {
            continue;
        }

        $f = str_replace('//', '/', $path . '/' . $file);

        $tmp++;
        if ($tmp > 500) {
			$tmp = 0;
            // такая вот хуита... =( забиваем буфер
			echo 'scan ' . htmlspecialchars($f, ENT_NOQUOTES) . '...' . str_repeat(' ', 512/*4096*/) . '<br/>';
			ob_flush();
		}

        $pathinfo = pathinfo($f);
        $rus_name = $name = $pathinfo['filename'];

        // транслит
        if ($name[0] == '!') {
            $name = $rus_name = substr($name, 1);
            $rus_name = trans($rus_name);
        }

        if (is_dir($f)) {

            // скриншоты
        	$screen = $GLOBALS['setup']['spath'] . iconv_substr($f . '/', iconv_strlen($GLOBALS['setup']['path']));
            if (!file_exists($screen)) {
                mkdir($screen, 0777);
            }
            chmod($screen, 0777);

        	// описания
        	$desc = $GLOBALS['setup']['opath'] . iconv_substr($f . '/', iconv_strlen($GLOBALS['setup']['path']));
            if (!file_exists($desc)) {
                mkdir($desc, 0777);
            }
        	chmod($desc, 0777);

            // вложения
        	$attach = $GLOBALS['setup']['apath'] . iconv_substr($f . '/', iconv_strlen($GLOBALS['setup']['path']));
            if (!file_exists($attach)) {
                mkdir($attach, 0777);
            }
        	chmod($attach, 0777);

            sleep(0.01); // =///
            if (!mysql_query('
                INSERT IGNORE INTO `files`
                (`dir`, `path`, `name`, `rus_name`, `infolder`, `size` ,`timeupload`)
                VALUES(
                "1",
                "' . mysql_real_escape_string($f . '/', $GLOBALS['mysql']) . '",
                "' . mysql_real_escape_string($name, $GLOBALS['mysql']) . '",
                "' . mysql_real_escape_string($rus_name, $GLOBALS['mysql']) . '",
                "' . mysql_real_escape_string($pathinfo['dirname'] . '/', $GLOBALS['mysql']) . '",
                0,
                "' . filectime($f) . '"
                )
            ', $GLOBALS['mysql'])) {
                $errors[] = mysql_error($GLOBALS['mysql']);
            }

            $folders++;
            scaner($f);
        } else if (is_file($f)) {

            $files++;
            if ($pathinfo['basename'] == $cont) {
                continue;
            } else {
                sleep(0.01); // =///
                if (!mysql_query('
                    INSERT IGNORE INTO `files`
                    (`dir`, `path`, `name`, `rus_name`, `infolder`, `size` ,`timeupload`)
                    VALUES(
                    "0",
                    "' . mysql_real_escape_string($f, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($rus_name, $GLOBALS['mysql']) . '",
                    "' . mysql_real_escape_string($pathinfo['dirname'] . '/', $GLOBALS['mysql']) . '",
                    "' . filesize($f) . '",
                    "' . filectime($f) . '"
                    )
                ', $GLOBALS['mysql'])) {
                    $errors[] = mysql_error($GLOBALS['mysql']);
                }
            }
        }
    }

    return array('folders' => $folders, 'files' => $files, 'errors' => $errors);
}


/**
 * Изменение количества файлов в директориях
 * 
 * @param string директория
 * @param bool инкремент или декремент
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
    return mysql_query('UPDATE `files` SET `dir_count` = `dir_count` ' . ($increment ? '+' : '-') . ' 1 WHERE `path` IN ("' . implode('","', $in) . '")', $GLOBALS['mysql']);
}


/**
 * Размер
 * 
 * @param int $int
 * @return string
 */
function size($int = 0)
{
    if ($int < 1024) {
    	return $int . 'b';
    } else if ($int < 1048576) {
    	return round($int / 1024, 2) . 'Kb';
    } else if ($int < 1073741824) {
    	return round($int / 1048576, 2) . 'Mb';
    } else {
    	return round($int / 1073741824, 2) . 'Gb';
    }
}


/**
 * Создает файл
 * Последний элемент в path считается файлом. Директория согласно функции pathinfo
 * 
 * @path string
 * @return bool
 */
function chmods($path = '', $chmod_dir = 0777, $chmod_file = 0666)
{
    @mkdir(pathinfo($path, PATHINFO_DIRNAME), $chmod_dir, true);
    file_put_contents($path, '');
    return chmod($path, $chmod_file);
}


/**
 * Время
 * 
 * @param int $t
 * @return string
 */
function tm($t)
{
    if (date('Y.m.d', $t) == date('Y.m.d', $_SERVER['REQUEST_TIME'])) {
        return $_SESSION['language']['today'] . ' ' . date('H:i', $t);
    } else if (date('Y.m.d', $t) == date('Y.m.d', $_SERVER['REQUEST_TIME'] - 86400)) {
        return $_SESSION['language']['yesterday'] . ' ' . date('H:i', $t);
    } else {
        return date('Y.m.d H:i', $t);
    }
}


/**
 * Получаем картинки из тем
 */
function thm($path = '')
{
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

    require_once DIR . '/PEAR/Tar.php';

    $thm = new Archive_Tar($path);


    if (!$file = $thm->extractInString(pathinfo($path, PATHINFO_FILENAME).'.xml')) {
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


    // fix bug in tar.php
    if (!$file) {
    	preg_match('/<\?\s*xml\s*version\s*=\s*"1\.0"\s*\?>(.*)<\/.+>/isU', file_get_contents($path), $arr);
    	$file = trim($arr[0]);
    }


    $load = simplexml_load_string($file);

    if ($load->Author_organization['Value']) {
    	$str .= $_SESSION['language']['author'] . ': ' . htmlspecialchars($load->Author_organization['Value'], ENT_NOQUOTES) . '<br/>';
    }

    if ($load['version']) {
    	$str .= $_SESSION['language']['version'] . ': ' . htmlspecialchars($load['version'], ENT_NOQUOTES) . '<br/>';

    	if (in_array($load['version'], array_keys($ver_thm))) {
    		$str .= $_SESSION['language']['models'] . ': ' . $ver_thm[(string)$load['version']] . '<br/>';
    	}            
    }

    return $str;
}


/**
 * Упрощенный ресайзер картинок
 */
function simple_resize ($data)
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
 */
function img_resize($in = '', $out = '', $w = '', $h = '', $marker = false)
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

    switch ($type) {
        case 1:
            if ($GLOBALS['setup']['anim_change']) {
                ini_set('memory_limit', '256M');

                // GIF Поддержка анимации    
                require_once DIR . '/GIFDecoder.class.php';
                require_once DIR . '/GIFEncoder.class.php';

                $gif = new GIFDecoder(file_get_contents($in));

                $arr = $gif->GIFGetFrames();
                $dly = $gif->GIFGetDelays();
                $frames = $framed = array();

                $a = sizeof($arr);
                for ($i = 0; $i < $a; ++$i) {
            		$tmp1 = DIR . '/cache/' . mt_rand() . '.gif';
            		$tmp2 = DIR . '/cache/' . mt_rand() . '.gif';

            		file_put_contents($tmp1, $arr[$i]);
                	$resize = imagecreatefromgif($tmp1);

                	$image_p = imagecreatetruecolor($w, $h);
                	imagecopyresampled($image_p, $resize, 0, 0, 0, 0, $w, $h, $wn, $hn);


                	if ($marker) {
            			$image_p = marker($image_p, imagecreatefrompng(DIR . '/marker.png'));
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
            return;
            break;


        case 6:
            // BMP
            require_once DIR . '/bmp.php';
            $old = imagecreatefrombmp($in, DIR . '/cache/');
            break;


        default:
            return;
            break;
    }



    $new = imagecreatetruecolor($w, $h);
    imagecopyresampled($new, $old, 0, 0, 0, 0, $w, $h, $wn, $hn);

    if ($marker) {
    	$new = marker($new, imagecreatefrompng(DIR . '/marker.png'));
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
    require_once DIR . '/PEAR/pclzip.lib.php';

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
	require_once DIR . '/header.php';
	echo '<div class="no">' . $str . '</div><div class="iblock">- <a href="javascript:history.back();">' . $_SESSION['language']['back'] . '</a><br/>- <a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/>- <a href="' . $GLOBALS['setup']['site_url'] . '">' . $_SESSION['language']['home'] . '</a><br/></div>';
	require_once DIR . '/foot.php';
	exit;
}


/**
 * Возвращает случайный пароль
 * 
 * @return string random password 6-8 symbols
 */
function pass($min = 6, $max = 8)
{
	return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWZYZ0123456789'), 0, mt_rand($min, $max));
}


/**
 * Конвертируем из неизвоестной кодировки в UTF-8
 * 
 * @param string $str
 * @return string
 */
function str_to_utf8($str)
{
	if (@iconv('UTF-8', 'UTF-8//IGNORE', $str) != $str) {
		$str = iconv('Windows-1251', 'UTF-8//TRANSLIT', $str);
	}
    return $str;
}


/**
 * Постраничная навигация
 * 
 * @param int       текущая страница
 * @param int       всего страниц
 * @param string    url страницы, к нему прибавится /номер
 * @return string   html с навигацией
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
        return;
    } else {
        return '<div class="row">&#160;' . $go . '</div>';
    }
}


/**
 * Возвращает MIME файла по его расширению
 * 
 * @param  string   расширение
 * @return string   MIME тип
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
    }
}

?>
