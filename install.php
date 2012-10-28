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


require 'moduls/config.php';
require 'moduls/header.php';

set_time_limit(999);
ignore_user_abort(true);


if (!@$_GET['level']) {
    echo 'Введите ваши данные:<br/>
    <form action="install.php?level=1" method="post">
    <div class="row">Пароль админа:<br/><input name="pass" type="text" value="1234"/><br/><input type="submit" value="Установка"/></div>
    </form>';
} else {
    $er = '';

    chmod('files/', 0777);
    chmod('about/', 0777);
    chmod('attach/', 0777);
    chmod('screen/', 0777);
    chmod('data/mp3/', 0777);
    chmod('data/zip/', 0777);
    chmod('data/zip_pic/', 0777);
    chmod('data/jar/', 0777);
    chmod('data/jar_ico/', 0777);
    chmod('data/theme/', 0777);
    chmod('data/ffmpeg/', 0777);
    chmod('data/pic/', 0777);

    mysql_query('DROP TABLE `files`,`komments`,`online`,`setting`,`loginlog`,`news`,`news_komments`,`users_profiles`,`users_settings`;', $mysql);

    $sql = "CREATE TABLE `files` (
      `id` int(11) unsigned NOT NULL auto_increment,
      `dir` enum('0','1') NOT NULL default '0',
      `dir_count` int(11) unsigned NOT NULL default '0',
      `path` varchar(255) NOT NULL,
      `infolder` varchar(255) NOT NULL,
      `name` varchar(255) NOT NULL,
      `rus_name` varchar(255) NOT NULL,
      `aze_name` varchar(255) NOT NULL,
      `tur_name` varchar(255) NOT NULL,
      `priority` tinyint(3) NOT NULL default '0',
      `size` int(11) unsigned NOT NULL,
      `loads` int(11) unsigned NOT NULL default '0',
      `timeload` int(11) unsigned NOT NULL,
      `timeupload` int(11) unsigned NOT NULL,
      `ips` text NOT NULL,
      `yes` mediumint(4) unsigned NOT NULL default '0',
      `no` mediumint(4) unsigned NOT NULL default '0',
      `hidden` enum('0','1') NOT NULL default '0',
      `attach` text,
      `seo` text,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `path` (`path`),
      KEY `loads` (`loads`),
      KEY `yes` (`yes`),
      KEY `infolder` (`infolder`),
      KEY `infolder_timeupload` (`infolder`,`timeupload`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `komments` (
      `id` int(11) unsigned NOT NULL auto_increment,
      `file_id` int(11) unsigned NOT NULL,
      `name` varchar(255) NOT NULL,
      `text` text NOT NULL,
      `time` int(11) unsigned NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `file_id` (`file_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `online` (
      `ip` varchar(23) NOT NULL,
      `time` datetime NOT NULL,
      UNIQUE KEY `ip` (`ip`)
    ) ENGINE=MEMORY ;";
    mysql_query($sql, $mysql);
    mysql_query("INSERT INTO `online` (`time`) VALUES ('0');", $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `setting` (
      `name` varchar(32) NOT NULL,
      `value` varchar(1023) NOT NULL,
      UNIQUE KEY `name` (`name`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE IF NOT EXISTS `loginlog` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `ua` varchar(255) NOT NULL,
      `ip` varchar(23) NOT NULL,
      `time` int(10) unsigned NOT NULL,
      `access_num` tinyint(3) unsigned NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=1 ;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `news` (
      `id` int(11) unsigned NOT NULL auto_increment,
      `news` text NOT NULL,
      `rus_news` text NOT NULL,
      `aze_news` text NOT NULL,
      `tur_news` text NOT NULL,
      `time` int(11) unsigned NOT NULL default '0',
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `news_komments` (
      `id` int(11) unsigned NOT NULL auto_increment,
      `id_news` int(11) unsigned NOT NULL,
      `text` text NOT NULL,
      `name` varchar(32) NOT NULL,
      `time` int(11) unsigned NOT NULL default '0',
      PRIMARY KEY  (`id`),
      KEY `id_news` (`id_news`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `users_profiles` (
      `id` int(10) unsigned NOT NULL auto_increment,
      `url` varchar(255) NOT NULL COMMENT 'ссылка на главную партнера',
      `name` varchar(255) NOT NULL COMMENT 'название ссылки',
      `pass` char(32) NOT NULL default '',
      `mail` varchar(255) NOT NULL,
      `style` varchar(255) NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED AUTO_INCREMENT=8 ;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }


    $sql = "CREATE TABLE `users_settings` (
      `parent_id` int(10) unsigned NOT NULL,
      `position` enum('0','1') NOT NULL default '0' COMMENT 'позиция ссылки. 0 - верх, 1 - низ',
      `name` varchar(255) NOT NULL COMMENT 'название ссылки',
      `value` varchar(255) default NULL COMMENT 'текст ссылки',
      KEY `parent_id` (`parent_id`),
      KEY `parent_id_position` (`parent_id`,`position`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;";
    mysql_query($sql, $mysql);
    $error = mysql_error($mysql);
    if ($error) {
        $er .= $error . '<br/>';
    }



    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'password','" . md5(trim($_POST['pass'])) . "');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'path', 'files');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'opath','about');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'apath','attach');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'spath', 'screen');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'jpath', 'data/jar');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'ipath', 'data/jar_ico');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'zpath', 'data/zip');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'zppath', 'data/zip_pic');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'tpath', 'data/theme');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'mp3path', 'data/mp3');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'ffmpegpath', 'data/ffmpeg');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'picpath', 'data/pic');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'limit', '10');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'klimit', '25');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'onpage', '10');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'prew', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'sort', 'name');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'pagehand', '10');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'hackmess', 'Error!');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'view_size',  '128*128,120*160,132*176,240*320');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'lib_desc', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'lib', '1024');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'lib_str', '160');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'desc', '50');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'marker', '2');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'marker_where', 'foot');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'css', 'style');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'countban', '2');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'autologin', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'timeban', '10');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'site_url', '" . $_SERVER['HTTP_HOST'] . "');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'anim_change', '1');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'screen_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'screen_file_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'swf_change', '0');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'swf_file_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'jar_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'jar_file_change', '1');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'lib_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'service_change', '1');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'abuse_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'exchanger_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'exchanger_notice', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'exchanger_hidden', '0');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'exchanger_name', '[a-zA-Z0-9_]');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'exchanger_extensions', 'jpg,gif,png,3gp,mp4,flv,avi,mp3,thm,nth,zip,txt');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'send_email', '1');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'online_max', '0');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'online_max_time', NOW());", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'service_change_advanced', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'service_head', '2');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'service_foot', '3');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'style_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'ffmpeg_frame', '5');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'ffmpeg_frames', '25,120,250');", $mysql);

    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'zag', 'Downloads');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'day_new', '2');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'komments_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'komments_captcha', '0');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'top_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'ext', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'delete_dir', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'delete_file', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'search_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'prev_next', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'eval_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'stat_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'onpage_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'preview_change', '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'prev_size', '80*80');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'top_num',  '20');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'pagehand_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'zip_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'jad_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'cut_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'audio_player_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'video_player_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'buy_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'online',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'online_time',  '60');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'buy',  '<strong><a href=\"/\">" . $_SERVER['HTTP_HOST'] . "</a></strong>');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'randbuy',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'countbuy',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'banner',  '<strong><a href=\"/\">" . $_SERVER['HTTP_HOST'] . "</a></strong>');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'randbanner',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'countbanner',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'zakaz_change',  '1');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'zakaz_email',  'admin@" . $_SERVER['HTTP_HOST'] . "');", $mysql);
    mysql_query("INSERT INTO `setting` (`name`,`value`) VALUES ( 'langpack',  'russian');", $mysql);
    mysql_query("INSERT INTO `loginlog` (`ua`, `ip`, `time`) VALUES ('', '', '');", $mysql);


    mysql_query("INSERT INTO `setting` (`name`, `value` ) VALUES ('version', '08-04-2012');", $mysql);


    echo 'Установка закончена<br/>Не забудте удалить файл install.php<br/><strong><a href="./">К загрузкам</a><br/>';
}
