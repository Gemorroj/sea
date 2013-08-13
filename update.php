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
 * @author Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */


require 'core/config.php';


set_time_limit(999);
ignore_user_abort(true);

$mysqldb = MysqlDb::getInstance();
$version = Config::get('version');

if (!$version) {
    $mysqldb->exec("
        CREATE TABLE `users_profiles` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `url` varchar(255) NOT NULL COMMENT 'ссылка на главную партнера',
            `name` varchar(255) NOT NULL COMMENT 'название ссылки',
            `pass` char(32) NOT NULL default '',
            `mail` varchar(255) NOT NULL,
            `style` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
    ");


    $mysqldb->exec("
        CREATE TABLE `users_settings` (
            `parent_id` int(10) unsigned NOT NULL,
            `position` enum('0','1') NOT NULL default '0' COMMENT 'позиция ссылки. 0 - верх, 1 - низ',
            `name` varchar(255) NOT NULL COMMENT 'название ссылки',
            `value` varchar(255) default NULL COMMENT 'текст ссылки',
            KEY `parent_id` (`parent_id`),
            KEY `parent_id_position` (`parent_id`,`position`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8
    ");


    $mysqldb->exec("
        ALTER TABLE `files`
            CHANGE `yes` `yes` MEDIUMINT( 4 ) UNSIGNED NOT NULL DEFAULT '0',
            CHANGE `no` `no` MEDIUMINT( 4 ) UNSIGNED NOT NULL DEFAULT '0'
    ");
    $mysqldb->exec('ALTER TABLE `files` DROP INDEX `size`');
    $mysqldb->exec('ALTER TABLE `files` DROP INDEX `rus_name`');
    $mysqldb->exec('ALTER TABLE `files` DROP INDEX `dir`');
    $mysqldb->exec('ALTER TABLE `files` ADD INDEX (`yes`)');
    $mysqldb->exec('ALTER TABLE `files` DROP INDEX `path`, ADD UNIQUE `path` ( `path` )');
    $mysqldb->exec("ALTER TABLE `files` ADD `hidden` ENUM( '0', '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0'");


    $mysqldb->exec('TRUNCATE TABLE `online`');
    $mysqldb->exec("
        ALTER TABLE `online`
            CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `ip` `ip` VARCHAR( 23 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            CHANGE `time` `time` DATETIME NOT NULL
    ");
    $mysqldb->exec('ALTER TABLE `online` DROP `id`');
    $mysqldb->exec('ALTER TABLE `online` ENGINE = MEMORY');


    $mysqldb->exec("
        ALTER TABLE `loginlog`
            CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
            CHANGE `ua` `ua` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            CHANGE `ip` `ip` VARCHAR( 23 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
            CHANGE `time` `time` INT( 10 ) UNSIGNED NOT NULL ,
            CHANGE `access_num` `access_num` TINYINT( 3 ) UNSIGNED NOT NULL
    ");

    $mysqldb->exec("ALTER TABLE `loginlog` ROW_FORMAT = FIXED");
    $mysqldb->exec("ALTER TABLE `setting` ROW_FORMAT = FIXED");
    $mysqldb->exec("ALTER TABLE `users_profiles` ROW_FORMAT = FIXED");
    $mysqldb->exec("ALTER TABLE `users_settings` ROW_FORMAT = FIXED");

    $mysqldb->exec("ALTER TABLE `files` ADD COLUMN `attach` TEXT DEFAULT NULL");
    $mysqldb->exec("ALTER TABLE `files` ADD COLUMN `seo` TEXT DEFAULT NULL");

    $mysqldb->exec("
        ALTER TABLE `files`
            ADD `aze_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `rus_name` ,
            ADD `tur_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `aze_name`
    ");
    $mysqldb->exec("
        ALTER TABLE `news`
            ADD `aze_news` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `rus_news` ,
            ADD `tur_news` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `aze_news`
    ");

    $version = '2';
}


if ($version < 3) {
    $mysqldb->exec("RENAME TABLE `komments` TO `comments`");
    $mysqldb->exec("RENAME TABLE `news_komments` TO `news_comments`");
    $mysqldb->exec("DELETE FROM `setting` WHERE `name` = 'klimit'");
    $mysqldb->exec("UPDATE `setting` SET `name` = 'comments_change' WHERE `name` = 'komments_change'");
    $mysqldb->exec("UPDATE `setting` SET `name` = 'comments_view' WHERE `name` = 'komments_view'");
    $mysqldb->exec("UPDATE `setting` SET `name` = 'comments_captcha' WHERE `name` = 'komments_captcha'");
    $mysqldb->exec("UPDATE `setting` SET `value` = UNIX_TIMESTAMP() WHERE `name` = 'online_max_time'");

    $version = '3';
}


if ($version < 3.1) {
    $mysqldb->exec("REPLACE INTO `setting` (`name`,`value`) VALUES ( 'importpath', 'import')");
    $mysqldb->exec("REPLACE INTO `setting` (`name`,`value`) VALUES ( 'ignore_index_breadcrumbs', '0')");
    $mysqldb->exec("REPLACE INTO `setting` (`name`,`value`) VALUES ( 'ignore_index_pages', '0')");
    $mysqldb->exec("UPDATE `setting` SET `name` = 'prev' WHERE `name` = 'prew';");

    $version = '3.1';
}


$mysqldb->prepare("REPLACE INTO `setting` (`name`, `value` ) VALUES (?, ?)")->execute(array('version', $version));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/html" xml:lang="ru" lang="ru">
    <head>
        <meta name="viewport" content="width=device-width"/>
        <title>Обновление</title>
    </head>
    <body>
        <div>
            <fieldset>
                <legend>Обновление закончено</legend>
                <p>
                    Текущая версия: <strong><?php echo $version; ?></strong><br/><br/>
                    Не забудьте удалить файл install.php и update.php<br/><br/>
                    <strong><a href="./apanel/">В админку</a><br/>
                    <strong><a href="./">К загрузкам</a><br/>
                </p>
            </fieldset>
        </div>
    </body>
</html>