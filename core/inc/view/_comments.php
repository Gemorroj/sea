<?php
// Последние комментарии

//$comments = array();
if (Config::get('comments_view') && $commentsCount) {
    $mysqldb = MysqlDb::getInstance();

    $q = $mysqldb->prepare('SELECT `name`, `text`, `time` FROM `comments` WHERE `file_id` = ? ORDER BY `id` DESC LIMIT ?');
    $q->bindValue(1, $id, PDO::PARAM_INT);
    $q->bindVAlue(2, Config::get('comments_view'), PDO::PARAM_INT);
    $q->execute();

    $comments = $q->fetchAll();
}
