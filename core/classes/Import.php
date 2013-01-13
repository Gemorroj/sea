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

require_once 'functions.php';

/**
 * Sea Downloads
 *
 * @author  Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Import
{
    /**
     * @var string
     */
    private $_importFolder;
    /**
     * @var string
     */
    private $_importFolderFiles;
    /**
     * @var string
     */
    private $_importFolderAbout;
    /**
     * @var string
     */
    private $_importFolderScreen;
    /**
     * @var string
     */
    private $_importFolderAttach;
    /**
     * @var string
     */
    private $_filesFolder;
    /**
     * @var string
     */
    private $_aboutFolder;
    /**
     * @var string
     */
    private $_screenFolder;
    /**
     * @var string
     */
    private $_attachFolder;

    /**
     * @var PDOStatement
     */
    private $_importQuery;
    /**
     * @var PDOStatement
     */
    private $_directoryExistsQuery;

    /**
     * @var array
     */
    private $_message = array();
    /**
     * @var array
     */
    private $_error = array();


    /**
     * @param array $setup
     */
    public function __construct($setup)
    {
        $this->_importFolder = $setup['importpath'];
        $this->_importFolderFiles = $setup['importpath'] . '/files';
        $this->_importFolderAbout = $setup['importpath'] . '/about';
        $this->_importFolderScreen = $setup['importpath'] . '/screen';
        $this->_importFolderAttach = $setup['importpath'] . '/attach';
        $this->_filesFolder = $setup['path'];
        $this->_aboutFolder = $setup['opath'];
        $this->_screenFolder = $setup['spath'];
        $this->_attachFolder = $setup['apath'];

        $this->_importQuery = MysqlDb::getInstance()->prepare('
            INSERT INTO `files` (
                `dir`, `path`, `name`, `rus_name`, `aze_name`, `tur_name`, `infolder`, `size`, `timeupload`
            ) VALUES (
                "0", ?, ?, ?, ?, ?, ?, ?, ?
            )
        ');
        $this->_directoryExistsQuery = MysqlDb::getInstance()->prepare('SELECT 1 FROM `files` WHERE `path` = ? AND `dir` = "1" LIMIT 1');
    }


    /**
     * @return array
     */
    public function importFiles()
    {
        $this->_importFilesRecursive($this->_importFolderFiles);

        return array('message' => $this->_message, 'error' => $this->_error);
    }


    /**
     * @param string $folder
     */
    private function _importFilesRecursive($folder)
    {
        $toFolder = $this->_filesFolder . strstr(ltrim(strstr($folder, '/'), '/'), '/') . '/';

        foreach ((array)array_diff(scandir($folder, 0), array('.', '..')) as $file) {
            if ($file[0] === '.') {
                continue;
            }
            if (is_dir($folder . '/' . $file) === true) {
                $q = $this->_directoryExistsQuery->execute(array($toFolder . $file . '/'));
                if (!$q) {
                    $this->_error[] = implode("\n", $this->_directoryExistsQuery->errorInfo());
                }
                if ($this->_directoryExistsQuery->rowCount() < 1) {
                    $result = addDir($file, $toFolder, $file, $file, $file, $file);
                    $this->_error = array_merge($this->_error, $result['error']);
                    $this->_message = array_merge($this->_message, $result['message']);
                }

                $this->_importFilesRecursive($folder . '/' . $file);
                continue;
            }
            if (is_file($folder . '/' . $file) === true) {
                $this->_importFile($folder . '/' . $file);
            }
        }
    }


    /**
     * @param string $file
     */
    private function _importFile($file)
    {
        $toFile = $this->_filesFolder . strstr(ltrim(strstr($file, '/'), '/'), '/');

        if (checkExt(pathinfo($file, PATHINFO_EXTENSION)) === false) {
            $this->_error[] = 'Импорт файла ' . $file . ' окончилась неудачно: недоступное расширение';
            return;
        }
        if (file_exists($toFile) === true) {
            $this->_error[] = 'Загрузка файла ' . $file . ' окончилась неудачно: файл ' . $toFile . ' уже существует';
            return;
        }

        if (copy($file, $toFile) === true) {
            $aze_name = $tur_name = $rus_name = $name = basename($toFile, '.' . pathinfo($toFile, PATHINFO_EXTENSION));

            $infolder = dirname($toFile) . '/';

            $this->_importQuery->execute(array(
                $toFile,
                $name,
                $rus_name,
                $aze_name,
                $tur_name,
                $infolder,
                filesize($toFile),
                filectime($toFile)
            ));
            $id = MysqlDb::getInstance()->lastInsertId();

            dir_count($infolder, true);
            chmod($toFile, 0644);

            $this->_importFileData($id, $toFile);

            $this->_message[] = 'Импорт файла ' . $file . ' прошел успешно';
        } else {
            $err = error_get_last();
            $this->_error[] = 'Импорт файла ' . $file . ' окончился неудачно: ' . $err['message'];
        }
    }


    /**
     * @param int $id
     * @param string $file
     */
    private function _importFileData($id, $file)
    {
        $result = array('message' => array(), 'error' => array());

        $preFileAbout = $this->_importFolderAbout . strstr($file, '/');
        $preFileScreen = $this->_importFolderScreen . strstr($file, '/');
        $preFileAttach = $this->_importFolderAttach . strstr($file, '/') . '_';

        if (file_exists($preFileAbout . '.txt') === true) {
            $result = addAbout($file, file_get_contents($preFileAbout . '.txt'));
            $this->_error = array_merge($this->_error, $result['error']);
            $this->_message = array_merge($this->_message, $result['message']);
        }

        if (file_exists($preFileScreen . '.gif') === true) {
            $result = addScreen($file, $preFileScreen . '.gif');
            $this->_error = array_merge($this->_error, $result['error']);
            $this->_message = array_merge($this->_message, $result['message']);
        } elseif (file_exists($preFileScreen . '.jpg') === true) {
            $result = addScreen($file, $preFileScreen . '.jpg');
            $this->_error = array_merge($this->_error, $result['error']);
            $this->_message = array_merge($this->_message, $result['message']);
        } elseif (file_exists($preFileScreen . '.png') === true) {
            $result = addScreen($file, $preFileScreen . '.png');
            $this->_error = array_merge($this->_error, $result['error']);
            $this->_message = array_merge($this->_message, $result['message']);
        }

        $attach = glob($preFileAttach . '*');
        if ($attach) {
            $array = array();
            foreach ($attach as $v) {
                $result = addAttach($file, $id, $v, $array);
                $this->_error = array_merge($this->_error, $result['error']);
                $this->_message = array_merge($this->_message, $result['message']);

                // fix
                list(, $name) = explode('_', basename($v), 2);
                $array[] = $name;
            }
        }
    }
}
