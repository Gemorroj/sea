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
class Http_Request
{
    protected static $_post = array();
    protected static $_get = array();
    protected static $_cookies = array();
    protected static $_files = array();
    protected static $_queryString;
    protected static $_requestUri;
    protected static $_ip;
    protected static $_host;
    protected static $_userAgent;
    protected static $_referer;


    /**
     * Инициализация
     */
    public static function init()
    {
        self::$_post = $_POST;
        self::$_get = $_GET;
        self::$_cookies = $_COOKIE;
        self::$_files = $_FILES;
        self::$_queryString = $_SERVER['QUERY_STRING'];
        self::$_requestUri = $_SERVER['REQUEST_URI'];
        self::$_ip = $_SERVER['REMOTE_ADDR'];
        self::$_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        self::$_userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        self::$_referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }


    /**
     * @return bool
     */
    public static function isPost()
    {
        return ('POST' === $_SERVER['REQUEST_METHOD']);
    }


    /**
     * @return bool
     */
    public static function isGet()
    {
        return ('GET' === $_SERVER['REQUEST_METHOD']);
    }


    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return array_key_exists($key, self::$_get) ? self::$_get[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function post($key, $default = null)
    {
        return array_key_exists($key, self::$_post) ? self::$_post[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function cookie($key, $default = null)
    {
        return array_key_exists($key, self::$_cookies) ? self::$_cookies[$key] : $default;
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function file($key, $default = null)
    {
        return array_key_exists($key, self::$_files) ? self::$_files[$key] : $default;
    }

    /**
     * @return array
     */
    public static function getCookies()
    {
        return self::$_cookies;
    }

    /**
     * @return array
     */
    public static function getFiles()
    {
        return self::$_files;
    }

    /**
     * @return array
     */
    public static function getGet()
    {
        return self::$_get;
    }

    /**
     * @return array
     */
    public static function getPost()
    {
        return self::$_post;
    }

    /**
     * @return string
     */
    public static function getHost()
    {
        return self::$_host;
    }

    /**
     * @return string
     */
    public static function getIp()
    {
        return self::$_ip;
    }

    /**
     * @return string
     */
    public static function getQueryString()
    {
        return self::$_queryString;
    }

    /**
     * @return string
     */
    public static function getRequestUri()
    {
        return self::$_requestUri;
    }

    /**
     * @return static string|null
     */
    public static function getUserAgent()
    {
        return self::$_userAgent;
    }

    /**
     * @return static string|null
     */
    public static function getReferer()
    {
        return self::$_referer;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public static function addGet($key, $value)
    {
        self::$_get[$key] = $value;
    }


    /**
     * Определяем бот это или нет
     *
     * @return bool
     */
    public static function isCrawler()
    {
        $engines = array(
            'Aport', //Aport
            'Google', //Google
            'msnbot', //MSN
            'Rambler', //Rambler
            'Yahoo', //Yahoo
            'Yandex', //Yandex
            'AbachoBOT', //AbachoBOT
            'accoona', //Accoona
            'AcoiRobot', //AcoiRobot
            'ASPSeek', //ASPSeek
            'CrocCrawler', //CrocCrawler
            'Dumbot', //Dumbot
            'FAST-WebCrawler', //FAST-WebCrawler
            'GeonaBot', //GeonaBot
            'Gigabot', //Gigabot
            'Lycos', //Lycos spider
            'MSRBOT', //MSRBOT
            'Scooter', //Altavista robot
            'AltaVista', //Altavista robot
            'WebAlta', //WebAlta
            'IDBot', //ID-Search Bot
            'eStyle', //eStyle Bot
            'Mail.Ru', //Mail.Ru Bot
            'Scrubby', //Scrubby robot
        );

        $userAgent = self::getUserAgent();
        foreach ($engines as $engine) {
            if (stristr($userAgent, $engine)) {
                return true;
            }
        }

        return false;
    }
}
