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


define('APANEL', true);
@set_time_limit(99999);
ini_set('max_execution_time', 99999);
ignore_user_abort(true);
ob_implicit_flush(1);
//clearstatcache();
ini_set('memory_limit', '256M');

chdir('../');

require 'moduls/config.php';
require 'moduls/header.php';


$HeadTime = microtime(true);

if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error('Error');
}


// скриншоты
chmod($setup['spath'], 0777);
// описания
chmod($setup['opath'], 0777);
// вложения
chmod($setup['apath'], 0777);

$scan = isset($_GET['scan']) ? $_GET['scan'] : $setup['path'];

echo '<div style="font-size: x-small;">';
$data = scaner($scan);
echo '</div>';


if ($data['errors']) {
    echo '<div class="no">' . implode('<br/>', $data['errors']) . '<br/></div>';
}

echo '<div class="mblock">БД обновлена!<br/></div>
Просканировано директорий: ' . $data['folders'] . '<br/>
Просканировано файлов: ' . $data['files'] . '<br/>
<div class="mblock" style="color:#b00;">
Внимание! Теперь следует пересчитать количество файлов в папках<br/>
Для продолжения нажмите на <a class="yes" href="apanel_count.php">ЭТУ</a> ссылку
</div>';
