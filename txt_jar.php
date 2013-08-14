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


// Проверка переменных
$id = intval($_GET['id']);
// Получаем инфу о файле
$v = getFileInfo($id);


if (file_exists($v['path'])) {
    updFileLoad($id);

    $nm = array_reverse(explode('.', basename($v['path'])));
    $nm = $nm[1];
    $tmp = Config::get('jpath') . '/' . str_replace('/', '--', mb_substr(strstr($v['path'], '/'), 1)) . '.jar';

    if (!file_exists($tmp)) {
        $f = Helper::str2utf8(file_get_contents($v['path']));

        copy('core/resources/book.zip', $tmp);
        copy('core/resources/props.ini', Config::get('jpath') . '/props.ini');
        copy('core/resources/MANIFEST.MF', Config::get('jpath') . '/MANIFEST.MF');

        $arr = str_split($f, 25600);
        $all = sizeof($arr);
        $ar = file('core/resources/props.ini');

        $ar[] = chr(0) . chr(10) . chr(0) . wordwrap('J/textfile.txt.label=1', 1, chr(0), true);
        for ($i = 1; $i < $all; ++$i) {
            $ar[] = chr(10) . chr(0) . wordwrap('J/textfile' . $i . '.txt.label=' . ($i + 1), 1, chr(0), true);
        }
        $ar[] = chr(10);

        file_put_contents(Config::get('jpath') . '/props.ini', $ar);
        file_put_contents(
            Config::get('jpath') . '/MANIFEST.MF',
            'Manifest-Version: 1.0
MicroEdition-Configuration: CLDC-1.0
MicroEdition-Profile: MIDP-1.0
MIDlet-Name: ' . $nm . '
MIDlet-Vendor: Gemor Reader
MIDlet-1: ' . $nm . ', /icon.png, br.BookReader
MIDlet-Version: 1.6
MIDlet-Info-URL: http://' . $_SERVER['HTTP_HOST'] . '
MIDlet-Delete-Confirm: GoodBye =)',
            FILE_APPEND
        );

        $zip = new PclZip(dirname(__FILE__) . '/' . $tmp);
        //echo 'ERROR : '.$zip->errorInfo(true);

        $zip->add(dirname(__FILE__) . '/' . Config::get('jpath') . '/props.ini', PCLZIP_OPT_REMOVE_ALL_PATH);
        //echo 'ERROR : '.$zip->errorInfo(true);

        $zip->add(
            dirname(__FILE__) . '/' . Config::get('jpath') . '/MANIFEST.MF',
            PCLZIP_OPT_REMOVE_ALL_PATH,
            PCLZIP_OPT_ADD_PATH,
            'META-INF'
        );
        //echo 'ERROR : '.$zip->errorInfo(true);

        file_put_contents(Config::get('jpath') . '/textfile.txt', $arr[0]);

        $zip->add(dirname(__FILE__) . '/' . Config::get('jpath') . '/textfile.txt', PCLZIP_OPT_REMOVE_ALL_PATH);
        //echo 'ERROR : '.$zip->errorInfo(true);

        unlink(Config::get('jpath') . '/textfile.txt');

        for ($i = 1; $i < $all; ++$i) {
            file_put_contents(Config::get('jpath') . '/textfile' . $i . '.txt', $arr[$i]);

            $zip->add(
                dirname(__FILE__) . '/' . Config::get('jpath') . '/textfile' . $i . '.txt',
                PCLZIP_OPT_REMOVE_ALL_PATH
            );
            //echo 'ERROR : '.$zip->errorInfo(true);
            unlink(Config::get('jpath') . '/textfile' . $i . '.txt');
        }

        unlink(Config::get('jpath') . '/MANIFEST.MF');
        unlink(Config::get('jpath') . '/props.ini');

        chmod($tmp, 0644);
    }

    Http_Response::getInstance()->redirect('http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . str_replace('%2F', '/', rawurlencode($tmp)), 301);
} else {
    Http_Response::getInstance()->renderError(Language::get('error'));
}
