<?php

$style = null;
if (Config::get('style_change')) {
    if (Http_Request::post('style')) {
        $stylePost = Helper::removeSchema(Http_Request::post('style'));
        if ($stylePost && Helper::isValidStyle($stylePost)) {
            $style = $stylePost;
            setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, SEA_PUBLIC_DIRECTORY, Http_Request::getHost(), false, true);
        }
    } elseif (Http_Request::get('style')) {
        $styleGet = Helper::removeSchema(Http_Request::get('style'));
        if ($styleGet && Helper::isValidStyle($styleGet)) {
            $style = $styleGet;
            setcookie('style', $style, $_SERVER['REQUEST_TIME'] + 86400000, SEA_PUBLIC_DIRECTORY, Http_Request::getHost(), false, true);
        }
    } elseif (Http_Request::cookie('style')) {
        $styleCookie = Helper::removeSchema(Http_Request::cookie('style'));
        if ($styleCookie && Helper::isValidStyle($styleCookie)) {
            $style = $styleCookie;
        }
    } else {
        $style = isset($_SESSION['style']) ? $_SESSION['style'] : Http_Request::getHost() . SEA_PUBLIC_DIRECTORY . 'style/' . Config::get('css') . '.css';
    }
}

if (!$style) {
    $style = Http_Request::getHost() . SEA_PUBLIC_DIRECTORY . 'style/' . Config::get('css') . '.css';
}
