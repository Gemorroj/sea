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


header('Pragma: public');
header('Cache-Control: public, max-age=8640000');
header('Expires: ' . date('r', $_SERVER['REQUEST_TIME'] + 8640000));

require 'core/config.php';

// Если jad выключен
if (!Config::get('jad_change')) {
    Http_Response::getInstance()->renderError('Not found');
}

$id = intval(Http_Request::get('id'));
// Получаем инфу о файле
$v = Files::getFileInfo($id);

if (is_file($v['path'])) {
    Files::updateFileLoad($id);

    $zip = new PclZip($v['path']);
    $content = $zip->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);

    header('Content-type: text/vnd.sun.j2me.app-descriptor');
    header('Content-Disposition: attachment; filename="' . rawurlencode(basename($v['path'])) . '.jad";');

    echo trim($content[0]['content']) . "\n" .
        'MIDlet-Jar-Size: ' . filesize($v['path']) . "\n" .
        'MIDlet-Jar-URL: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . $v['path'];
} else {
    Http_Response::getInstance()->renderError(Language::get('error'));
}
