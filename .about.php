<?php
// mod Gemorroj


//error_reporting(0);
set_time_limit(9999);
header('Content-type: text/html; charset=utf-8');
require 'moduls/config.php';


echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
<title>Updater</title>
</head>
<body>
<div>';


$act = isset($_GET['act']) ? $_GET['act'] : '';
switch ($act) {
    default:
        echo '<fieldset>
        <legend>Перенос описаний из БД в файлы</legend>
        <a href="' . $_SERVER['PHP_SELF'] . '?act=about">Перенести</a>
        </fieldset>
        <fieldset>
        <legend>Рассортировка описаний из 1 папки в требуемую структуру</legend>
        <form action="' . $_SERVER['PHP_SELF'] . '?act=sort_about" method="post">
        <div>
        Папка с описаниями:<br/><input type="text" name="about_folder" value="' . dirname(__FILE__) . '"/> <input type="submit"/>
        </div>
        </form>
        </fieldset>
        <fieldset>
        <legend>Рассортировка скриншотов из 1 папки в требуемую структуру</legend>
        <form action="' . $_SERVER['PHP_SELF'] . '?act=sort_screen" method="post">
        <div>
        Папка со скриншотами:<br/><input type="text" name="screen_folder" value="' . dirname(__FILE__) . '"/> <input type="submit"/>
        </div>
        </form>
        </fieldset>';
    break;
    
    
    case 'about':
        $q = mysql_query('SELECT `dir`, `path`, `about` FROM `files`', $mysql);
        
        $i = 0;
        while ($file = mysql_fetch_assoc($q)) {
            $about = mb_substr($file['path'], mb_strlen($setup['path']));
        
            if ($file['dir']) {
                mkdir($setup['opath'] . mb_substr($about, 0, -mb_strlen(strrchr($about, '/'))), 0777, true);
            }
        
            if (trim($file['about'])) {
                if (file_put_contents($setup['opath'] . $about . '.txt', $file['about'])) {
                    $i++;
                } else {
                    echo 'Error - '.$about.'<br/>';
                }
            }
        }
        echo 'Готово. Создано <strong>' . $i . '</strong> файлов описаний';
    break;


    case 'sort_about':
        $i = 0;
        foreach (scandir($_POST['about_folder']) as $f) {
            $ext = strtolower(pathinfo($_POST['about_folder'] . '/' . $f, PATHINFO_EXTENSION));
            if ($ext == 'txt' && is_file($_POST['about_folder'] . '/' . $f)) {
                $name = substr($f, 0, -4); // отрезаем .txt
                $path = mysql_query('SELECT `path` FROM `files` WHERE SUBSTR(`path`, -' . mb_strlen($name) . ') = "' . mysql_real_escape_string($name, $mysql) . '"');
                if (mysql_num_rows($path)) {
                    
                    $about = mysql_result($path, 0);
                    $about = mb_substr($about, mb_strlen($setup['path']));

                    mkdir($setup['opath'] . mb_substr($about, 0, -mb_strlen(strrchr($about, '/'))), 0777, true);

                    if (rename($_POST['about_folder'] . '/' . $f, $setup['opath'] . $about . '.' . $ext)) {
                        $i++;
                    } else {
                        echo 'Error - ' . $f . '<br/>';
                    }
                   
                }
            }
        }
        echo 'Готово. Перемещено <strong>' . $i . '</strong> файлов описаний';
    break;
    
    
    case 'sort_screen':
        $i = 0;
        foreach (scandir($_POST['screen_folder']) as $f) {
            $ext = strtolower(pathinfo($_POST['screen_folder'] . '/' . $f, PATHINFO_EXTENSION));
            if (($ext == 'gif' || $ext == 'jpg') && is_file($_POST['screen_folder'] . '/' . $f)) {
                
                $name = substr($f, 0, -4); // отрезаем расширение
                
                $path = mysql_query('SELECT `path` FROM `files` WHERE SUBSTR(`path`, -' . mb_strlen($name) . ') = "' . mysql_real_escape_string($name, $mysql) . '"');
                if (mysql_num_rows($path)) {
                    
                    $about = mysql_result($path, 0);
                    $about = mb_substr($about, mb_strlen($setup['path']));

                    mkdir($setup['spath'] . mb_substr($about, 0, -mb_strlen(strrchr($about, '/'))), 0777, true);

                    if (rename($_POST['screen_folder'] . '/' . $f, $setup['spath'] . $about . '.' . $ext)) {
                        $i++;
                    } else {
                        echo 'Error - ' . $f . '<br/>';
                    }
                   
                }
            }
        }
        echo 'Готово. Перемещено <strong>' . $i . '</strong> скриншотов';
    break;    
}



echo '</div></body></html>';

?>
