<?php

if (Config::get('style_change')) {
    $stylePost = Http_Request::post('style');
    if ($stylePost && parse_url($stylePost) && strpos($stylePost, '.')) {
        $style = preg_replace('/^(?:.*:\/\/)/', '', $stylePost);
        setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
    } else {
        $styleGet = Http_Request::get('style');
        if (isset($styleGet) && parse_url($styleGet) && strpos($styleGet, '.')) {
            $style = preg_replace('/^(?:.*:\/\/)/', '', $styleGet);
            setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
        } else {
            $styleCookie = Http_Request::cookie('style');
            if (isset($styleCookie) && parse_url($styleCookie) && strpos($styleCookie, '.')) {
                $style = preg_replace('/^(?:.*:\/\/)/', '', $styleCookie);
            } else {
                if (isset($_SESSION['style'])) {
                    $style = $_SESSION['style'];
                } else {
                    $style = $_SERVER['HTTP_HOST'] . DIRECTORY . 'style/' . Config::get('css') . '.css';
                }
            }
        }
    }
} else {
    $style = $_SERVER['HTTP_HOST'] . DIRECTORY . 'style/' . (Config::get('css') ? Config::get('css') : 'style') . '.css';
}
