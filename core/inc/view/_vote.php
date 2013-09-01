<?php
// Система голосований

// $vote = null;
$eval = Http_Request::get('eval');

if (Config::get('eval_change') && $eval) {
    if (strpos($file['ips'], $_SERVER['REMOTE_ADDR']) === false) {
        $vote = true;
        if (!$file['ips']) {
            $ipp = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipp = $file['ips'] . "\n" . $_SERVER['REMOTE_ADDR'];
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
