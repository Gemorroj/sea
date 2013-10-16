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

if (Http_Request::post('lib')) {
    $_SESSION['lib'] = intval(Http_Request::post('lib'));
}

//Seo::addTitle(Language::get('settings'));
Breadcrumbs::add('settings', Language::get('settings'));

$sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : '';
$onpage = isset($_SESSION['onpage']) ? $_SESSION['onpage'] : '';
$prev = isset($_SESSION['prev']) ? $_SESSION['prev'] : '';
$lib = isset($_SESSION['lib']) ? $_SESSION['lib'] :Config::get('lib');

Http_Response::getInstance()->getTemplate()
    ->setTemplate('settings.tpl')
    ->assign('sort', $sort)
    ->assign('onpage', $onpage)
    ->assign('prev', $prev)
    ->assign('lib', $lib)
    ->assign('langpack', Language::getLangpack())
    ->assign('langpacks', Language::getLangpacks())
    ->assign('styles', glob('style/*.css', GLOB_NOESCAPE));

Http_Response::getInstance()->render();
