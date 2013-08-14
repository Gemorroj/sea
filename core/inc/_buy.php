<?php
$banner = $buy = array();

if (Config::get('buy_change')) {
    if (Config::get('buy')) {
        if (Config::get('randbuy')) {
            $list = explode("\n", Config::get('buy'));
            shuffle($list);
            for ($i = 0; $i < Config::get('countbuy'); ++$i) {
                $buy[] = $list[$i];
            }
        } else {
            $list = explode("\n", Config::get('buy'));
            for ($i = 0; $i < Config::get('countbuy'); ++$i) {
                $buy[] = $list[$i];
            }
        }
    }

    if (Config::get('banner')) {
        if (Config::get('randbanner')) {
            $list = explode("\n", Config::get('banner'));
            shuffle($list);
            for ($i = 0; $i < Config::get('countbanner'); ++$i) {
                $banner[] = $list[$i];
            }
        } else {
            $list = explode("\n", Config::get('banner'));
            for ($i = 0; $i < Config::get('countbanner'); ++$i) {
                $banner[] = $list[$i];
            }
        }
    }
}


// модуль расширенного сервиса
$serviceBanner = $serviceBuy = array();
if (Config::get('service_change_advanced') && (Config::get('service_head') || Config::get('service_foot'))) {
    $user = isset($_GET['user']) ? $_GET['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');

    if ($user) {
        $q = Db_Mysql::getInstance()->prepare('
            SELECT `name`, `value`
            FROM `users_settings`
            WHERE `parent_id` = ?
            AND `position` = ?
            LIMIT ?
        ');

        if (Config::get('service_head')) {
            $q->bindValue(1, $user);
            $q->bindValue(2, '0');
            $q->bindValue(3, intval(Config::get('service_head')), PDO::PARAM_INT);
            $q->execute();

            foreach ($q as $head) {
                $serviceBuy[$head['value']] = $head['name'];
            }
        }

        if (Config::get('service_foot')) {
            $q->bindValue(1, $user);
            $q->bindValue(2, '1');
            $q->bindValue(3, intval(Config::get('service_foot')), PDO::PARAM_INT);
            $q->execute();

            foreach ($q as $foot) {
                $serviceBanner[$foot['value']] = $foot['name'];
            }
        }
    }
}
