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


if (!$setup['service_change_advanced']) {
    error('Not found');
}


$template->setTemplate('service.tpl');

$seo['title'] = $language['advanced_service'];


if (isset($_GET['act']) && $_GET['act'] == 'enter' && isset($_GET['id']) && isset($_GET['pass'])) {
    $q = mysql_query(
        '
        SELECT *
        FROM `users_profiles`
        WHERE `id` = ' . intval($_GET['id']) . '
        AND `pass` = "' . md5($_GET['pass']) . '"'
        ,
        $mysql
    );

    if (mysql_num_rows($q)) {
        $assoc = mysql_fetch_assoc($q);
        $_SESSION['id'] = $assoc['id'];
        $_SESSION['name'] = $assoc['name'];
        $_SESSION['url'] = $assoc['url'];
        $_SESSION['mail'] = $assoc['mail'];
        $_SESSION['style'] = $assoc['style'];

        header('Location: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'service');
        exit;
    } else {
        error($language['user_not_found']);
    }
} elseif (isset($_GET['act']) && $_GET['act'] == 'registration') {
    if ($_POST) {
        $_POST['style'] = ltrim($_POST['style'], 'http://');
        $_POST['url'] = ltrim($_POST['url'], 'http://');

        $error = array();
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']) {
            $error[] = $language['not_a_valid_code'];
        }
        unset($_SESSION['captcha_keystring']);

        if (strlen($_POST['pass']) < 4) {
            $error[] = $language['short_password'];
        }

        if (strlen($_POST['url']) < 4 || !strpos($_POST['url'], '.')) {
            $error[] = $language['not_a_valid_url'];
        }

        if (strlen($_POST['style']) < 4 || !strpos($_POST['style'], '.')) {
            $error[] = $language['not_a_valid_style'];
        }

        if (strlen($_POST['mail']) < 4 || !strpos($_POST['mail'], '@')) {
            $error[] = $language['not_a_valid_mail'];
        }

        if ($error) {
            error($error);
        }

        $url = mysql_real_escape_string($_POST['url'], $mysql);
        $pass = md5($_POST['pass']);
        $mail = mysql_real_escape_string($_POST['mail'], $mysql);
        $name = mysql_real_escape_string($_POST['name'], $mysql);
        $style = mysql_real_escape_string($_POST['style'], $mysql);

        if (mysql_num_rows(
            mysql_query(
                '
            SELECT 1
            FROM `users_profiles`
            WHERE `url` = "' . $url . '"'
                ,
                $mysql
            )
        )
        ) {
            // Такой URL уже есть
            error($language['duplicate_url']);
        } else {
            if (mysql_query(
                'INSERT INTO `users_profiles` SET `name` = "' . $name . '", `url` = "' . $url . '", `pass` = "' . $pass
                    . '", `mail` = "' . $mail . '", `style` = "' . $style . '"',
                $mysql
            )
            ) {
                $_SESSION['id'] = mysql_insert_id($mysql);
                $_SESSION['name'] = $_POST['name'];
                $_SESSION['url'] = $_POST['url'];
                $_SESSION['mail'] = $_POST['mail'];
                $_SESSION['style'] = $_POST['style'];

                mail(
                    $mail,
                    '=?utf-8?B?' . base64_encode('Registration in ' . $_SERVER['HTTP_HOST'] . DIRECTORY) . '?=',
                    'Your password: ' . $_POST['pass'] . "\r\n" . 'ID: ' . $_SESSION['id'],
                    'From: robot@' . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
                );

                header('Location: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . 'service');
                exit;
            } else {
                error($language['error']);
            }
        }
    }
} elseif (isset($_GET['act']) && $_GET['act'] == 'pass') {
    $id = intval($_POST['id']);
    $q = mysql_query('SELECT `mail` FROM `users_profiles` WHERE `id` = ' . $id, $mysql);
    if (mysql_num_rows($q) && $mail = mysql_result($q, 0)) {
        $pass = pass();
        mysql_query('UPDATE `users_profiles` SET `pass` = "' . md5($pass) . '" WHERE `id` = ' . $id, $mysql);
        mail(
            $mail,
            '=?utf-8?B?' . base64_encode('Change Password ' . $_SERVER['HTTP_HOST'] . DIRECTORY) . '?=',
            'Your new password: ' . $pass . "\r\n" . 'ID: ' . $id,
            'From: robot@' . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8"
        );

        message($language['email_sent_successfully']);
    } else {
        error($language['email_not_found']);
    }
} else {
    if (isset($_SESSION['id'])) {
        // если пользователь вошел в кабинет
        $act = isset($_GET['act']) ? $_GET['act'] : '';

        switch ($act) {
            default:
                $head = $foot = array();

                if ($setup['service_head']) {
                    $q = mysql_query(
                        'SELECT `name`, `value` FROM `users_settings` WHERE `parent_id` = ' . (int)$_SESSION['id']
                            . ' AND `position` = "0"',
                        $mysql
                    );
                    for ($i = 1; $i <= $setup['service_head']; ++$i) {
                        $head[$i] = mysql_fetch_assoc($q);
                    }
                }
                if ($setup['service_foot']) {
                    $q = mysql_query(
                        'SELECT `name`, `value` FROM `users_settings` WHERE `parent_id` = ' . (int)$_SESSION['id']
                            . ' AND `position` = "1"',
                        $mysql
                    );
                    for ($i = 1; $i <= $setup['service_foot']; ++$i) {
                        $foot[$i] = mysql_fetch_assoc($q);
                    }
                }

                $template->assign('head', $head);
                $template->assign('foot', $foot);
                break;


            case 'save':
                $_POST['style'] = ltrim($_POST['style'], 'http://');
                $_POST['url'] = ltrim($_POST['url'], 'http://');

                $_SESSION['url'] = $_POST['url'];
                $_SESSION['name'] = $_POST['name'];
                $_SESSION['mail'] = $_POST['mail'];


                $key = 0;

                mysql_query(
                    '
                UPDATE `users_profiles`
                SET
                `name` = "' . mysql_real_escape_string($_POST['name'], $mysql) . '",
                `url` = "' . mysql_real_escape_string($_POST['url'], $mysql) . '",
                `mail` = "' . mysql_real_escape_string($_POST['mail'], $mysql) . '",
                `style` = "' . mysql_real_escape_string($_POST['style'], $mysql) . '",
                WHERE `id` = ' . (int)$_SESSION['id']
                    ,
                    $mysql
                );
                mysql_query('DELETE FROM `users_settings` WHERE `parent_id` = ' . (int)$_SESSION['id'], $mysql);
                $sql = 'INSERT INTO `users_settings` (`parent_id`, `position`, `name`, `value`) VALUES';

                $all = sizeof($_POST['head']['name']);
                $all = $all < $setup['service_head'] ? $all : $setup['service_head'];
                for ($i = 0; $i < $all; ++$i) {
                    $name = $_POST['head']['name'][$i];
                    $value = ltrim($_POST['head']['value'][$i], 'http://');
                    if ($name && $value) {
                        $sql
                            .=
                            '(' . (int)$_SESSION['id'] . ', "0", "' . mysql_real_escape_string($name, $mysql) . '", "'
                                . mysql_real_escape_string($value, $mysql) . '"),';
                        $key++;
                    }
                }

                $all = sizeof($_POST['foot']['name']);
                $all = $all < $setup['service_foot'] ? $all : $setup['service_foot'];
                for ($i = 0; $i < $all; ++$i) {
                    $name = $_POST['foot']['name'][$i];
                    $value = ltrim($_POST['foot']['value'][$i], 'http://');
                    if ($name && $value) {
                        $sql
                            .=
                            '(' . (int)$_SESSION['id'] . ', "1", "' . mysql_real_escape_string($name, $mysql) . '", "'
                                . mysql_real_escape_string($value, $mysql) . '"),';
                        $key++;
                    }
                }

                if ($key) {
                    $r = mysql_query(rtrim($sql, ','), $mysql);
                } else {
                    $r = true;
                }

                mysql_query('OPTIMIZE TABLE `users_profiles`, `users_settings`', $mysql);
                mysql_query('ANALYZE TABLE `users_profiles`, `users_settings`', $mysql);

                if ($r) {
                    message($language['settings_saved']);
                } else {
                    error($language['error']);
                }
                break;


            case 'exit':
                session_destroy();
                error($language['signed_out']);
                break;
        }
    }
}


$template->assign('breadcrumbs', array('service' => $language['advanced_service']));
$template->send();
