<?php
// mod Gemorroj


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

?>
