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

$GLOBALS['tm'] = microtime(true);

require_once dirname(__FILE__) . '/config.php';

header('Content-type: text/html; charset=utf-8');


if (defined('APANEL')) {
    define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname(dirname($_SERVER['PHP_SELF'])) . '/'));
} else {
    define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));
}


// заменяем языковой пакет
isset($_POST['langpack']) && Language::getInstance()->setLangpack($_POST['langpack']);
isset($_GET['langpack']) && Language::getInstance()->setLangpack($_GET['langpack']);
$language = Language::getInstance()->getLanguage();


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$seo = array();


require DIR . '/inc/_style.php';
require DIR . '/inc/_buy.php';
require DIR . '/inc/_online.php';
require DIR . '/inc/_service.php';


$template = new Template();

$template->assignByRef('seo', $seo);
$template->assign('setup', $setup);
$template->assign('style', $style);
$template->assign('language', $language);
$template->assign('id', $id);
$template->assign('buy', $buy);
$template->assign('banner', $banner);
$template->assign('serviceBuy', $serviceBuy);
$template->assign('serviceBanner', $serviceBanner);
$template->assign('online', $online);
