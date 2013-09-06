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
class Scanner
{
    /**
     * Обновление данных файлов в БД
     *
     * @param string $path
     * @param string $name
     * @param string $rus_name
     * @param string $aze_name
     * @param string $tur_name
     * @param bool   $dir
     * @param bool   $insert
     *
     * @return PDOStatement
     */
    protected function _scanDb($path, $name, $rus_name, $aze_name, $tur_name, $dir = true, $insert = true)
    {
        static $preparedQueryInsert = null;
        static $preparedQueryUpdate = null;

        if ($preparedQueryInsert === null) {
            $preparedQueryInsert = Db_Mysql::getInstance()->prepare('
                INSERT INTO `files` (
                    `dir`, `size`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `timeupload`
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ');
        }
        if ($preparedQueryUpdate === null) {
            $preparedQueryUpdate = Db_Mysql::getInstance()->prepare('
                UPDATE `files`
                SET `name` = IF(`name` <> "", `name`, ?),
                `rus_name` = IF(`rus_name` <> "", `rus_name`, ?),
                `aze_name` = IF(`aze_name` <> "", `aze_name`, ?),
                `tur_name` = IF(`tur_name` <> "", `tur_name`, ?)
                WHERE `path` = ?
            ');
        }

        if ($insert) {
            if ($dir) {
                $preparedQueryInsert->bindValue(1, '1');
                $preparedQueryInsert->bindValue(2, 0);
            } else {
                $preparedQueryInsert->bindValue(1, '0');
                $preparedQueryInsert->bindValue(2, filesize($path));
            }
            $preparedQueryInsert->bindValue(3, $path);
            $preparedQueryInsert->bindValue(4, $name);
            $preparedQueryInsert->bindValue(5, $rus_name);
            $preparedQueryInsert->bindValue(6, $aze_name);
            $preparedQueryInsert->bindValue(7, $tur_name);
            $preparedQueryInsert->bindValue(8, dirname($path) . '/');
            $preparedQueryInsert->bindValue(9, filectime($path));

            return $preparedQueryInsert;
        } else {
            $preparedQueryUpdate->bindValue(1, $name);
            $preparedQueryUpdate->bindValue(2, $rus_name);
            $preparedQueryUpdate->bindValue(3, $aze_name);
            $preparedQueryUpdate->bindValue(4, $tur_name);
            $preparedQueryUpdate->bindValue(5, $path);

            return $preparedQueryUpdate;
        }
    }



    /**
     * Обновление данных загрузок в БД
     *
     * @param string $path
     * @param string $cont
     *
     * @return array
     */
    public function scan($path = '', $cont = 'folder.png')
    {
        static $folders = 0;
        static $files = 0;
        static $errors = array();
        static $preparedQuery = null;

        if ($preparedQuery === null) {
            $preparedQuery = Db_Mysql::getInstance()->prepare('SELECT `name`, `rus_name`, `aze_name`, `tur_name` FROM `files` WHERE `path` = ?');
        }


        if (is_readable($path) === false) {
            $errors[] = $path . ': не доступно для чтения. Вероятно, не хватает прав.';
            return array('folders' => $folders, 'files' => $files, 'errors' => $errors);
        }

        chmod($path, 0777);

        foreach (array_diff(scandir($path, 0), array('.', '..')) as $file) {
            if ($file[0] === '.') {
                continue;
            }

            $f = str_replace('//', '/', $path . '/' . $file);
            $pathinfo = pathinfo($f);

            $is_dir = is_dir($f);
            if ($is_dir === true) {
                $f .= '/';
            }
            $is_file = ($is_dir === false && $pathinfo['basename'] != $cont && is_file($f) === true);

            if (!$preparedQuery->execute(array($f))) {
                $errors[] = implode("\n", $preparedQuery->errorInfo());
                continue;
            }

            $insert = true;
            if ($preparedQuery->rowCount() > 0) {
                $insert = false;
                $row = $preparedQuery->fetch();
                if ($row['name'] != '' && $row['rus_name'] != '' && $row['aze_name'] != '' && $row['tur_name'] != '') {
                    if ($is_dir === true) {
                        $folders++;
                        $this->scan($f);
                    } elseif ($is_file === true) {
                        $files++;
                    }
                    continue;
                }
            }



            $aze_name = $tur_name = $rus_name = $name = ($is_dir === true ? $pathinfo['basename'] : $pathinfo['filename']);
            if ($name == '') {
                $tmpErr = error_get_last();
                $errors[] = $tmpErr['message'];
                continue;
            }

            // транслит
            if ($name[0] === '!') {
                $aze_name = $tur_name = $rus_name = $name = substr($name, 1);
                $rus_name = Translit::trans($rus_name);
            }

            if ($is_dir === true) {
                // скриншоты
                $screen = Config::get('spath') . mb_substr($f, mb_strlen(Config::get('path')));
                if (is_file($screen) === false) {
                    mkdir($screen, 0777);
                }
                chmod($screen, 0777);

                // описания
                $desc = Config::get('opath') . mb_substr($f, mb_strlen(Config::get('path')));
                if (is_file($desc) === false) {
                    mkdir($desc, 0777);
                }
                chmod($desc, 0777);

                // вложения
                $attach = Config::get('apath') . mb_substr($f, mb_strlen(Config::get('path')));
                if (is_file($attach) === false) {
                    mkdir($attach, 0777);
                }
                chmod($attach, 0777);

                $q = $this->_scanDb($f, $name, $rus_name, $aze_name, $tur_name, true, $insert);
                if (!$q->execute()) {
                    $errors[] = implode("\n", $q->errorInfo());
                }

                $folders++;
                $this->scan($f);
            } elseif ($is_file === true) {
                $files++;
                $q = $this->_scanDb($f, $name, $rus_name, $aze_name, $tur_name, false, $insert);
                if (!$q->execute()) {
                    $errors[] = implode("\n", $q->errorInfo());
                }
            }
        }

        return array('folders' => $folders, 'files' => $files, 'errors' => $errors);
    }


    /**
     * Обновление количества файлов в директориях
     */
    public function scanCount()
    {
        $db = Db_Mysql::getInstance();

        $q1 = $db->prepare('SELECT COUNT(1) FROM `files` WHERE `infolder` LIKE ? AND `hidden` = "0"');
        $q2 = $db->prepare('UPDATE `files` SET `dir_count` = ? WHERE `path` = ?');

        foreach ($db->query('SELECT `path` FROM `files` WHERE `dir` = "1" GROUP BY `path`') as $dir) {
            $q1->execute(array($db->escapeLike($dir['path']) . '%'));
            $count = $q1->fetchColumn();

            $q2->execute(array($count, $dir['path']));
        }
    }
}
