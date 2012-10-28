<?php

if ($setup['service_change']) {
    if (isset($_GET['url'])) {
        $_SESSION['site_url'] = $setup['site_url'] = ltrim($_GET['url'], 'http://');
    } else if (isset($_SESSION['site_url'])) {
        $setup['site_url'] = $_SESSION['site_url'];
    }
}

if ($setup['service_change_advanced']) {
    $user = isset($_GET['user']) ? $_GET['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');
    $user = intval($user);

    if ($user) {
        $_SESSION['user'] = $user;

        $q = mysql_fetch_row(mysql_query('
            SELECT `url`, `name`, `style`
            FROM `users_profiles`
            WHERE `id` = ' . $user
        , $mysql));
        $_SESSION['site_url'] = $setup['site_url'] = $q[0];
        //$_SESSION['site_name'] = $setup['site_name'] = $q[1];

        if ($q[2] && $q[2] != @$_SESSION['style']) {
            $_SESSION['style'] = $style = $q[2];
        }
    }
}
