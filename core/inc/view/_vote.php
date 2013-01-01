<?php
// Система голосований

// $vote = null;
if ($setup['eval_change'] && isset($_GET['eval'])) {
    if (strpos($file['ips'], $_SERVER['REMOTE_ADDR']) === false) {
        $vote = true;
        if (!$file['ips']) {
            $ipp = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipp = $file['ips'] . "\n" . $_SERVER['REMOTE_ADDR'];
        }

        $mysqldb = MysqlDb::getInstance();

        if ($_GET['eval'] < 1) {
            $file['no'] += 1;

            $q = $mysqldb->prepare('UPDATE `files` SET `no` = `no` + 1, `ips` = ? WHERE `id` = ?');
            $q->bindValue(1, $ipp);
            $q->bindValue(2, $file['id'], PDO::PARAM_INT);
            $q->execute();
        } else {
            $file['yes'] += 1;

            $q = $mysqldb->prepare('UPDATE `files` SET `yes` = `yes` + 1, `ips` = ? WHERE `id` = ?');
            $q->bindValue(1, $ipp);
            $q->bindValue(2, $file['id'], PDO::PARAM_INT);
            $q->execute();
        }
    } else {
        $vote = false;
    }
}
