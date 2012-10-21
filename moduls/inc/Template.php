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
 * @author Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Template extends Smarty
{
    private $_template = 'index.tpl';


    /**
     * Конструктор
     */
    final public function __construct()
    {
        parent::__construct();

        $this->setTemplateDir(DIR . '/Smarty/templates/')
            ->setCompileDir(DIR . '/Smarty/templates_c/')
            ->setConfigDir(DIR . '/Smarty/configs/')
            ->setCacheDir(DIR . '/Smarty/cache/')
            ->registerPlugin('modifier', 'dateFormatExtended', array($this, 'dateFormatExtended'))
            ->registerPlugin('modifier', 'sizeFormatExtended', array($this, 'sizeFormatExtended'))
            ->registerPlugin('function', 'paginationExtended', array($this, 'paginationExtended'))
            ->loadFilter('variable', 'htmlspecialchars');

        $this->compile_check = true;
    }


    /**
     * Задаем шаблон для выдачи
     *
     * @param string $template имя файла шаблона
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
    public function send ()
    {
        $this->display($this->_template);
    }


    /**
     * Форматирование даты
     * Функция форматирования даты для Smarty
     *
     * @param string $str
     * @return string
     */
    public function dateFormatExtended($str)
    {
        $language = Language::getInstance()->getLanguage();

        if (date('Y.m.d', $str) == date('Y.m.d', $_SERVER['REQUEST_TIME'])) {
            return $language['today'] . ' ' . date('H:i', $str);
        } else if (date('Y.m.d', $str) == date('Y.m.d', $_SERVER['REQUEST_TIME'] - 86400)) {
            return $language['yesterday'] . ' ' . date('H:i', $str);
        } else {
            return date('Y.m.d H:i', $str);
        }
    }


    /**
     * Форматирование размера
     * функция форматирования размера для Smarty
     *
     * @param int $int
     * @return string
     */
    public function sizeFormatExtended($int = 0)
    {
        if ($int < 1024) {
            return $int . 'b';
        } else if ($int < 1048576) {
            return round($int / 1024, 2) . 'Kb';
        } else if ($int < 1073741824) {
            return round($int / 1048576, 2) . 'Mb';
        } else {
            return round($int / 1073741824, 2) . 'Gb';
        }
    }


    /**
     * Постраничная навигация
     * функция постраничной навигации для Smarty
     *
     * @param array                    $params   parameters (page - текущая страница, pages - всего страниц, url - url страницы, к нему прибавится /номер)
     * @param Smarty_Internal_Template $template template object
     * @return string      html с навигацией
     */
    public function paginationExtended($params, $template)
    {
        $language = Language::getInstance()->getLanguage();

        $params['page'] = intval($params['page']);
        $params['pages'] = intval($params['pages']);
        $params['url'] = htmlspecialchars($params['url']);

        $go = '';

        $page1 = $params['page'] - 2;
        $page2 = $params['page'] - 1;
        $page3 = $params['page'] + 1;
        $page4 = $params['page'] + 2;

        if ($page1 > 0) {
            $go .= '<a href="' . $params['url'] . '/' . $page1 . '">' . $page1 . '</a> ';
        }

        if ($page2 > 0) {
            $go .= '<a href="' . $params['url'] . '/' . $page2 . '">' . $page2 . '</a> ';
        }

        $go .= $params['page'] . ' ';

        if ($page3 <= $params['pages']) {
            $go .= '<a href="' . $params['url'] . '/' . $page3 . '">' . $page3 . '</a> ';
        }
        if ($page4 <= $params['pages']) {
            $go .= '<a href="' . $params['url'] . '/' . $page4 . '">' . $page4 . '</a> ';
        }

        if ($params['pages'] > 3 && $params['pages'] > $page4) {
            $go .= '... <a href="' . $params['url'] . '/' . $params['pages'] . '">' . $params['pages'] . '</a>';
        }

        if ($page1 > 1) {
            $go = '<a href="' . $params['url'] . '/1">1</a> ... ' . $go;
        }

        if ($GLOBALS['setup']['pagehand_change'] && $params['pages'] > $GLOBALS['setup']['pagehand']) {
            $go .= '<br/>' . str_replace(array('%page%', '%pages%'), array($params['page'], $params['pages']), $language['page']) . ':<br/><form action="' . $params['url'] . '" method="get"><div class="row"><input class="enter" name="page" type="text" maxlength="8" size="8"/> <input class="buttom" type="submit" value="' . $language['go'] . '"/></div></form>';
        }

        return $go != $params['page'] . ' ' ? '<div class="row">&#160;' . $go . '</div>' : '';
    }
}

?>
