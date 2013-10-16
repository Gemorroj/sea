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
class Template extends Smarty
{
    private $_template = 'index.tpl';


    /**
     * Конструктор
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTemplateDir(CORE_DIRECTORY . '/Smarty/templates/')
            ->setCompileDir(CORE_DIRECTORY . '/Smarty/templates_c/')
            ->setConfigDir(CORE_DIRECTORY . '/Smarty/configs/')
            ->setCacheDir(CORE_DIRECTORY . '/Smarty/cache/')
            ->registerPlugin('modifier', 'dateFormatExtended', array($this, 'dateFormatExtended'))
            ->registerPlugin('modifier', 'sizeFormatExtended', array($this, 'sizeFormatExtended'))
            ->registerPlugin('modifier', 'bbcode', array($this, 'bbcode'))
            ->registerPlugin('function', 'paginationExtended', array($this, 'paginationExtended'))
            ->registerPlugin('function', 'getStyle', array($this, 'getStyle'))
            ->loadFilter('variable', 'htmlspecialchars');

        $this->compile_check = false;
    }


    /**
     * Задаем шаблон для выдачи
     *
     * @param string $template имя файла шаблона
     *
     * @return Template
     */
    public function setTemplate($template)
    {
        $this->_template = $template;

        return $this;
    }


    /**
     * Выдача шаблона в выходной поток
     */
    public function send()
    {
        $this->assign('pageTime', microtime(true) - START_TIME);
        $this->display($this->_template);
        exit;
    }


    /**
     * Форматирование даты
     * Функция форматирования даты для Smarty
     *
     * @param string $str
     *
     * @return string
     */
    public function dateFormatExtended($str)
    {
        if (date('Y.m.d', $str) === date('Y.m.d', $_SERVER['REQUEST_TIME'])) {
            return Language::get('today') . ' ' . date('H:i', $str);
        } else {
            if (date('Y.m.d', $str) === date('Y.m.d', $_SERVER['REQUEST_TIME'] - 86400)) {
                return Language::get('yesterday') . ' ' . date('H:i', $str);
            } else {
                return date('Y.m.d H:i', $str);
            }
        }
    }


    /**
     * Форматирование размера
     * функция форматирования размера для Smarty
     *
     * @param int $int
     *
     * @return string
     */
    public function sizeFormatExtended($int = 0)
    {
        if ($int < 1024) {
            return $int . 'b';
        } else {
            if ($int < 1048576) {
                return round($int / 1024, 2) . 'Kb';
            } else {
                if ($int < 1073741824) {
                    return round($int / 1048576, 2) . 'Mb';
                } else {
                    return round($int / 1073741824, 2) . 'Gb';
                }
            }
        }
    }


    /**
     * CSS стиль (костыль для поддержки различных протоколов)
     * функция получения CSS стиля для Smarty
     *
     * @param array                    $params   parameters (style - путь к CSS стилю без http://)
     * @param Smarty_Internal_Template $template template object
     *
     * @return string      путь к CSS стилю
     */
    public function getStyle($params, $template)
    {
        if (parse_url('http://' . $params['style'], PHP_URL_HOST) === Http_Request::getHost()) {
            return substr($params['style'], strlen(Http_Request::getHost()));
        } else {
            return 'http://' . $params['style'];
        }
    }


    /**
     * Постраничная навигация
     * функция постраничной навигации для Smarty
     *
     * @param array                    $params   parameters (page - текущая страница, pages - всего страниц, url - url страницы, к нему прибавится /номер)
     * @param Smarty_Internal_Template $template template object
     *
     * @return string      html с навигацией
     */
    public function paginationExtended($params, $template)
    {
        $params['page'] = intval($params['page']);
        $params['pages'] = intval($params['pages']);
        $params['url'] = htmlspecialchars($params['url']);

        $appendStr = isset($params['query']) ? '?' . http_build_query($params['query'], '', '&amp;') : '';


        $go = '';

        $page1 = $params['page'] - 2;
        $page2 = $params['page'] - 1;
        $page3 = $params['page'] + 1;
        $page4 = $params['page'] + 2;

        if ($page1 > 0) {
            $go .= '<a href="' . $params['url'] . '/' . $page1 . $appendStr . '">' . $page1 . '</a> ';
        }

        if ($page2 > 0) {
            $go .= '<a href="' . $params['url'] . '/' . $page2 . $appendStr . '">' . $page2 . '</a> ';
        }

        $go .= '[' . $params['page'] . '] ';

        if ($page3 <= $params['pages']) {
            $go .= '<a href="' . $params['url'] . '/' . $page3 . $appendStr . '">' . $page3 . '</a> ';
        }
        if ($page4 <= $params['pages']) {
            $go .= '<a href="' . $params['url'] . '/' . $page4 . $appendStr . '">' . $page4 . '</a> ';
        }

        if ($params['pages'] > 3 && $params['pages'] > $page4) {
            $go .= '... <a href="' . $params['url'] . '/' . $params['pages'] . $appendStr . '">' . $params['pages'] . '</a>';
        }

        if ($page1 > 1) {
            $go = '<a href="' . $params['url'] . '/1' . $appendStr . '">1</a> ... ' . $go;
        }

        if (Config::get('pagehand_change') && $params['pages'] > Config::get('pagehand')) {
            $page = str_replace(
                array('%page%', '%pages%'),
                array($params['page'], $params['pages']),
                Language::get('page')
            );
            $hiddens = '';
            if (isset($params['query'])) {
                foreach ($params['query'] as $key => $value) {
                    $hiddens .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '"/>';
                }
            }

            $go .= '<br/>
            <form action="' . $params['url'] . '" method="get">
                <div class="iblock">
                    ' . $hiddens . '
                    <label>' . $page . ':<br/>
                        <input class="enter" name="page" type="number" maxlength="8" size="8" required="required" min="1" max="65536"/>
                    </label>
                    <input class="buttom" type="submit" value="' . Language::get('go') . '"/>
                </div>
            </form>';
        }

        return $go != '[' . $params['page'] . '] ' ? '<nav class="row">' . $go . '</nav>' : '';
    }


    /**
     * Ббкод в html
     *
     * @param string $str
     *
     * @return string
     */
    public function bbcode($str)
    {
        $str = htmlspecialchars($str);

        $bbcode = array(
            '/\[url\](.+)\[\/url\]/isU' => '<a href="$1">$1</a>',
            '/\[url=(.+)\](.+)\[\/url\]/isU' => '<a href="$1">$2</a>',
            '/\[i\](.+)\[\/i\]/isU' => '<em>$1</em>',
            '/\[b\](.+)\[\/b\]/isU' => '<strong>$1</strong>',
            '/\[u\](.+)\[\/u\]/isU' => '<span style="text-decoration:underline;">$1</span>',
            '/\[big\](.+)\[\/big\]/isU' => '<span style="font-size:large;">$1</span>',
            '/\[small\](.+)\[\/small\]/isU' => '<span style="font-size:small;">$1</span>',
            '/\[code\](.+)\[\/code\]/isU' => '<code>$1</code>',
            '/\[red\](.+)\[\/red\]/isU' => '<span style="color:#ff0000;">$1</span>',
            '/\[yellow\](.+)\[\/yellow\]/isU' => '<span style="color:#ffff22;">$1</span>',
            '/\[green\](.+)\[\/green\]/isU' => '<span style="color:#00bb00;">$1</span>',
            '/\[blue\](.+)\[\/blue\]/isU' => '<span style="color:#0000bb;">$1</span>',
            '/\[white\](.+)\[\/white\]/isU' => '<span style="color:#ffffff;">$1</span>',
            '/\[color=(.+)\](.+)\[\/color\]/isU' => '<span style="color:$1;">$2</span>',
            '/\[size=([0-9]+)\](.+)\[\/size\]/isU' => '<span style="font-size:$1px;">$2</span>',
            '/\[img\](.+)\[\/img\]/isU' => '<img src="$1" alt=""/>',
            '/\[br\]/isU' => '<br />'
        );

        return preg_replace(array_keys($bbcode), array_values($bbcode), $str);
    }
}
