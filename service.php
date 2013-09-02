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


if (!Config::get('service_change_advanced')) {
    Http_Response::getInstance()->renderError('Not found');
}

$template = Http_Response::getInstance()->getTemplate();
$template->setTemplate('service.tpl');

//Seo::addTitle(Language::get('advanced_service'));
Breadcrumbs::add('service', Language::get('advanced_service'));

$db = Db_Mysql::getInstance();
$act = Http_Request::get('act');

if ($act == 'enter' && Http_Request::get('id') && Http_Request::get('pass')) {
    $q = $db->prepare('
        SELECT *
        FROM `users_profiles`
        WHERE `id` = ?
        AND `pass` = MD5(?)
    ');
    $q->execute(array(Http_Request::get('id'), Http_Request::get('pass')));

    if ($q->rowCount() > 0) {
        $assoc = $q->fetch();
        $_SESSION['id'] = $assoc['id'];
        $_SESSION['name'] = $assoc['name'];
        $_SESSION['url'] = $assoc['url'];
        $_SESSION['mail'] = $assoc['mail'];
        $_SESSION['style'] = $assoc['style'];

        Http_Response::getInstance()->redirect('http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'service');
    } else {
        Http_Response::getInstance()->renderError(Language::get('user_not_found'));
    }
} elseif ($act == 'registration') {
    if (Http_Request::isPost()) {
        $style = Helper::removeSchema(Http_Request::post('style'));
        $url = Helper::removeSchema(Http_Request::post('url'));
        $pass = Http_Request::post('pass');
        $mail = Http_Request::post('mail');
        $name = Http_Request::post('name');

        $error = array();
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != Http_Request::post('keystring')) {
            $error[] = Language::get('not_a_valid_code');
        }
        unset($_SESSION['captcha_keystring']);

        if (strlen($pass) < 4) {
            $error[] = Language::get('short_password');
        }

        if (!Helper::isValidUrl($url)) {
            $error[] = Language::get('not_a_valid_url');
        }

        if (!Helper::isValidStyle($style)) {
            $error[] = Language::get('not_a_valid_style');
        }

        if (!Helper::isValidEmail($mail)) {
            $error[] = Language::get('not_a_valid_mail');
        }

        if ($error) {
            Http_Response::getInstance()->renderError($error);
        }


        $q = $db->prepare('SELECT 1 FROM `users_profiles` WHERE `url` = ?');
        $q->execute(array($url));

        if ($q->rowCount() > 0) {
            // Такой URL уже есть
            Http_Response::getInstance()->renderError(Language::get('duplicate_url'));
        } else {
            $result = $db->prepare('
                INSERT INTO `users_profiles` (
                    `name`, `url`, `pass`, `mail`, `style`
                ) VALUES (
                    ?, ?, MD5(?), ?, ?
                )
            ')->execute(array(
                $name,
                $url,
                $pass,
                $mail,
                $style
            ));

            if ($result) {
                $_SESSION['id'] = $db->lastInsertId();
                $_SESSION['name'] = $name;
                $_SESSION['url'] = $url;
                $_SESSION['mail'] = $mail;
                $_SESSION['style'] = $style;

                mail(
                    $mail,
                    '=?utf-8?B?' . base64_encode('Registration in ' . $_SERVER['HTTP_HOST'] . DIRECTORY) . '?=',
                    'Your password: ' . $pass . "\r\n" . 'ID: ' . $_SESSION['id'],
                    'From: robot@' . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
                );

                Http_Response::getInstance()->redirect('http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'service');
            } else {
                Http_Response::getInstance()->renderError(Language::get('error'));
            }
        }
    }
} elseif ($act == 'pass') {
    $q = $db->prepare('SELECT `mail` FROM `users_profiles` WHERE `id` = ?');
    $q->execute(array(Http_Request::post('id')));
    $mail = $q->fetchColumn();

    if ($mail) {
        $pass = Helper::getRandPass();
        $db->prepare('UPDATE `users_profiles` SET `pass` = MD5(?) WHERE `id` = ?')->execute(array($pass, Http_Request::post('id')));

        mail(
            $mail,
            '=?utf-8?B?' . base64_encode('Change Password ' . $_SERVER['HTTP_HOST'] . DIRECTORY) . '?=',
            'Your new password: ' . $pass . "\r\n" . 'ID: ' . Http_Request::get('id'),
            'From: robot@' . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
        );

        Http_Response::getInstance()->renderMessage(Language::get('email_sent_successfully'));
    } else {
        Http_Response::getInstance()->renderError(Language::get('email_not_found'));
    }
} else {
    if (isset($_SESSION['id'])) {
        // если пользователь вошел в кабинет
        switch ($act) {
            default:
                $head = $foot = array();

                $q = $db->prepare('
                    SELECT `name`, `value`
                    FROM `users_settings`
                    WHERE `parent_id` = ?
                    AND `position` = ?
                    LIMIT ?
                ');

                if (Config::get('service_head')) {
                    $q->bindValue(1, $_SESSION['id'], PDO::PARAM_INT);
                    $q->bindValue(2, '0');
                    $q->bindValue(3, intval(Config::get('service_head')), PDO::PARAM_INT);
                    $q->execute();

                    $head = $q->fetchAll();
                    $l = sizeof($head);
                    if ($l < Config::get('service_head')) {
                        for ($i = $l; $i < Config::get('service_head'); ++$i) {
                            $head[] = array('name' => '', 'value' => '');
                        }
                    }
                }
                if (Config::get('service_foot')) {
                    $q->bindValue(1, $_SESSION['id'], PDO::PARAM_INT);
                    $q->bindValue(2, '1');
                    $q->bindValue(3, intval(Config::get('service_foot')), PDO::PARAM_INT);
                    $q->execute();

                    $foot = $q->fetchAll();
                    $l = sizeof($foot);
                    if ($l < Config::get('service_foot')) {
                        for ($i = $l; $i < Config::get('service_foot'); ++$i) {
                            $foot[] = array('name' => '', 'value' => '');
                        }
                    }
                }

                $template->assign('head', $head);
                $template->assign('foot', $foot);
                break;


            case 'save':
                $style = Helper::removeSchema(Http_Request::post('style'));
                $url = Helper::removeSchema(Http_Request::post('url'));
                $name = Http_Request::post('name');
                $mail = Http_Request::post('mail');

                $_SESSION['url'] = $url;
                $_SESSION['name'] = $name;
                $_SESSION['mail'] = $mail;


                $db->prepare('
                    UPDATE `users_profiles`
                    SET `name` = ?,
                    `url` = ?,
                    `mail` = ?,
                    `style` = ?
                    WHERE `id` = ?
                ')->execute(array(
                    $name,
                    $url,
                    $mail,
                    $style,
                    $_SESSION['id']
                ));

                $db->prepare('DELETE FROM `users_settings` WHERE `parent_id` = ?')->execute(array($_SESSION['id']));

                $q = $db->prepare('
                    INSERT INTO `users_settings` (
                        `parent_id`, `position`, `name`, `value`
                    ) VALUES (
                        ?, ?, ?, ?
                    )
                ');

                $head = Http_Request::post('head');
                $all = sizeof($head['name']);
                $all = $all < Config::get('service_head') ? $all : Config::get('service_head');
                for ($i = 0; $i < $all; ++$i) {
                    $name = $head['name'][$i];
                    $value = Helper::removeSchema($head['value'][$i]);
                    if ($name && $value) {
                        $q->execute(array(
                            $_SESSION['id'],
                            '0',
                            $name,
                            $value
                        ));
                    }
                }

                $foot = Http_Request::post('foot');
                $all = sizeof($foot['name']);
                $all = $all < Config::get('service_foot') ? $all : Config::get('service_foot');
                for ($i = 0; $i < $all; ++$i) {
                    $name = $foot['name'][$i];
                    $value = Helper::removeSchema($foot['value'][$i]);
                    if ($name && $value) {
                        $q->execute(array(
                             $_SESSION['id'],
                             '1',
                             $name,
                             $value
                        ));
                    }
                }

                //$db->exec('OPTIMIZE TABLE `users_profiles`, `users_settings`');
                //$db->exec('ANALYZE TABLE `users_profiles`, `users_settings`');

                Http_Response::getInstance()->renderMessage(Language::get('settings_saved'));
                break;


            case 'exit':
                session_destroy();
                Http_Response::getInstance()->renderError(Language::get('signed_out'));
                break;
        }
    }
}

Http_Response::getInstance()->render();
