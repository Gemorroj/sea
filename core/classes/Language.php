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
    private $_langpack;
    private $_language;
    private $_langpacks = array();
    private $_dbFilesCorrelation
        = array(
            'name' => 'english',
            'rus_name' => 'russian',
            'aze_name' => 'azerbaijan',
            'tur_name' => 'turkey',
        );
    private $_dbNewsCorrelation
        = array(
            'news' => 'english',
            'rus_news' => 'russian',
            'aze_news' => 'azerbaijan',
            'tur_news' => 'turkey',
        );

    static private $_instance;


    final private function __construct()
    {
        $this->_loadLangpacks();
        $this->_load();
    }


    /**
     * Получение экземпляра класса
     *
     * @return Language
     */
    static public function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * Задаем языковой пакет
     *
     * @param string $langpack
     *
     * @return bool
     */
    public function setLangpack($langpack)
    {
        if ($langpack && in_array($langpack, $this->getLangpacks())) {
            $this->_langpack = $_SESSION['langpack'] = $langpack;
            $this->_language = include CORE_DIRECTORY . '/resources/language/' . $this->_langpack . '.dat';

            return true;
        }

        return false;
    }


    /**
     * Загружаем языковой пакет
     */
    private function _load()
    {
        global $setup;

        if (!isset($_SESSION['langpack']) || !in_array($_SESSION['langpack'], $this->getLangpacks())) {
            // язык по умолчанию
            $this->_langpack = $setup['langpack'];
        } else {
            $this->_langpack = $_SESSION['langpack'];
        }
        $this->_language = include CORE_DIRECTORY . '/resources/language/' . $this->_langpack . '.dat';
    }


    /**
     * Список доступных языковых пактов
     */
    private function _loadLangpacks()
    {
        foreach (glob(CORE_DIRECTORY . '/resources/language/*.dat') as $v) {
            $this->_langpacks[] = pathinfo($v, PATHINFO_FILENAME);
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
    public function buildFilesQuery($prefix = null, $name = 'name')
    {
        $prefix = ($prefix === null ? '' : '`' . $prefix . '`.');

        $key = array_search($this->getLangpack(), $this->_dbFilesCorrelation);
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
    public function buildNewsQuery($prefix = null, $name = 'news')
    {
        $prefix = ($prefix === null ? '' : '`' . $prefix . '`.');

        $key = array_search($this->getLangpack(), $this->_dbNewsCorrelation);
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
    public function getLangpacks()
    {
        return $this->_langpacks;
    }


    /**
     * Название языкового пакета
     *
     * @return string
     */
    public function getLangpack()
    {
        return $this->_langpack;
    }


    /**
     * Языковой пакет
     *
     * @return array
     */
    public function getLanguage()
    {
        return $this->_language;
    }
}
