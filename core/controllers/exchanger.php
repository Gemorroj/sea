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

if (!Config::get('exchanger_change')) {
    Http_Response::getInstance()->renderError(Language::get('not_available'));
}

//Seo::addTitle(Language::get('upload_file'));
Breadcrumbs::add('exchanger', Language::get('upload_file'));

$dirs = array();
$insertId = null;

if (Http_Request::isPost()) {
    $db = Db_Mysql::getInstance();
    $file = Http_Request::file('file');
    $screen = Http_Request::file('screen');

    if (!$file || $file['error']) {
        Http_Response::getInstance()->renderError(Language::get('when_downloading_a_file_error_occurred'));
    }

    $pathinfo = pathinfo($file['name']);
    $path = Config::get('path') . Http_Request::post('topath');
    $pathname = $path . $file['name'];

    $ext = explode(',', strtolower(Config::get('exchanger_extensions')));
    if (!in_array(strtolower($pathinfo['extension']), $ext)) {
        Http_Response::getInstance()->renderError(Language::get('invalid_file_extension'));
    }

    if (!preg_match('/^' . Config::get('exchanger_name') . '+$/i', $pathinfo['filename'])) {
        Http_Response::getInstance()->renderError(Language::get('not_a_valid_file_name'));
    }


    $q = $db->prepare('
        SELECT 1
        FROM `files`
        WHERE `path` = ?
        AND `dir` = "1"
        ' . (SEA_IS_ADMIN !== true ? 'AND `hidden` = "0"' : '')
    );
    $q->execute(array($path));

    if (!$q || $q->rowCount() < 1) {
        Http_Response::getInstance()->renderError(Language::get('you_have_specified_the_correct_path_to_load'));
    }


    $q = $db->prepare('SELECT 1 FROM `files` WHERE `path` = ?');
    $q->execute(array($pathname));

    if ($q->rowCount() > 0) {
        Http_Response::getInstance()->renderError(Language::get('file_with_this_name_already_exists'));
    }

    if (!move_uploaded_file($file['tmp_name'], $pathname)) {
        Http_Response::getInstance()->renderError(Language::get('an_error_occurred_while_copying_files'));
    }

    $q = $db->prepare('
        INSERT INTO `files` (
            `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`, `hidden`
        ) VALUES ("0", ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $result = $q->execute(array(
        $pathname,
        $pathinfo['filename'],
        $pathinfo['filename'],
        $pathinfo['filename'],
        $pathinfo['filename'],
        $path,
        filesize($pathname),
        filectime($pathname),
        (Config::get('exchanger_hidden') ? 1 : 0)
    ));

    if (!$result) {
        unlink($pathname);
        Http_Response::getInstance()->renderError(Language::get('error_writing_to_database'));
    }
    $insertId = $db->lastInsertId();
    if (!Config::get('exchanger_hidden')) {
        Files::updateDirCount($path, true);
    }


    if ($screen && !$screen['error']) {
        $screenPath = Config::get('spath') . substr($pathname, strlen(Config::get('path'))) . '.png';

        if (!move_uploaded_file($screen['tmp_name'], $screenPath)) {
            Http_Response::getInstance()->renderError(Language::get('error'));
        }

        Image::resize($screenPath, $screenPath, 0, 0, Config::get('marker'));
    }

    if (Http_Request::post('about')) {
        $about = Config::get('opath') . substr($pathname, strlen(Config::get('path'))) . '.txt';
        file_put_contents($about, trim(Http_Request::post('about')));
    }

    if (Config::get('exchanger_notice')) {
        Helper::sendEmail(
            Config::get('zakaz_email'),
            'Новый файл',
            'Загружен новый файл: ' . Helper::getUrl() . SEA_PUBLIC_DIRECTORY . 'view/' . $insertId . "\r\n" .
            'Браузер: ' . Http_Request::getUserAgent() . "\r\n" .
            'IP: ' . Http_Request::getIp()
        );
    }
} else {
    $dirs = Files::getAllDirs();
}

Http_Response::getInstance()->getTemplate()
    ->setTemplate('exchanger.tpl')
    ->assign('insertId', $insertId)
    ->assign('upload_max_filesize', ini_get('upload_max_filesize'))
    ->assign('dirs', $dirs);

Http_Response::getInstance()->render();
