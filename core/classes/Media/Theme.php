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
class Media_Theme
{
    /**
     * Получаем картинку из тем
     *
     * @param string $path
     * @return string
     */
    public static function getImage($path = '')
    {
        $name = Config::get('tpath') . '/' . str_replace('/', '--', mb_substr(strstr($path, '/'), 1)) . '.png';

        if (is_file($name)) {
            return $name;
        } elseif (is_file($name . '.gif')) {
            return $name;
        } elseif (is_file($name . '.swf')) {
            return $name . '.swf';
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'thm':
                $url = self::_saveThmImage($path, $name);
                break;

            case 'nth':
                $url = self::_saveNthImage($path, $name);
                break;

            case 'sdt':
                $url = self::_saveSdtImage($path, $name);
                break;

            case 'scs':
                $url = self::_saveScsImage($path, $name);
                break;

            case 'utz':
                $url = self::_saveUtzImage($path, $name);
                break;

            case 'apk':
                $url = self::_saveApkImage($path, $name);
                break;

            default:
                $url = null;
                break;
        }

        return $url;
    }


    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    protected static function _saveApkImage($path, $name)
    {
        $apk = new PclZip($path);

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
        return $name;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    protected static function _saveUtzImage($path, $name)
    {
        $utz = new PclZip($path);

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

        Image::resize($name, $name, 0, 0, Config::get('marker'));
        return $name;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    protected static function _saveScsImage($path, $name)
    {
        $scs = new PclZip($path);

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
        return $name;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    protected static function _saveNthImage($path, $name)
    {
        $nth = new PclZip($path);

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

        Image::resize($name, $name, 0, 0, Config::get('marker'));
        return $name;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    protected static function _saveSdtImage($path, $name)
    {
        $sdt = new PclZip($path);
        $teg = $image = $skin = '';

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
                    if ($f['name'] == 'idle_wallpaper' || $f['name'] == 'screensaver_image' || $f['name'] == 'switch_on_animation') {
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

        Image::resize($name, $name, 0, 0, Config::get('marker'));
        return $name;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return string
     */
    protected static function _saveThmImage($path, $name)
    {
        $thm = new Archive_Tar($path);

        $content = $thm->extractInString('Theme.xml');
        if (!$content) {
            $content = $thm->extractInString(pathinfo($path, PATHINFO_FILENAME) . '.xml');
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
            preg_match('/<\?\s*xml\s*version\s*=\s*"1\.0"\s*\?>(.*)<\/.+>/isU', file_get_contents($path), $arr);
            $content = trim($arr[0]);
            unset($arr);
        }


        $load = (string)simplexml_load_string($content)->Standby_image['Source'];
        if (!$load) {
            $load = (string)simplexml_load_string($content)->Desktop_image['Source'];
        }

        if (!$load) {
            return null;
        }


        file_put_contents($name, $thm->extractInString($load));
        unset($load, $content);

        Image::resize($name, $name, 0, 0, Config::get('marker'));
        return $name;
    }


    /**
     * Получаем данные из тем
     *
     * @param int $id
     * @param string $path
     * @return array
     */
    public static function getInfo($id, $path = '')
    {
        $filename = pathinfo($path);
        $ext = strtolower($filename['extension']);

        switch ($ext) {
            case 'thm':
                $info = self::_getThmInfo($id, $path);
                break;


            default:
                $info = array('author' => '', 'version' => '', 'models' => '');
                break;
        }

        return $info;
    }


    /**
     * Получаем данные из thm тем
     *
     * @param int $id
     * @param string $path
     * @return array
     */
    protected static function _getThmInfo($id, $path)
    {
        if (is_file(SEA_CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
            return unserialize(file_get_contents(SEA_CORE_DIRECTORY . '/cache/' . $id . '.dat'));
        }

        $ver_thm = array(
            1 => 'T68, T230, T290, T300, T310',
            '1.0' => 'T68, T230, T290, T300, T310',
            '1.1' => 'T68, T230, T290, T300, T310',
            '1.2' => 'T68, T230, T290, T300, T310',
            '1.3' => 'T68, T230, T290, T300, T310',
            '1.4' => 'T68, T230, T290, T300, T310',
            '1.5' => 'T68, T230, T290, T300, T310',
            '1.6' => 'T68, T230, T290, T300, T310',
            '1.7' => 'T68, T230, T290, T300, T310',
            '1.8' => 'T68, T230, T290, T300, T310',
            '1.9' => 'T68, T230, T290, T300, T310',
            2 => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.0' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.1' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.2' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.3' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.4' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.5' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.6' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.7' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.8' => 'J210, J220, J230, T610, T630, Z600, Z300',
            '2.9' => 'J210, J220, J230, T610, T630, Z600, Z300',
            3 => 'J300, K300, K500, K700, S700, Z1010',
            '3.0' => 'J300, K300, K500, K700, S700, Z1010',
            '3.1' => 'V800, Z800',
            '3.2' => 'V800, Z800',
            4 => 'K600, K750, W700, W800, Z520, Z525',
            '4.0' => 'K600, K750, W700, W800, Z520, Z525',
            '4.1' => 'K310, K320, K510,W200, W300, Z530, W550, W600, W810, Z550, Z558, W900',
            '4.5' => 'Z250, Z310, Z320, K550, K610, Z610, Z710, W610, W660, W710, K790, K800, K810, S500, W580, W830, W850, T650, K770, W880',
            '4.6' => 'K630, K660, K850, R300, R306, V640, W760, W890, W910, Z750',
            '4.7' => 'C702, C902, W760, W980, Z780',
            'UIQ3' => 'M600, P1, W950, W960, P990',
        );

        $thm = new Archive_Tar($path);


        if (!$file = $thm->extractInString(pathinfo($path, PATHINFO_FILENAME) . '.xml')) {
            $file = $thm->extractInString('Theme.xml');
        }

        if (!$file) {
            $list = $thm->listContent();
            $all = sizeof($list);
            for ($i = 0; $i < $all; ++$i) {
                if (pathinfo($list[$i]['filename'], PATHINFO_EXTENSION) === 'xml') {
                    $file = $thm->extractInString($list[$i]['filename']);
                    break;
                }
            }
        }


        // fix bug in Tar.php
        if (!$file) {
            preg_match('/<\?\s*xml\s*version\s*=\s*"1\.0"\s*\?>(.*)<\/.+>/isU', file_get_contents($path), $arr);
            $file = trim($arr[0]);
        }


        $load = simplexml_load_string($file);

        $out = array('author' => '', 'version' => '', 'models' => '');
        if ($load->Author_organization['Value']) {
            $out['author'] = (string)$load->Author_organization['Value'];
        }

        if ($load['version']) {
            $out['version'] = (string)$load['version'];

            if (in_array($load['version'], array_keys($ver_thm))) {
                $out['models'] = $ver_thm[(string)$load['version']];
            }
        }

        file_put_contents(SEA_CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($out));
        return $out;
    }


    /**
     * Поддерживается ли тема
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isSupported($ext)
    {
        return ($ext === 'thm' || $ext === 'nth' || $ext === 'utz' || $ext === 'sdt' || $ext === 'scs' || $ext === 'apk');
    }
}
