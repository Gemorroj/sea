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


require 'core/header.php';
###############Если стол выключен###############
if (!$setup['zakaz_change']) {
    error('Not found');
}

$template->setTemplate('table.tpl');

$seo['title'] = $language['orders'];


$sended = false;
if ($_POST) {
    if (empty($_POST['back']) || empty($_POST['text'])) {
        error($language['do_not_fill_in_the_required_fields']);
    }
    if ($setup['comments_captcha']) {
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']) {
            unset($_SESSION['captcha_keystring']);
            error($language['not_a_valid_code']);
        }
        unset($_SESSION['captcha_keystring']);
    }

    $sended = mail(
        $setup['zakaz_email'],
        '=?utf-8?B?' . base64_encode('Заказ из загруз центра') . '?=',
        'СООБЩЕНИЕ: ' . $_POST['text'] . "\n\n" . ' ОБРАТНЫЙ АДРЕС: ' . $_POST['back'],
        'Content-Type: text/plain; charset=utf-8' . "\r\n" . 'From: support@' . $_SERVER['HTTP_HOST']
    );
}

$template->assign('sended', $sended);
$template->assign('breadcrumbs', array('table' => $language['orders']));
$template->send();
