<?php

class MysqlDb extends PDO
{
    /**
     * @var MysqlDb
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
     * @return MysqlDb
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
    public static function setOptions(array $options)
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
