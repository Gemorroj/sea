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
class Http_Response
{
    /**
     * @var Http_Response
     */
    private static $_instance;

    /**
     * @var Template
     */
    protected $_template;
    /**
     * @var array
     */
    protected $_headers = array('Content-Type' => 'text/html; charset=UTF-8');
    /**
     * @var string
     */
    protected $_body = '';


    /**
     * Конструктор
     */
    private function __construct(Template $template)
    {
        $this->_template = $template;
    }


    /**
     * Инициализация
     */
    public static function init(Template $template)
    {
        if (null === self::$_instance) {
            self::$_instance = new self($template);
        }
    }

    /**
     * @return Http_Response
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @param string $body
     *
     * @return Http_Response
     */
    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return Http_Response
     */
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $value;
        return $this;
    }

    /**
     * Отправляем HTTP заголовки
     */
    protected function _renderHeaders()
    {
        foreach ($this->_headers as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    /**
     * Вывод в браузер
     */
    public function render()
    {
        $this->_renderHeaders();
        $this->_template->send();
    }

    /**
     * Вывод в браузер
     */
    public function renderBinary()
    {
        $this->_renderHeaders();
        echo $this->_body;
        exit;
    }

    /**
     * Отображение сообщений
     */
    public function renderMessage($str = '')
    {
        include_once SEA_CORE_DIRECTORY . '/header.php';
        $template = $this->getTemplate();

        $template
            ->setTemplate('message.tpl')
            ->assign('message', is_array($str) ? $str : array($str));

        $this->render();
        exit;
    }

    /**
     * Отображение ошибок
     */
    public function renderError($str = '')
    {
        include_once SEA_CORE_DIRECTORY . '/header.php';
        $template = $this->getTemplate();

        $template
            ->setTemplate('error.tpl')
            ->assign('message', is_array($str) ? $str : array($str));

        $this->render();
        exit;
    }

    /**
     * Переадресация
     *
     * @param string $url
     * @param int $code
     */
    public function redirect($url, $code = 302)
    {
        $this->getTemplate()->setTemplate('redirect.tpl')->assign('url', $url);

        $this->_renderHeaders();
        header('Location: ' . $url, true, $code);

        $this->_template->send();
        exit;
    }


    /**
     * HTTP кэширование
     *
     * @param int $expires
     * @return Http_Response
     */
    public function setCache($expires = 8640000)
    {
        $this->setHeader('Pragma', 'public')
            ->setHeader('Cache-Control', 'public, max-age=' . $expires)
            ->setHeader('Expires', date('r', time() + $expires));

        return $this;
    }


    /**
     * HTTP кэширование
     *
     * @return Http_Response
     */
    public function setNoCache()
    {
        $this->setHeader('Pragma', 'no-cache')
            ->setHeader('Cache-Control', 'no-cache, must-revalidate, no-store')
            ->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT');

        return $this;
    }
}
