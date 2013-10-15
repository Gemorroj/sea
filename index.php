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


define('START_TIME', microtime(true));
require dirname(__FILE__) . '/core/config.php';

$route = array(
    '' => 'index.php',
    '(?P<id>[0-9]+)' => 'index.php',
    '(?P<id>[0-9]+)/(?P<page>[0-9]+)' => 'index.php',
    'view/(?P<id>[0-9]+)' => 'view.php',
    'view_comments/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'view_comments.php',
    'news/*(?P<page>[0-9]*)' => 'news.php',
    'news_comments/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'news_comments.php',
    'rate/(?P<i>[0-9]+)' => 'rate.php',
    'search/*(?P<page>[0-9]*)' => 'search.php',
    'top/*(?P<page>[0-9]*)' => 'top.php',
    'new/*(?P<page>[0-9]*)' => 'new.php',
    'load/(?P<id>[0-9]+)' => 'load.php',
    'ffmpeg/(?P<id>[0-9]+)' => 'ffmpeg.php',
    'apic/(?P<id>[0-9]+)' => 'apic.php',
    'email/(?P<id>[0-9]+)' => 'email.php',
    'abuse/(?P<id>[0-9]+)' => 'abuse.php',
    'im/(?P<id>[0-9]+)' => 'im.php',
    'theme/(?P<id>[0-9]+)' => 'theme.php',
    'jar/(?P<id>[0-9]+)' => 'jar.php',
    'jad/(?P<id>[0-9]+)' => 'jad.php',
    'cut/(?P<id>[0-9]+)' => 'cut.php',
    'txt_zip/(?P<id>[0-9]+)' => 'txt_zip.php',
    'txt_jar/(?P<id>[0-9]+)' => 'txt_jar.php',
    'settings' => 'settings.php',
    'stat' => 'stat.php',
    'table' => 'table.php',
    'exchanger' => 'exchanger.php',
    'service' => 'service.php',
    'rss' => 'rss.php',
    'read/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'read.php',
    'zip/(?P<id>[0-9]+)/*(?P<page>[0-9]*)' => 'zip.php',
    'zip/(?P<action>preview)/(?P<id>[0-9]+)/(?P<name>.+)/(?P<page>[0-9]*)' => 'zip.php',
    'zip/(?P<action>down)/(?P<id>[0-9]+)/(?P<name>.+)' => 'zip.php',
);

foreach ($route as $regexp => $path) {
    $matches = null;
    if (preg_match('#^' . DIRECTORY . $regexp . '/*$#', $_SERVER['REQUEST_URI'], $matches)) {
        foreach ($matches as $key => $value) {
            if (false === is_int($key)) {
                Http_Request::addGet($key, $value);
            }
        }
        include './core/controllers/' . $path;
        break;
    }
}
