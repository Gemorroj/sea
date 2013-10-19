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
class Autoload
{
    private static $_autoload;


    /**
     * Конструктор
     */
    private function __construct()
    {
        set_include_path(
            SEA_CORE_DIRECTORY . DIRECTORY_SEPARATOR . 'PEAR' . PATH_SEPARATOR . get_include_path()
        );

        spl_autoload_register(array($this, '_classes'));
        spl_autoload_register(array($this, '_smarty'));
        spl_autoload_register(array($this, '_pear'));
    }


    /**
     * Инициализация
     */
    public static function init()
    {
        if (null === self::$_autoload) {
            self::$_autoload = new self();
        }
    }


    /**
     * @param string $class
     */
    protected function _classes($class)
    {
        $this->_include(SEA_CORE_DIRECTORY . '/classes/' . str_replace('_', '/', $class) . '.php');
    }


    /**
     * @param string $class
     */
    protected function _smarty($class)
    {
        $this->_include(SEA_CORE_DIRECTORY . '/Smarty/libs/' . $class . '.class.php');
    }


    /**
     * @param string $class
     */
    protected function _pear($class)
    {
        $this->_include(SEA_CORE_DIRECTORY . '/PEAR/' . str_replace('_', '/', $class) . '.php');
    }


    /**
     * @param string $file
     */
    protected function _include($file)
    {
        if (true === is_file($file)) {
            include $file;
        }
    }
}
