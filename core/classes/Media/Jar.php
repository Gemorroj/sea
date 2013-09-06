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
class Media_Jar
{
    /**
     * Получаем картинку из jar файла
     *
     * @param string $path
     * @return string
     */
    public static function getImage($path = '')
    {
        $name = Config::get('ipath') . '/' . str_replace('/', '--', mb_substr(strstr($path, '/'), 1)) . '.png';
        if (is_file($name)) {
            return $name;
        }


        $icon = array();
        $archive = new PclZip($path);

        $list = $archive->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);


        if (@$list[0]['content']) {
            if (!$icon) {
                preg_match('/MIDlet\-Icon:[\s*](.*)/iux', $list[0]['content'], $arr);

                if (@$arr[1]) {
                    foreach (explode(',', $arr[1]) as $v) {
                        $v = trim(trim($v), '/');
                        if (strtolower(pathinfo($v, PATHINFO_EXTENSION)) === 'png') {
                            $icon = $archive->extract(PCLZIP_OPT_BY_NAME, $v, PCLZIP_OPT_EXTRACT_AS_STRING);
                            break;
                        }
                    }
                }
            }

            if (!$icon) {
                preg_match('/MIDlet\-1:[\s*](.*)/iux', $list[0]['content'], $arr);

                if (@$arr[1]) {
                    foreach (explode(',', $arr[1]) as $v) {
                        $v = trim(trim($v), '/');
                        if (strtolower(pathinfo($v, PATHINFO_EXTENSION)) === 'png') {
                            $icon = $archive->extract(PCLZIP_OPT_BY_NAME, $v, PCLZIP_OPT_EXTRACT_AS_STRING);
                            break;
                        }
                    }
                }
            }
        }


        if (@$icon[0]['content']) {
            file_put_contents($name, $icon[0]['content']);
            return $name;
        }

        return null;
    }


    /**
     * Поддерживается ли jar
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isSupported($ext)
    {
        return ($ext === 'jar');
    }
}
