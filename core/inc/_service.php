<?php

if ($setup['service_change']) {
    if (isset($_GET['url'])) {
        $_SESSION['site_url'] = $setup['site_url'] = ltrim($_GET['url'], 'http://');
    } else {
        if (isset($_SESSION['site_url'])) {
            $setup['site_url'] = $_SESSION['site_url'];
        }
    }
}

if ($setup['service_change_advanced']) {
    $user = isset($_GET['user']) ? $_GET['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');

    if ($user) {
        $q = MysqlDb::getInstance()->prepare('SELECT `url`, `name`, `style` FROM `users_profiles` WHERE `id` = ?');

        if ($q->execute(array($user)) && $q->rowCount() > 0) {
            $fetch = $q->fetch();
            $_SESSION['user'] = $user;
            $_SESSION['site_url'] = $setup['site_url'] = $fetch['url'];
            //$_SESSION['site_name'] = $setup['site_name'] = $fetch['name'];

            if ($fetch['style'] && $fetch['style'] != @$_SESSION['style']) {
                $_SESSION['style'] = $style = $fetch['style'];
            }
        }
    }
}
