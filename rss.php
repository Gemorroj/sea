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


header('Content-Type: application/rss+xml; charset=UTF-8');

require 'moduls/config.php';
define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));

$link = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'news.php';

class rss2 extends DOMDocument
{
    private $_channel;


    public function __construct($title, $link, $description)
    {
        parent::__construct('1.0', 'UTF-8');
        //$this->formatOutput = true;

        $root = $this->createElement('rss');
        $root->setAttribute('version', '0.92');
        $rss = $this->appendChild($root);

        $this->_channel = $rss->appendChild($this->createElement('channel'));

        $this->_channel->appendChild($this->createElement('title', $title));
        $this->_channel->appendChild($this->createElement('link', $link));
        $this->_channel->appendChild($this->createElement('description', $description));
    }


    public function addItem($title, $link, $description)
    {
        $item = $this->createElement('item');
        $item->appendChild($this->createElement('title', $title));
        $item->appendChild($this->createElement('link', $link));
        $item->appendChild($this->createElement('description', $description));
        $this->_channel->appendChild($item);
    }
}


$rss = new rss2($language['news'], $link, $language['news']);


$q = mysql_query('
    SELECT ' . Language::getInstance()->buildNewsQuery() . ',
    `time`
    FROM `news`
    ORDER BY `id` DESC
    LIMIT 0, 10
', $mysql);


while ($arr = mysql_fetch_assoc($q)) {
    $rss->addItem(
        'Новость - ' . date('Y.m.d H:i', $arr['time']),
        $link,
        '<div>' . $arr['news'] . '</div>'
    );
}

echo trim($rss->saveXML());
