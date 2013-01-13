<?php

if ($setup['style_change']) {
    if (isset($_POST['style']) && parse_url($_POST['style']) && strpos($_POST['style'], '.')) {
        $style = ltrim($_POST['style'], 'http://');
        setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
    } else {
        if (isset($_GET['style']) && parse_url($_GET['style']) && strpos($_GET['style'], '.')) {
            $style = ltrim($_GET['style'], 'http://');
            setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
        } else {
            if (isset($_COOKIE['style']) && parse_url($_COOKIE['style']) && strpos($_COOKIE['style'], '.')) {
                $style = ltrim($_COOKIE['style'], 'http://');
            } else {
                if (isset($_SESSION['style'])) {
                    $style = $_SESSION['style'];
                } else {
                    $style = $_SERVER['HTTP_HOST'] . DIRECTORY . 'style/' . $setup['css'] . '.css';
                }
            }
        }
    }
} else {
    $style = $_SERVER['HTTP_HOST'] . DIRECTORY . 'style/' . ($setup['css'] ? $setup['css'] : 'style') . '.css';
}
