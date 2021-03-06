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


require_once SEA_CORE_DIRECTORY . '/header.php';

if (!Config::get('zakaz_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

//Seo::addTitle(Language::get('orders'));
Breadcrumbs::add('table', Language::get('orders'));

$sentEmail = false;
if (Http_Request::isPost()) {
    if (!Http_Request::post('back') || !Http_Request::post('text')) {
        Http_Response::getInstance()->renderError(Language::get('do_not_fill_in_the_required_fields'));
    }
    if (Config::get('comments_captcha')) {
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != Http_Request::post('keystring')) {
            unset($_SESSION['captcha_keystring']);
            Http_Response::getInstance()->renderError(Language::get('not_a_valid_code'));
        }
        unset($_SESSION['captcha_keystring']);
    }

    $sentEmail = Helper::sendEmail(
        Config::get('zakaz_email'),
        'Заказ из загруз-центра',
        'СООБЩЕНИЕ: ' . Http_Request::post('text') . "\r\n" . 'ОБРАТНЫЙ АДРЕС: ' . Http_Request::post('back')
    );
}

Http_Response::getInstance()->getTemplate()
    ->setTemplate('table.tpl')
    ->assign('sentEmail', $sentEmail);

Http_Response::getInstance()->render();
