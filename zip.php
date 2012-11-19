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


require 'moduls/header.php';
require 'moduls/PEAR/pclzip.lib.php';


// если zip отключен
if (!$setup['zip_change']) {
    error('Not found');
}



$onpage = get2ses('onpage');
if ($onpage < 3) {
    $onpage = $setting['onpage'];
}

$prew = get2ses('prew');
if ($prew != 0 && $prew != 1) {
    $prew = $setup['preview'];
}

$id = abs($_GET['id']);
$page = isset($_GET['page']) ? abs($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;


$d = getFileInfo($id);
if (!file_exists($d['path'])) {
    error('File not found');
}
###############Получаем каталог#############
$seo = unserialize($d['seo']);
$filename = pathinfo($d['path']);
$ext = strtolower($filename['extension']);
if ($ext != 'zip') {
    error('It is not ZIP archive');
}
$dir = $filename['dirname'] . '/';
$back = mysql_fetch_assoc(
    mysql_query("SELECT * FROM `files` WHERE `path` = '" . mysql_real_escape_string($dir, $mysql) . "'", $mysql)
);
###############Заголовок###################
if ($seo['title']) {
    $title .= htmlspecialchars($seo['title'], ENT_NOQUOTES);
} else {
    $title .= htmlspecialchars($filename['basename'], ENT_NOQUOTES);
}

echo '<div class="mblock"><img src="' . DIRECTORY . 'dis/load.png" alt=""/><strong>' . $language['view archive']
    . ' <a href="' . DIRECTORY . 'zip/' . $id . '">' . $filename['basename']
    . '</a></strong></div><div class="iblock">';
###############Содержимое###################
if (!isset($_GET['action'])) {
    $zip = new PclZip($d['path']);

    if (!$list = $zip->listContent()) {
        error('Error: ' . $zip->errorInfo(true));
    }
    $listcontent = $sizelist = $savelist = '';
    $aa = sizeof($list);
    for ($i = 0; $i < $aa; ++$i) {
        for (reset($list[$i]); $key = key($list[$i]); next($list[$i])) {
            $zfilesize = strstr($listcontent, '--size');
            $zfilesize = str_replace('--size:', '', $zfilesize);
            $zfilesize = str_replace($zfilesize, $zfilesize . '|', $zfilesize);
            $sizelist .= $zfilesize;
            $listcontent = "[$i]--$key:" . $list[$i][$key];
            $zfile = strstr($listcontent, '--filename');
            $zfile = str_replace('--filename:', '', $zfile);
            $zfile = str_replace($zfile, $zfile . '|', $zfile);
            $savelist .= $zfile;
        }
    }
    /*
    // TODO:заменить просмотр
    for ($i = 0, $s = sizeof($list); $i < $s; ++$i) {
        if ($list[$i]['folder']) {
            $folder++;
        } else {
            $link .= '<br/>&#187;<span class="red"><a href="extract.php?arh=' . basename($f) . '&amp;f=' . rawurlencode($list[$i]['filename']) . '">' . htmlspecialchars(charsets($list[$i]['filename']), ENT_NOQUOTES, 'UTF-8') . '</a></span> (' . round($list[$i]['size'] / 1024, 2) . ' kb)';
        }
    }
    */


    $sizefiles2 = explode('|', $sizelist);

    $sizelist2 = array_sum($sizefiles2);
    $obkb = round($sizelist2 / 1024, 2);
    $preview = $savelist;

    $preview = explode('|', $preview);

    $count = sizeof($preview) - 1;
    echo $language['all files'] . ': ' . $count . '<br/>' . $language['the unpacked archive'] . ': ' . $obkb . ' kb</div><div class="row">';
    if ($page < 1) {
        $page = 1;
    }
    $n = 0;
    $pages = ceil($count / $onpage);
    if (!$pages) {
        $pages = 1;
    }
    if ($page) {
        $n = ($onpage * $page) - $onpage;
    }
    // if ($count == 0) echo 'Empty';
    $sizefiles = explode('|', $sizelist);
    $selectfile = explode('|', $savelist);
    //------------------------------------------------------------------------------------------
    for ($i = 1; $i <= $onpage; ++$i) {
        if (empty($selectfile[$n])) {
            $n++;
            continue;
        }
        $path = $selectfile[$n];
        $fname = preg_replace('#.*[\\/]#', '', $path);
        $zdir = preg_replace('#[\\/]?[^\\/]*$#', '', $path);
        echo $zdir . '/<a href="' . DIRECTORY . 'zip/preview/' . $id . '/' . $path . '/">' . $fname . '</a>';
        if ($sizefiles[$n]) {
            echo ' [' . round($sizefiles[$n] / 1024, 2) . 'kb]';
        }
        echo '<br/>';
        $n++;
    }
    //------------------------------------------------------------------------------------------

    echo '</div>';

    if ($pages > 1) {
        echo '<div class="iblock">' . $language['pages'] . ': ';
        $asd = $page - 2;
        $asd2 = $page + 3;
        if ($asd < $count && $asd > 0 && $page > 3) {
            echo '<a href="' . DIRECTORY . 'zip/' . $id . '&amp;page=1">1</a> ... ';
        }
        for ($i = $asd; $i < $asd2; ++$i) {
            if ($i < $count && $i > 0) {
                if ($i > $pages) {
                    break;
                }
                if ($page == $i) {
                    echo '<strong>[' . $i . ']</strong> ';
                } else {
                    echo '<a href="' . DIRECTORY . 'zip/' . $id . '/' . $i . '">' . $i . '</a> ';
                }
            }
        }
        if ($i <= $pages) {
            if ($asd2 < $count) {
                echo ' ... <a href="' . DIRECTORY . 'zip/' . $id . '/' . $pages . '">' . $pages . '</a>';
            }
        }
        echo '<br/></div>';
    }

    echo '<div class="iblock">';

} else {
    if ($_GET['action'] == 'preview') {
        if (strpos($_GET['open'], '..') !== false || strpos($_GET['open'], './') !== false) {
            error($setup['hackmess']);
        }

        $title .= ' - ' . $_GET['open'];
        echo '<strong>' . $language['file'] . ': <a href="' . DIRECTORY . 'zip/down/' . $id . '/' . str_replace(
            '"',
            '&quot;',
            $_GET['open']
        ) . '">' . $_GET['open'] . '</a></strong><br/>';

        $mime = ext_to_mime(pathinfo($_GET['open'], PATHINFO_EXTENSION));

        if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/jpeg' || $mime == 'image/bmp') {
            $f = $setup['zppath'] . '/' . str_replace(
                '/',
                '--',
                mb_substr(strstr($d['path'], '/'), 1) . '_' . strtolower($_GET['open'])
            );
            if (!file_exists($f)) {
                $zip = new PclZip($d['path']);
                $content = $zip->extract(PCLZIP_OPT_BY_NAME, $_GET['open'], PCLZIP_OPT_EXTRACT_AS_STRING);
                file_put_contents($f, $content[0]['content']);
            }
            echo '<img src="' . DIRECTORY . $f . '" alt="' . $_GET['open'] . '"/><br/>';
        } else {
            if ($mime == 'text/plain') {
                $zip = new PclZip($d['path']);
                $content = $zip->extract(PCLZIP_OPT_BY_NAME, $_GET['open'], PCLZIP_OPT_EXTRACT_AS_STRING);

                $startDebug = microtime(true);
                $content = str_to_utf8($content[0]['content']);
                echo $language['lines'] . ': ' . substr_count($content, "\n");

                $pages = floor(mb_strlen($content) / $setup['lib']);
                $content = mb_substr($content, $page * $setup['lib'] - $setup['lib'], $setup['lib'] + 64);

                if ($page > 1) {
                    $i = 0;
                    foreach (str_split($content) as $v) {
                        if ($v == ' ' || $v == "\n" || $v == "\r" || $v == "\t") {
                            break;
                        }
                        $i++;
                    }
                    $content = substr($content, $i);
                }

                if ($setup['lib_str']) {
                    echo'<pre>' . wordwrap(htmlspecialchars($content, ENT_NOQUOTES), $setup['lib_str'], "\n", false)
                        . '</pre>'
                        . go($page, $pages, DIRECTORY . 'zip/preview/' . $id . '/' . $_GET['open']);
                } else {
                    echo'<pre>' . htmlspecialchars($content, ENT_NOQUOTES) . '</pre>' . go(
                        $page,
                        $pages,
                        DIRECTORY . 'zip/preview/' . $id . '/' . $_GET['open']
                    );
                }
            } else {
                echo '<span class="no">' . $language['file unavailable for viewing'] . '</span>';
            }
        }

        echo '</div><div class="iblock">';
    } else {
        if ($_GET['action'] == 'down') {
            if (strpos($_GET['open'], '..') !== false or strpos($_GET['open'], './') !== false) {
                error($setup['hackmess']);
            }

            ob_end_clean();

            $zip = new PclZip($d['path']);
            $mime = ext_to_mime(pathinfo($_GET['open'], PATHINFO_EXTENSION));
            header('Content-Type: ' . $mime);
            if ($mime == 'text/plain') {
                $f = $zip->extract(PCLZIP_OPT_BY_NAME, $_GET['open'], PCLZIP_OPT_EXTRACT_AS_STRING);
                echo str_to_utf8($f[0]['content']);
            } else {
                $zip->extract(PCLZIP_OPT_BY_NAME, $_GET['open'], PCLZIP_OPT_EXTRACT_IN_OUTPUT);
            }

            exit;
        }
    }
}

echo '- <a href="' . DIRECTORY . 'view/' . $id . '">' . $language['go to the description of the file']
    . '</a><br/>- <a href="' . DIRECTORY . $back['id'] . '">' . $language['go to the category'] . '</a><br/>- <a href="'
    . DIRECTORY . '">' . $language['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $language['home']
    . '</a></div>';
