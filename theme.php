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


require 'core/config.php';

ini_set('memory_limit', '128M');

$id = intval($_GET['id']);

$v = getFileInfo($id);
$prev_path = str_replace('/', '--', mb_substr(strstr($v['path'], '/'), 1));
$ext = strtolower(pathinfo($v['path'], PATHINFO_EXTENSION));


$name = Config::get('tpath') . '/' . $prev_path . '.gif';
$location = 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $name;


if (file_exists($name)) {
    Http_Response::getInstance()->redirect($location, 301);
} elseif (file_exists($name . '.swf')) {
    Http_Response::getInstance()->redirect($location . '.swf', 301);
}


if ($ext == 'nth') {
    $nth = new PclZip($v['path']);

    $content = $nth->extract(PCLZIP_OPT_BY_NAME, 'theme_descriptor.xml', PCLZIP_OPT_EXTRACT_AS_STRING);
    if (!$content) {
        $content = $nth->extract(PCLZIP_OPT_BY_PREG, '#\.xml$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }


    $teg = simplexml_load_string($content[0]['content'])->wallpaper['src'];
    if (!$teg) {
        $teg = simplexml_load_string($content[0]['content'])->wallpaper['main_display_graphics'];
    }
    if (!$teg) {
        $teg = simplexml_load_string($content[0]['content'])->background['main_default_bg'];
    }

    $image = $nth->extract(PCLZIP_OPT_BY_NAME, (string)$teg, PCLZIP_OPT_EXTRACT_AS_STRING);

    file_put_contents($name, $image[0]['content']);
    unset($image);
    $info = getimagesize($name);

    if ($info[2] == 4 || $info[2] == 13) {
        Image::resize($name, $name . '.swf', 0, 0, Config::get('marker'));
        $location .= '.swf';
    } else {
        Image::resize($name, $name, 0, 0, Config::get('marker'));
    }
} elseif ($ext == 'thm') {
    $thm = new Archive_Tar($v['path']);

    $content = $thm->extractInString('Theme.xml');
    if (!$content) {
        $content = $thm->extractInString(pathinfo($v['path'], PATHINFO_FILENAME) . '.xml');
    }

    if (!$content) {
        $list = $thm->listContent();
        $all = sizeof($list);
        for ($i = 0; $i < $all; ++$i) {
            if (pathinfo($list[$i]['filename'], PATHINFO_EXTENSION) == 'xml') {
                $content = $thm->extractInString($list[$i]['filename']);
                break;
            }
        }
    }

    // fix bug in Tar.php
    if (!$content) {
        preg_match('/<\?\s*xml\s*version\s*=\s*"1\.0"\s*\?>(.*)<\/.+>/isU', file_get_contents($v['path']), $arr);
        $content = trim($arr[0]);
        unset($arr);
    }


    $load = (string)simplexml_load_string($content)->Standby_image['Source'];
    if (!$load) {
        $load = (string)simplexml_load_string($content)->Desktop_image['Source'];
    }

    if (!$load) {
        exit;
    }


    file_put_contents($name, $thm->extractInString($load));
    unset($load, $content);
    $info = getimagesize($name);

    if ($info[2] == 4 || $info[2] == 13) {
        Image::resize($name, $name . '.swf', 0, 0, Config::get('marker'));
        $location .= '.swf';
    } else {
        Image::resize($name, $name, 0, 0, Config::get('marker'));
    }
} elseif ($ext == 'sdt') {
    $sdt = new PclZip($v['path']);
    $format = $teg = $image = $skin = '';

    $content = $sdt->extract(PCLZIP_OPT_BY_NAME, 'config.stc', PCLZIP_OPT_EXTRACT_AS_STRING);
    if ($content) {
        $format = 'stc';
    } else {
        $content = $sdt->extract(PCLZIP_OPT_BY_NAME, 'config.spc', PCLZIP_OPT_EXTRACT_AS_STRING);
        $format = 'spc';
    }

    $xml = simplexml_load_string($content[0]['content']);

    switch ($format) {
        case 'stc':
            foreach ($xml->resource_assignment->res as $f) {
                if ($f['name'] == 'Idle background animation') {
                    $teg = (string)$f['src'];
                    break;
                } else {
                    if ($f['name'] == 'Color skin') {
                        $skin = (string)$f['src'];
                    }
                }
            }
            if (!$teg) {
                if ($skin) {
                    $content = $sdt->extract(PCLZIP_OPT_BY_NAME, $skin, PCLZIP_OPT_EXTRACT_AS_STRING);
                } else {
                    $content = $sdt->extract(PCLZIP_OPT_BY_PREG, '#\.scs$#', PCLZIP_OPT_EXTRACT_AS_STRING);
                }

                if ($content) {
                    file_put_contents($name, $content[0]['content']);
                    $scs = new PclZip($name);
                    $image = $scs->extract(
                        PCLZIP_OPT_BY_NAME,
                        'SkinApplicationImage.jpg',
                        PCLZIP_OPT_EXTRACT_AS_STRING
                    );
                    if (!$image) {
                        $image = $scs->extract(
                            PCLZIP_OPT_BY_NAME,
                            'SkinApplicationImage.gif',
                            PCLZIP_OPT_EXTRACT_AS_STRING
                        );
                    }
                    if (!$image) {
                        $image = $scs->extract(
                            PCLZIP_OPT_BY_NAME,
                            'SkinApplicationImage.png',
                            PCLZIP_OPT_EXTRACT_AS_STRING
                        );
                    }
                    if (!$image) {
                        $image = $scs->extract(
                            PCLZIP_OPT_BY_NAME,
                            'SkinApplicationImage.bmp',
                            PCLZIP_OPT_EXTRACT_AS_STRING
                        );
                    }
                    if (!$image) {
                        $image = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.jpg$#', PCLZIP_OPT_EXTRACT_AS_STRING);
                    }
                    if (!$image) {
                        $image = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.gif$#', PCLZIP_OPT_EXTRACT_AS_STRING);
                    }
                    if (!$image) {
                        $image = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.png$#', PCLZIP_OPT_EXTRACT_AS_STRING);
                    }
                    if (!$image) {
                        $image = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.bmp$#', PCLZIP_OPT_EXTRACT_AS_STRING);
                    }
                }
            }
            break;


        case 'spc':
            foreach ($xml->format->res as $f) {
                if ($f['name'] == 'idle_wallpaper' || $f['name'] == 'screensaver_image'
                    || $f['name'] == 'switch_on_animation'
                ) {
                    $teg = (string)$f['src'];
                    break;
                }
            }
            break;
    }

    if (!$image) {
        $image = $sdt->extract(PCLZIP_OPT_BY_NAME, $teg, PCLZIP_OPT_EXTRACT_AS_STRING);
    }

    file_put_contents($name, $image[0]['content']);
    $info = getimagesize($name);

    if ($info[2] == 4 || $info[2] == 13) {
        Image::resize($name, $name . '.swf', 0, 0, Config::get('marker'));
        $location .= '.swf';
    } else {
        Image::resize($name, $name, 0, 0, Config::get('marker'));
    }
} elseif ($ext == 'scs') {
    $scs = new PclZip($v['path']);

    $content = $scs->extract(PCLZIP_OPT_BY_NAME, 'SkinApplicationImage.jpg', PCLZIP_OPT_EXTRACT_AS_STRING);
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_NAME, 'SkinApplicationImage.gif', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_NAME, 'SkinApplicationImage.png', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_NAME, 'SkinApplicationImage.bmp', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.jpg$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.gif$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.png$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $scs->extract(PCLZIP_OPT_BY_PREG, '#\.bmp$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }


    file_put_contents($name, $content[0]['content']);
    Image::resize($name, $name, 0, 0, Config::get('marker'));
} elseif ($ext == 'utz') {
    $utz = new PclZip($v['path']);


    $content = $utz->extract(PCLZIP_OPT_BY_NAME, 'Theme.xml', PCLZIP_OPT_EXTRACT_AS_STRING);
    if (!$content) {
        $content = $utz->extract(PCLZIP_OPT_BY_PREG, '#\.xml$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }

    $teg = (string)simplexml_load_string($content[0]['content'])->preview['file'];
    if (!$teg) {
        $teg = (string)simplexml_load_string($content[0]['content'])->wallpapers->wallpaper['file'];
    }

    if ($teg) {
        $image = $utz->extract(PCLZIP_OPT_BY_NAME, $teg, PCLZIP_OPT_EXTRACT_AS_STRING);
    } else {
        $image = $utz->extract(PCLZIP_OPT_BY_PREG, '#\.png$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }

    file_put_contents($name, $image[0]['content']);
    $info = getimagesize($name);

    if ($info[2] == 4 || $info[2] == 13) {
        Image::resize($name, $name . '.swf', 0, 0, Config::get('marker'));
        $location .= '.swf';
    } else {
        Image::resize($name, $name, 0, 0, Config::get('marker'));
    }
} elseif ($ext == 'apk') {
    $apk = new PclZip($v['path']);

    $content = $apk->extract(PCLZIP_OPT_BY_NAME, 'res/drawable/icon.png', PCLZIP_OPT_EXTRACT_AS_STRING);
    if (!$content) {
        $content = $apk->extract(PCLZIP_OPT_BY_NAME, 'res/drawable/main.png', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable/.*icon.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(PCLZIP_OPT_BY_PREG, '#res/drawable/.*\.png$#', PCLZIP_OPT_EXTRACT_AS_STRING);
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_NAME,
            'res/drawable-ldpi/icon.png',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_NAME,
            'res/drawable-ldpi/main.png',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable-ldpi/.*icon.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable-ldpi/.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_NAME,
            'res/drawable-mdpi/icon.png',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_NAME,
            'res/drawable-mdpi/main.png',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable-mdpi/.*icon.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable-mdpi/.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_NAME,
            'res/drawable-hdpi/icon.png',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_NAME,
            'res/drawable-hdpi/main.png',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable-hdpi/.*icon.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }
    if (!$content) {
        $content = $apk->extract(
            PCLZIP_OPT_BY_PREG,
            '#res/drawable-hdpi/.*\.png$#',
            PCLZIP_OPT_EXTRACT_AS_STRING
        );
    }


    file_put_contents($name, $content[0]['content']);
    Image::resize($name, $name, 0, 0, Config::get('marker'));
}

Http_Response::getInstance()->redirect($location, 301);
