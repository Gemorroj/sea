<?php
// Последние комментарии

//$comments = array();
if ($setup['comments_view'] && $commentsCount) {
    $mysqldb = MysqlDb::getInstance();

    $q = $mysqldb->prepare('SELECT `name`, `text`, `time` FROM `comments` WHERE `file_id` = ? ORDER BY `id` DESC LIMIT ?');
    $q->bindValue(1, $id, PDO::PARAM_INT);
    $q->bindVAlue(2, $setup['comments_view'], PDO::PARAM_INT);
    $q->execute();

    $comments = $q->fetchAll();
}
