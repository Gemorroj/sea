<?php
/**
 * Copyright (c) 2012, Gemorroj
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @author Sea, Gemorroj
 */
/**
 * Sea Downloads
 *
 * @author  Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

require 'core/header.php';

// Если комментарии выключены
if (!Config::get('comments_change')) {
    error('Not found');
}

// Получаем инфу о новости
$q = $mysqldb->prepare('SELECT *, ' . Language::buildNewsQuery() . ' FROM `news` WHERE `id` = ?');
$q->execute(array($id));
$news = $q->fetch();

if (!$news || !$news['news']) {
    error('Not found');
}

$desc = mb_substr($news['news'], 0, Config::get('desc'));


$template->setTemplate('comments.tpl');
$seo['title'] = $desc . ' - ' . Language::get('comments');
$template->assign('breadcrumbs', array(
    'news' => Language::get('news') . ' - ' . $desc,
    'news_comments/' . $id => Language::get('comments')
));

$template->assign('comments_module', 'news_comments');
$template->assign('comments_module_backlink', DIRECTORY . 'news');
$template->assign('comments_module_backname', Language::get('news'));


// всего комментариев
$q = $mysqldb->prepare('SELECT COUNT(1) FROM `news_comments` WHERE `id_news` = ?');
$q->execute(array($id));
$all = $q->fetchColumn();

$paginatorConf = getPaginatorConf($all);

###############Постраничная навигация###############
$template->assign('paginatorConf', $paginatorConf);


$query = $mysqldb->prepare('
    SELECT *
    FROM `news_comments`
    WHERE `id_news` = ?
    ORDER BY `id` DESC
    LIMIT ?, ?
');
$query->bindValue(1, $id, PDO::PARAM_INT);
$query->bindValue(2, $paginatorConf['start'], PDO::PARAM_INT);
$query->bindValue(3, $paginatorConf['onpage'], PDO::PARAM_INT);

$query->execute();
$comments = $query->fetchAll();


###############Запись###########################
if ($_POST) {
    //Проверка на ошибки
    if (!$_POST['msg'] || !$_POST['name']) {
        error(Language::get('not_filled_one_of_the_fields'));
    }
    if (mb_strlen($_POST['msg']) < 4) {
        error(Language::get('you_have_not_written_a_comment_or_he_is_too_short'));
    }

    if (Config::get('comments_captcha')) {
        if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']) {
            unset($_SESSION['captcha_keystring']);
            error(Language::get('not_a_valid_code'));
        }
        unset($_SESSION['captcha_keystring']);
    }

    $q = $mysqldb->prepare('SELECT 1 FROM `news_comments` WHERE `id_news` = ? AND `text` = ? LIMIT 1');
    $q->execute(array($id, $_POST['msg']));

    if ($q->rowCount() > 0) {
        error(Language::get('why_repeat_myself'));
    }

    //Если нет ошибок пишем в базу
    setcookie('sea_name', $_POST['name'], $_SERVER['REQUEST_TIME'] + 86400000, DIRECTORY, $_SERVER['HTTP_HOST'], false, true);

    $q = $mysqldb->prepare('
        INSERT INTO `news_comments` (
            `id_news`, `name`, `text`, `time`
        ) VALUES (
            ?, ?, ?, UNIX_TIMESTAMP()
        )
    ');
    $result = $q->execute(array($id, $_POST['name'], $_POST['msg']));

    if (!$result) {
        error(Language::get('error'));
    }

    message(Language::get('your_comment_has_been_successfully_added'));
}

$template->assign('comments', $comments);
$template->send();
