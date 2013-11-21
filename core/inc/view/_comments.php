<?php
// Последние комментарии

//$comments = array();
if (Config::get('comments_view') && $commentsCount) {
    $db = Db_Mysql::getInstance();

    $q = $db->prepare('SELECT `name`, `text`, `time` FROM `comments` WHERE `file_id` = ? ORDER BY `id` DESC LIMIT ?');
    $q->bindValue(1, $id, PDO::PARAM_INT);
    $q->bindValue(2, intval(Config::get('comments_view')), PDO::PARAM_INT);
    $q->execute();

    $comments = $q->fetchAll();
}
