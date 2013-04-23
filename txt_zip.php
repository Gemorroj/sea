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

    $tmp = $setup['zpath'] . '/' . str_replace('/', '--', mb_substr(strstr($v['path'], '/'), 1)) . '.zip';

    if (!file_exists($tmp)) {
        include_once 'core/classes/pclzip.lib.php';

        $zip = new PclZip($tmp);

        function cb($p_event, &$p_header)
        {
            $p_header['stored_filename'] = basename($p_header['filename']);

            return 1;
        }

        $zip->create($v['path'], PCLZIP_CB_PRE_ADD, 'cb');
        chmod($tmp, 0644);
    }

    redirect('Location: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . str_replace('%2F', '/', rawurlencode($tmp)), 301);
} else {
    error($language['error']);
}
