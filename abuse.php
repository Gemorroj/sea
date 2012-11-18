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


require 'moduls/header.php';

// Если жалобы выключены
if (!$setup['abuse_change']) {
    error('Not found');
}

// Получаем инфу о файле
$v = getFileInfo($id);

if (!is_file($v['path'])) {
    error('File not found');
}

$seo['title'] = $language['complain_about_a_file'] . ' - ' . $v['name'];
$template->assign(
    'breadcrumbs',
    array(
        'view/' . $id => $v['name'],
        'abuse/' . $id => $language['complain_about_a_file']
    )
);

if (mail(
    $setup['zakaz_email'],
    '=?utf-8?B?' . base64_encode('Жалоба на файл') . '?=',
    'Получена жалоба на файл http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'view/' . $id . "\r\n" .
        'Браузер: ' . $_SERVER['HTTP_USER_AGENT'] . "\r\n" .
        'IP: ' . $_SERVER['REMOTE_ADDR'],
    "From: robot@" . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
)) {
    message($language['complaint_sent_to_the_administration']);
} else {
    error($language['sending_email_error_occurred']);
}
