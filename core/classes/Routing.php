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
class Routing
{
    protected static $_instance;
    protected static $_rules = array();


    private function __construct()
    {

    }

    /**
     * @return Routing
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param array $rules
     */
    public static function init(array $rules)
    {
        self::$_rules = $rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return self::$_rules;
    }

    /**
     * @return string
     */
    protected function _getPath()
    {
        $queryString = Http_Request::getQueryString();
        $queryStringLength = strlen($queryString);
        $requestUri = Http_Request::getRequestUri();

        if ($queryStringLength > 0) {
            $requestUri = substr($requestUri, 0, -($queryStringLength + 1));
        }

        return $requestUri;
    }

    /**
     * @return int
     */
    public function handle()
    {
        foreach ($this->getRules() as $regexp => $path) {
            $matches = null;
            if (preg_match('#^' . DIRECTORY . $regexp . '/*$#', $this->_getPath(), $matches)) {
                foreach ($matches as $key => $value) {
                    if (false === is_int($key)) {
                        Http_Request::addGet($key, $value);
                    }
                }
                return include CORE_DIRECTORY . '/controllers/' . $path;
                break;
            }
        }
        return 0;
    }
}
