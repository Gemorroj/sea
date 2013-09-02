<?php

if (Config::get('style_change')) {
    if (Http_Request::post('style')) {
        $stylePost = Helper::removeSchema(Http_Request::post('style'));
        if ($stylePost && Helper::isValidStyle($stylePost)) {
            $style = $stylePost;
            setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
        }
    } elseif (Http_Request::get('style')) {
        $styleGet = Helper::removeSchema(Http_Request::get('style'));
        if ($styleGet && Helper::isValidStyle($styleGet)) {
            $style = $styleGet;
            setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);
        }
    } elseif (Http_Request::cookie('style')) {
        $styleCookie = Helper::removeSchema(Http_Request::cookie('style'));
        if ($styleCookie && Helper::isValidStyle($styleCookie)) {
            $style = $styleCookie;
        }
    } else {
        $style = isset($_SESSION['style']) ? $_SESSION['style'] : $_SERVER['HTTP_HOST'] . DIRECTORY . 'style/' . Config::get('css') . '.css';
    }
} else {
    $style = $_SERVER['HTTP_HOST'] . DIRECTORY . 'style/' . Config::get('css') . '.css';
}
