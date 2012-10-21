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



require 'moduls/header.php';
###############Если статистика выключена###############
if (!$setup['stat_change']) {
    error('Not found');
}

$template->setTemplate('stat.tpl');

$seo['title'] = $language['statistics'];


$stat = mysql_fetch_assoc(mysql_query('
    SELECT COUNT(1) AS all_files, SUM(`loads`) AS total_downloads, SUM(`size`) AS total_volume
    FROM `files`
    WHERE `dir` = "0" AND `hidden` = "0"
', $mysql));
$stat['total_new_files'] = mysql_result(mysql_query('
    SELECT COUNT(1)
    FROM `files`
    WHERE `timeupload` > ' . ($_SERVER['REQUEST_TIME'] - (86400 * $setup['day_new'])) . '
    AND `hidden` = "0"
', $mysql), 0);


$template->assign('stat', $stat);
$template->assign('breadcrumbs', array('stat' => $language['statistics']));

require 'moduls/foot.php';

?>
