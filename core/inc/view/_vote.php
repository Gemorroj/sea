<?php
// Система голосований

// $vote = null;
$eval = Http_Request::get('eval');

if (Config::get('eval_change') && $eval !== null) {
    if (strpos($file['ips'], Http_Request::getIp()) === false) {
        $vote = true;
        if (!$file['ips']) {
            $ipp = Http_Request::getIp();
        } else {
            $ipp = $file['ips'] . "\n" . Http_Request::getIp();
        }

        $db = Db_Mysql::getInstance();

        if ($eval < 1) {
            $file['no'] += 1;

            $q = $db->prepare('UPDATE `files` SET `no` = `no` + 1, `ips` = ? WHERE `id` = ?');
            $q->bindValue(1, $ipp);
            $q->bindValue(2, $file['id'], PDO::PARAM_INT);
            $q->execute();
        } else {
            $file['yes'] += 1;

            $q = $db->prepare('UPDATE `files` SET `yes` = `yes` + 1, `ips` = ? WHERE `id` = ?');
            $q->bindValue(1, $ipp);
            $q->bindValue(2, $file['id'], PDO::PARAM_INT);
            $q->execute();
        }
    } else {
        $vote = false;
    }
}
