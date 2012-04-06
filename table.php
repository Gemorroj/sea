<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#             	 Автор  :  Sea                        #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#   		По всем вопросам пишите в ICQ.            #
#-----------------------------------------------------#

// mod Gemorroj

require 'moduls/config.php';
require 'moduls/header.php';
###############Если стол выключен###############
if (!$setup['zakaz_change']) {
	error('Not found');
}

$title .= $language['orders'];

###############Проверка переменных###############
if (!isset($_POST['text']) || !isset($_POST['back'])) {
echo '<div class="mblock"><strong>' . $language['orders'] . '</strong></div>
<div class="row">
<form method="post" action="' . $_SERVER['PHP_SELF'] . '?">
<div class="row">
' . $language['inform administration'] . '<br/>
<textarea class="enter" name="text" rows="2" cols="24"></textarea><br/>
' . $language['how do you contact'] . '<br/>
<input class="enter" type="text" name="back" value="" maxlength="500"/><br/>
<input class="buttom" type="submit" name="send" value="' . $language['go'] . '"/>
</div>
</form></div>';
} else {
    if (empty($_POST['back']) || empty($_POST['text'])) {
    	error($language['do not fill in the required fields']);
    }
    $headers = 'Content-Type: text/plain; charset=utf-8' . "\r\n" . 'From: support@' . $_SERVER['HTTP_HOST'];
    $text = 'СООБЩЕНИЕ: ' . $_POST['text'] . ' ОБРАТНЫЙ АДРЕС: ' . $_POST['back'];
    if (mail($setup['zakaz_email'], '=?utf-8?B?' . base64_encode('Заказ из загруз центра') . '?=', $text, $headers)) {
        echo '<div class="mblock"><strong>' . $language['orders'] . '</strong></div>' . $language['message sent successfully'] . '<br/>';
    } else {
    	echo '<div class="mblock"><strong>' . $language['orders'] . '</strong></div>' . $language['message not sent'] . '<br/>';
    }
}
echo '<div class="iblock">
- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>
- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a></div>';

require 'moduls/foot.php';

?>
