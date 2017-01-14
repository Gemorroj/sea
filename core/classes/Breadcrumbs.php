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
class Breadcrumbs
{
    /**
     * @var array
     */
    protected static $_breadcrumbs = array();

    /**
     * Бредкрамбсы
     *
     * @param array $path
     * @param bool $isDir
     */
    public static function init($path, $isDir = false)
    {
        $ex = explode('/', rtrim($path, '/'));
        $all = sizeof($ex);

        if ($all > 1) {
            $path = array();
            $prefix = '';

            for ($i = 0; $i < $all; ++$i) {
                if (!$isDir && ($i + 1) === $all) {
                    $path[] = $prefix . $ex[$i];
                } else {
                    $path[] = $prefix . $ex[$i] . '/';
                    $prefix .= $ex[$i] . '/';
                }
            }

            $q = Db_Mysql::getInstance()->prepare('
                SELECT `id`, ' . Language::buildFilesQuery() . '
                FROM `files`
                WHERE `path` IN(' . rtrim(str_repeat('?,', $all), ',') . ')
            ');
            $q->execute($path);

            foreach ($q as $s) {
                self::$_breadcrumbs[$s['id']] = $s['name'];
            }
            if (!$isDir) {
                end(self::$_breadcrumbs);
                $key = key(self::$_breadcrumbs);
                $val = array_pop(self::$_breadcrumbs);
                self::$_breadcrumbs['view/' . $key] = $val;
            }
        }
    }


    /**
     * @param string $key
     * @param string $value
     */
    public static function add($key, $value)
    {
        self::$_breadcrumbs[$key] = $value;
    }


    /**
     * @return array
     */
    public static function getBreadcrumbs()
    {
        return self::$_breadcrumbs;
    }
}
