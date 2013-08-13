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
class Language
{
    private static $_langpack;
    private static $_language;
    private static $_langpacks = array();
    private static $_dbFilesCorrelation
        = array(
            'name' => 'english',
            'rus_name' => 'russian',
            'aze_name' => 'azerbaijan',
            'tur_name' => 'turkey',
        );
    private static $_dbNewsCorrelation
        = array(
            'news' => 'english',
            'rus_news' => 'russian',
            'aze_news' => 'azerbaijan',
            'tur_news' => 'turkey',
        );

    static private $_instance;


    private function __construct()
    {
        $this->_loadLangpacks();
        $this->_load();
    }


    /**
     * Инициализация
     */
    public static function init()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
    }


    /**
     * Задаем языковой пакет
     *
     * @param string $langpack
     *
     * @return bool
     */
    public static function setLangpack($langpack)
    {
        if ($langpack && in_array($langpack, self::getLangpacks())) {
            self::$_langpack = $_SESSION['langpack'] = $langpack;
            self::$_language = include CORE_DIRECTORY . '/resources/language/' . self::$_langpack . '.dat';

            return true;
        }

        return false;
    }


    /**
     * Загружаем языковой пакет
     */
    private function _load()
    {
        if (!isset($_SESSION['langpack']) || !in_array($_SESSION['langpack'], self::getLangpacks())) {
            // язык по умолчанию
            self::$_langpack = Config::get('langpack');
        } else {
            self::$_langpack = $_SESSION['langpack'];
        }
        self::$_language = include CORE_DIRECTORY . '/resources/language/' . self::$_langpack . '.dat';
    }


    /**
     * Список доступных языковых пактов
     */
    private function _loadLangpacks()
    {
        foreach (glob(CORE_DIRECTORY . '/resources/language/*.dat') as $v) {
            self::$_langpacks[] = pathinfo($v, PATHINFO_FILENAME);
        }
    }


    /**
     * Строка запроса для файлов
     *
     * @param string $prefix
     * @param string $name
     *
     * @return string
     */
    public static function buildFilesQuery($prefix = null, $name = 'name')
    {
        $prefix = ($prefix === null ? '' : '`' . $prefix . '`.');

        $key = array_search(self::getLangpack(), self::$_dbFilesCorrelation);
        if ($key !== false) {
            return $prefix . '`' . $key . '` AS `' . $name . '`';
        }

        return $prefix . '`' . $name .'`';
    }


    /**
     * Строка запроса для новостей
     *
     * @param string $prefix
     * @param string $name
     *
     * @return string
     */
    public static function buildNewsQuery($prefix = null, $name = 'news')
    {
        $prefix = ($prefix === null ? '' : '`' . $prefix . '`.');

        $key = array_search(self::getLangpack(), self::$_dbNewsCorrelation);
        if ($key !== false) {
            return $prefix . '`' . $key . '` AS `' . $name .'`';
        }

        return $prefix . '`' . $name .'`';
    }


    /**
     * Название языковых пакетов
     *
     * @return array
     */
    public static function getLangpacks()
    {
        return self::$_langpacks;
    }


    /**
     * Название языкового пакета
     *
     * @return string
     */
    public static function getLangpack()
    {
        return self::$_langpack;
    }


    /**
     * Языковой пакет
     *
     * @return array
     */
    public static function getLanguage()
    {
        return self::$_language;
    }


    /**
     * Перевод
     *
     * @param string $key
     *
     * @return string
     */
    public static function get($key)
    {
        return self::$_language[$key];
    }
}
