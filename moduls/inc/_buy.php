<?php
$banner = $buy = array();

if ($setup['buy_change']) {
    if ($setup['buy']) {
        if ($setup['randbuy']) {
            $list = explode("\n", $setup['buy']);
            shuffle($list);
            for ($i = 0; $i < $setup['countbuy']; ++$i) {
                $buy[] = $list[$i];
            }
        } else {
            $list = explode("\n", $setup['buy']);
            for ($i = 0; $i < $setup['countbuy']; ++$i) {
                $buy[] = $list[$i];
            }
        }
    }

    if ($setup['banner']) {
        if ($setup['randbanner']) {
            $list = explode("\n", $setup['banner']);
            shuffle($list);
            for ($i = 0; $i < $setup['countbanner']; ++$i) {
                $banner[] = $list[$i];
            }
        } else {
            $list = explode("\n", $setup['banner']);
            for ($i = 0; $i < $setup['countbanner']; ++$i) {
                $banner[] = $list[$i];
            }
        }
    }
}


// модуль расширенного сервиса
$serviceBanner = $serviceBuy = array();
if ($setup['service_change_advanced']) {
    $user = isset($_GET['user']) ? $_GET['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');
    $user = intval($user);

    if ($user) {
        if ($setup['service_head']) {
            $head = mysql_query('
                SELECT `name`, `value`
                FROM `users_settings`
                WHERE `parent_id` = ' . $user . '
                AND `position` = "0"
            ', $mysql);
            $all = mysql_num_rows($head);
            $all = $all < $setup['service_head'] ? $all : $setup['service_head'];
            if ($all) {
                for ($i = 0; $i < $all; ++$i) {
                    $q = mysql_fetch_assoc($head);
                    $serviceBuy[$q['value']] = $q['name'];;
                }
            }
        }

        if ($setup['service_foot']) {
            $foot = mysql_query('
                SELECT `name`, `value`
                FROM `users_settings`
                WHERE `parent_id` = ' . $user . '
                AND `position` = "1"
            ', $mysql);
            $all = mysql_num_rows($foot);
            $all = $all < $setup['service_foot'] ? $all : $setup['service_foot'];
            if ($all) {
                for ($i = 0; $i < $all; ++$i) {
                    $q = mysql_fetch_assoc($foot);
                    $serviceBanner[$q['value']] = $q['name'];
                }
            }
        }
    }
}
