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
if ($setup['service_change_advanced'] && ($setup['service_head'] || $setup['service_foot'])) {
    $user = isset($_GET['user']) ? $_GET['user'] : (isset($_SESSION['user']) ? $_SESSION['user'] : '');

    if ($user) {
        $q = MysqlDb::getInstance()->prepare('
            SELECT `name`, `value`
            FROM `users_settings`
            WHERE `parent_id` = ?
            AND `position` = ?
            LIMIT ?
        ');

        if ($setup['service_head']) {
            $q->bindValue(1, $user);
            $q->bindValue(2, '0');
            $q->bindValue(3, intval($setup['service_head']), PDO::PARAM_INT);
            $q->execute();

            foreach ($q as $head) {
                $serviceBuy[$head['value']] = $head['name'];
            }
        }

        if ($setup['service_foot']) {
            $q->bindValue(1, $user);
            $q->bindValue(2, '1');
            $q->bindValue(3, intval($setup['service_foot']), PDO::PARAM_INT);
            $q->execute();

            foreach ($q as $foot) {
                $serviceBanner[$foot['value']] = $foot['name'];
            }
        }
    }
}
