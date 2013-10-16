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


require_once CORE_DIRECTORY . '/header.php';

if (!Config::get('abuse_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

$id = intval(Http_Request::get('id'));
$v = Files::getFileInfo($id);

if (!$v || !is_file($v['path'])) {
    Http_Response::getInstance()->renderError(Language::get('not_found'));
}

Seo::unserialize($v['seo']);
//Seo::addTitle(Language::get('complain_about_a_file'));
//Seo::addTitle($v['name']);

Breadcrumbs::init($v['path']);
Breadcrumbs::add('abuse/' . $id, Language::get('complain_about_a_file'));

if (mail(
    Config::get('zakaz_email'),
    '=?utf-8?B?' . base64_encode('Жалоба на файл') . '?=',
    'Получена жалоба на файл ' . Helper::getUrl() . DIRECTORY . 'view/' . $id . "\r\n" .
    'Браузер: ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n" .
    'IP: ' . $_SERVER['REMOTE_ADDR'],
    "From: robot@" . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
)) {
    Http_Response::getInstance()->renderMessage(Language::get('complaint_sent_to_the_administration'));
} else {
    Http_Response::getInstance()->renderError(Language::get('sending_email_error_occurred'));
}
