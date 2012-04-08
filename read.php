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

if (!$setup['lib_change']) {
    error('Not found');
}
    
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}

// Получаем инфу о файле
$v = mysql_fetch_assoc(mysql_query('
    SELECT *,
    ' . Language::getInstance()->buildFilesQuery() . '
    FROM `files`
    WHERE `id` = ' . $id
    , $mysql
));
$pathinfo = pathinfo($v['path']);

if (!is_file($v['path']) || strtolower($pathinfo['extension']) != 'txt') {
    error('File not found');
}

$back = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `files` WHERE `path` = '" . mysql_real_escape_string($pathinfo['dirname'] . '/', $mysql) . "'", $mysql));


$seo = unserialize($v['seo']);

$title .= $language['read'] . ' - ' . htmlspecialchars($seo['title'] ? $seo['title'] : $v['name'], ENT_NOQUOTES) . ' / ' . $page;

if (isset($_SESSION['lib'])) {
    $setup['lib'] = $_SESSION['lib'];
}


// UTF-8
$fp = fopen($v['path'], 'rb');
if ($page > 1) {
    fseek($fp, $page * $setup['lib'] - $setup['lib']);
}
$content = fread($fp, $setup['lib']) . fgets($fp, 1024);
fclose($fp);

if ($page > 1) {
    $i = 0;
    foreach (str_split($content, 1) as $f) {
        if ($f == ' ' || $f == "\n" || $f == "\r" || $f == "\t") {
            break;
        }
        $i++;
    }
    $content = substr($content, $i);
}

$pages = ceil(filesize($v['path']) / $setup['lib']);


if ($setup['lib_str']) {
    echo '<pre class="ik">' . wordwrap(htmlspecialchars(str_to_utf8($content), ENT_NOQUOTES), $setup['lib_str'], "\n", false) . '</pre>' . go($page, $pages, DIRECTORY . 'read/' . $id);
} else {
    echo '<pre class="ik">' . htmlspecialchars(str_to_utf8($content), ENT_NOQUOTES) . '</pre>' . go($page, $pages, DIRECTORY . 'read/' . $id);
}

if ($back['id']) {
    $str = '- <a href="' . DIRECTORY . $back['id'] . '">' . $language['go to the category'] . '</a><br/>';
} else {
    $str = '';
}
echo '<div class="iblock">- <a href="' . DIRECTORY . 'view/' . $id . '">' . $language['go to the description of the file'] . '</a><br/>' . $str . '- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a><br/></div>';

require 'moduls/foot.php';

?>
