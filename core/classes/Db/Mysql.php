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
class Db_Mysql extends PDO
{
    /**
     * @var Db_Mysql
     */
    private static $_instance;
    /**
     * @var array
     */
    private static $_options = array(
        'host' => 'localhost',
        'dbname' => 'sea',
        'username' => 'root',
        'password' => '',
    );


    /**
     * @return Db_Mysql
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Constructor
     * Use only for new connection
     */
    public function __construct()
    {
        parent::__construct(
            'mysql:charset=utf8;host=' . self::$_options['host'] . ';dbname=' . self::$_options['dbname'],
            self::$_options['username'],
            self::$_options['password']
        );
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // fix for old php
        if (true === version_compare(PHP_VERSION, '5.3.6', '<')) {
            $this->exec('SET NAMES utf8');
        }
    }


    /**
     * @param array $options
     */
    public static function init(array $options)
    {
        self::$_options = $options;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        return self::$_options;
    }


    /**
     * Фильтрация данных для LIKE запросов
     *
     * @param string $str
     * @return string
     */
    public function escapeLike ($str)
    {
        return str_replace(array('%', '_'), array('\%', '\_'), $str);
    }
}
