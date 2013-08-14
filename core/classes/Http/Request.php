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
    protected static $_ip;
    protected static $_host;
    protected static $_userAgent;


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
        self::$_ip = $_SERVER['REMOTE_ADDR'];
        self::$_host = $_SERVER['HTTP_HOST'];
        self::$_userAgent = $_SERVER['HTTP_USER_AGENT'];
    }


    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        return self::$_get[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function post($key)
    {
        return self::$_post[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function cookie($key)
    {
        return self::$_cookies[$key];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public static function file($key)
    {
        return self::$_files[$key];
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
     * @return static string
     */
    public static function getUserAgent()
    {
        return self::$_userAgent;
    }
}
