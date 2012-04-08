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
