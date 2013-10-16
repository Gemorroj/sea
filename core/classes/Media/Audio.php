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
class Media_Audio
{
    /**
     * Данные об аудио файле
     *
     * @param int $id
     * @param string $path
     *
     * @return array
     */
    public static function getInfo($id, $path)
    {
        if (is_file(SEA_CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
            return unserialize(file_get_contents(SEA_CORE_DIRECTORY . '/cache/' . $id . '.dat'));
        }

        $path = SEA_CORE_DIRECTORY . '/../' . $path;


        switch (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            case 'ogg':
            case 'aac':
                $tmp = self::_getOggInfo($path);
                break;

            case 'wav':
                $tmp = self::_getWavInfo($path);
                break;

            case 'mp3':
                $tmp = self::_getMp3Info($path);
                break;

            default:
                $tmp = array();
                break;
        }

        file_put_contents(SEA_CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($tmp));
        return $tmp;
    }

    /**
     * Данные об MP3 файле
     *
     * @param string $path
     *
     * @return array
     */
    protected static function _getMp3Info($path)
    {
        $id3 = new MP3_Id3($path);
        $meta = $id3->getMeta();
        $tags = $id3->getTags();

        return array(
            'channels' => null,
            'sampleRate' => $meta->getFrequency(),
            'avgBitrate' => $meta->getBitrate() * 1024,
            'streamLength' => $meta->getLength(),
            'tag' => array(
                'title' => self::_fixTag($tags->getTrackTitle()),
                'artist' => self::_fixTag($tags->getArtistName()),
                'album' => self::_fixTag($tags->getAlbumTitle()),
                'date' => self::_fixTag($tags->getYear()),
                'genre' => self::_fixTag($tags->getGenre()->getName()),
                'comment' => self::_fixTag($tags->getComment()),
                'apic' => $tags->getPicture()->getData()
            )
        );
    }

    /**
     * Данные об WAV файле
     *
     * @param string $path
     *
     * @return array
     */
    protected static function _getWavInfo($path)
    {
        $wav = new File_Wav($path);
        $info = $wav->getInfo();

        return array(
            'channels' => $info->getChannels(),
            'sampleRate' => $info->getFramerate(),
            'avgBitrate' => $info->getByterate(),
            'streamLength' => $info->getLength(),
            'tag' => array(
                'title' => null,
                'artist' => null,
                'album' => null,
                'date' => null,
                'genre' => null,
                'comment' => null,
                'apic' => null
            )
        );
    }

    /**
     * Данные об OGG файле
     *
     * @param string $path
     *
     * @return array
     */
    protected static function _getOggInfo($path)
    {
        $tmp = array();

        try {
            $ogg = new File_Ogg($path);
            $obj = & current($ogg->_streams);

            function getWavData($key)
            {
                global $obj;
                if (isset($obj->_comments[$key])) {
                    return self::_fixTag($obj->_comments[$key]);
                }
                return '';
            }

            $tmp = array(
                'channels' => $obj->_channels,
                'sampleRate' => $obj->_sampleRate,
                'avgBitrate' => $obj->_avgBitrate,
                'streamLength' => $obj->_streamLength,
                'tag' => array(
                    'title' => getWavData('TITLE'),
                    'artist' => getWavData('ARTIST'),
                    'album' => getWavData('ALBUM'),
                    'date' => getWavData('DATE'),
                    'genre' => getWavData('GENRE'),
                    'comment' => getWavData('COMMENT'),
                    'apic' => null
                )
            );
        } catch (Exception $e) {}

        return $tmp;
    }

    /**
     * Поддерживается ли аудио
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isSupported($ext)
    {
        return ($ext === 'mp3' || $ext === 'wav' || $ext === 'wma' || $ext === 'ogg' || $ext === 'aac');
    }


    /**
     * Поддерживается ли проигрывание аудио в плеере
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isPlayerSupported($ext)
    {
        return ($ext === 'mp3' || $ext === 'ogg' || $ext === 'aac');
    }


    /**
     * @param string $str
     *
     * @return string
     */
    protected static function _fixTag($str)
    {
        return Helper::str2utf8(trim(str_replace(array(chr(0), chr(1)), '', $str)));
    }
}
