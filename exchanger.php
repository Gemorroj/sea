<?php
/**
 * @author Gemorroj
 * @copyright 2009
 */

require 'moduls/config.php';
require 'moduls/header.php';


if (!$setup['exchanger_change']) {
	error('Not found');
}


if ($_POST && $_FILES['file']) {

    if ($_FILES['file']['error']) {
        error($language['when downloading a file error occurred']);
    }

    $pathinfo = pathinfo($_FILES['file']['name']);

    $ext = explode(',', strtolower($setup['exchanger_extensions']));
    if (!in_array(strtolower($pathinfo['extension']), $ext)) {
        error($language['invalid file extension']);
    }

    if (!preg_match('/^' . $setup['exchanger_name'] . '+$/i', $pathinfo['filename'])) {
        error($language['not a valid file name']);
    }

    $path = mysql_result(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . intval($_POST['topath']) . ' AND `dir` = "1" AND `hidden` = "0"', $mysql), 0);

    if (!$path) {
        error($language['you have specified the correct path to load']);
    }
    $pathname = $path . $_FILES['file']['name'];

    $q = mysql_query('SELECT 1 FROM `files` WHERE `path` = "' . mysql_real_escape_string($pathname, $mysql) . '"', $mysql);
    if (mysql_num_rows($q)) {
        error($language['file with this name already exists']);
    }

    if (!move_uploaded_file($_FILES['file']['tmp_name'], $pathname)) {
        error($language['an error occurred while copying files']);
    }

    $q = mysql_query("
        INSERT INTO `files` (
            `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`, `hidden`
        ) VALUES (
            '" . mysql_real_escape_string($pathname, $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($pathinfo['filename'], $mysql) . "',
            '" . mysql_real_escape_string($path, $mysql) . "',
            " . filesize($pathname) . ",
            " . filectime($pathname) . ",
            '" . ($setup['exchanger_hidden'] ? 1 : 0) . "'
        )", $mysql
    );
    if (!$q) {
        unlink($pathname);
        error($language['error writing to database']);
    }
    $id = mysql_insert_id($mysql);

    if (!$setup['exchanger_hidden']) {
        $tmp = '<br/><a href="' . DIRECTORY . 'view/' . $id . '">http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'view/' . $id . '</a>';
        dir_count($path, true);
    } else {
        $tmp = '';
    }

    if (!$_FILES['screen']['error']) {
        $screen = $setup['spath'] . substr($pathname, strlen($setup['path'])) . '.gif';
        $image = getimagesize($_FILES['screen']['tmp_name']);
        switch ($image[2]) {
            case 1: // GIF
                img_resize($_FILES['screen']['tmp_name'], $screen, 0, 0, $setup['marker']);
                break;


            case 2: // JPEG
                $im = imagecreatefromjpeg($_FILES['screen']['tmp_name']);
                imagegif($im, $screen);
                imagedestroy($im);
                img_resize($_FILES['screen']['tmp_name'], $screen, 0, 0, $setup['marker']);
                break;


            case 3: //PNG
                $im = imagecreatefrompng($_FILES['screen']['tmp_name']);
                imagegif($im, $screen);
                imagedestroy($im);
                img_resize($_FILES['screen']['tmp_name'], $screen, 0, 0, $setup['marker']);
                break;
        }
    }

    if ($_POST['about']) {
        $about = $setup['opath'] . substr($pathname, strlen($setup['path'])) . '.txt';
        file_put_contents($about, nl2br(bbcode(htmlspecialchars(trim($_POST['about'])))));
    }

    if ($setup['exchanger_notice']) {
        mail(
            $setup['zakaz_email'],
            '=?utf-8?B?' . base64_encode('Новый файл') . '?=',
            'Загружен новый файл http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'apanel_view.php?id=' . $id . "\r\n" .
            'Браузер: ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n" .
            'IP: ' . $_SERVER['REMOTE_ADDR'],
            "From: robot@".$_SERVER['HTTP_HOST']."\r\nContent-type: text/plain; charset=UTF-8"
        );
    }

    echo '<div class="yes">' . $language['file successfully added'] . $tmp .'</div>';
} else {
    echo '<div class="mainzag">' . $language['upload file'] . '</div><form action="exchanger.php" method="post" enctype="multipart/form-data"><div class="row">' . $language['save'] . '<select class="buttom" name="topath">';

    $in = array();
    $dirs = mysql_query('SELECT `id`, `path` FROM `files` WHERE `dir` = "1" AND `hidden` = "0"', $mysql);
    while ($item = mysql_fetch_assoc($dirs)) {
        $arr = explode('/', $item['path']);
        $all = sizeof($arr) - 1;
        $in = array();
        for ($i = 0; $i < $all; ++$i) {
            if ($i > 0) {
                $in[$i] = $in[$i - 1] . mysql_real_escape_string($arr[$i], $mysql) . '/';
            } else {
                $in[$i] = mysql_real_escape_string($arr[$i], $mysql) . '/';
            }
        }

        $names = '';
        $q = mysql_query('SELECT ' . Language::getInstance()->buildFilesQuery() . ' FROM `files` WHERE `path` IN ("' . implode('","', $in) . '")', $mysql);
        while ($arrn = mysql_fetch_assoc($q)) {
            $names .= $arrn['name'] . '/';
        }

    	echo '<option value="' . $item['id'] . '">' . htmlspecialchars($names, ENT_NOQUOTES) . '</option>';
    }
    echo '</select><br/>' . $language['file'] . ' (' . htmlspecialchars($setup['exchanger_name'] . ' / ' . $setup['exchanger_extensions'], ENT_NOQUOTES) . ' / ' . ini_get('upload_max_filesize') . ')<br/><input type="file" name="file" class="enter"/><br/>' . $language['screenshot'] . ' (jpeg,gif,png)<br/><input type="file" name="screen" class="enter"/><br/>' . $language['description'] . '<br/><textarea class="enter" cols="24" rows="2" name="about"></textarea><br/><br/><input class="buttom" type="submit" value="' . $language['go'] . '"/></div></form>';
}

echo '<div class="iblock">- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a></div>';

require 'moduls/foot.php';

?>
