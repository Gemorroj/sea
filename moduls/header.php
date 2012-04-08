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


$GLOBALS['tm'] = microtime(true);

header('Content-type: text/html; charset=utf-8');
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');
header('Last-Modified: ' . gmdate('r') . ' GMT');
header('Cache-Control: post-check=0, pre-check=0');
header('Pragma: no-cache');


define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));


if (isset($_POST['langpack']) && Language::getInstance()->setLangpack($_POST['langpack'])) {
    // заменяем языковой пакет
    $language = Language::getInstance()->getLanguage();
}


if ($setup['service_change']) {
    if (isset($_GET['url'])) {
        $_SESSION['site_url'] = $setup['site_url'] = 'http://' . htmlspecialchars($_GET['url']);
    } else if (isset($_SESSION['site_url'])) {
        $setup['site_url'] = $_SESSION['site_url'];
    }
}

if ($setup['style_change']) {
    if (isset($_POST['style']) && parse_url($_POST['style'])) {
        $style = htmlspecialchars(rawurldecode($_POST['style']), ENT_COMPAT);
        setcookie('style', $_POST['style'], $_SERVER['REQUEST_TIME'] + 2592000, DIRECTORY, $_SERVER['HTTP_HOST']);
    } else if (isset($_GET['style']) && parse_url($_GET['style'])) {
        $style = htmlspecialchars(rawurldecode($_GET['style']), ENT_COMPAT);
        setcookie('style', $_GET['style'], $_SERVER['REQUEST_TIME'] + 2592000, DIRECTORY, $_SERVER['HTTP_HOST']);
    } else if (isset($_COOKIE['style']) && parse_url($_COOKIE['style'])) {
        $style = htmlspecialchars($_COOKIE['style'], ENT_COMPAT);
    } else if (isset($_SESSION['style'])) {
        $style = $_SESSION['style'];
    } else {
        $style = $_SERVER['HTTP_HOST'] . DIRECTORY . $setup['css'] . '.css';
    }
} else {
    $style = $_SERVER['HTTP_HOST'] . DIRECTORY . ($setup['css'] ? $setup['css'] : 'style') . '.css';
}



// функция замены title, keywords, description в буфере
function callback($buffer)
{
    if ($GLOBALS['title']) {
        $buffer = str_replace('</title>', ' - ' . $GLOBALS['title'] . '</title>', $buffer);
    }
    if ($GLOBALS['seo']) {
        if ($GLOBALS['seo']['keywords']) {
            $buffer = str_replace('</title>', '</title><meta name="keywords" content="' . htmlspecialchars($GLOBALS['seo']['keywords']) . '"/>', $buffer);
        }
        if ($GLOBALS['seo']['description']) {
            $buffer = str_replace('</title>', '</title><meta name="description" content="' . htmlspecialchars($GLOBALS['seo']['description']) . '"/>', $buffer);
        }
    }

    return $buffer;
}

ob_start('callback');
echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="alternate" type="application/rss+xml" href="http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'rss.php"/>
<link rel="stylesheet" type="text/css" href="http://' . $style . '"/>
<title>' . $setup['zag'] . '</title>
</head>
<body>
<div>';

$title = '';
$seo = array();

?>
