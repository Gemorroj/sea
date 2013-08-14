<?php
if (Config::get('service_change')) {
    if (isset($_GET['url'])) {
        $_SESSION['site_url'] = preg_replace('/^(?:.*:\/\/)/', '', $_GET['url']);
    }
    if (isset($_SESSION['site_url'])) {
        Config::set('site_url', $_SESSION['site_url']);
    }
}

if (Config::get('service_change_advanced')) {
    $user = isset($_GET['user']) ? $_GET['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');

    if ($user) {
        $q = Db_Mysql::getInstance()->prepare('SELECT `url`, `name`, `style` FROM `users_profiles` WHERE `id` = ?');

        if ($q->execute(array($user)) && $q->rowCount() > 0) {
            $fetch = $q->fetch();
            $_SESSION['user'] = $user;
            $_SESSION['site_url'] = $fetch['url'];
            Config::set('site_url', $fetch['url']);

            if ($fetch['style'] && $fetch['style'] != @$_SESSION['style']) {
                $_SESSION['style'] = $fetch['style'];
                $style = $fetch['style'];
            }
        }
    }
}
