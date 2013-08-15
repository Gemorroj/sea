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
        if (file_exists(CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
            return unserialize(file_get_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat'));
        }

        $path = CORE_DIRECTORY . '/../' . $path;

        $tmpa = array();
        $filename = pathinfo($path);
        $ext = strtolower($filename['extension']);

        if ($ext === 'mp3' || $ext === 'wav') {
            $audio = new AudioFile;
            $audio->loadFile($path);

            if ($audio->wave_length > 0) {
                $length = $audio->wave_length;
            } else {
                $mp3 = new mp3($path);
                $mp3->setFileInfoExact();
                $length = $mp3->time;
            }
            $comments = array();

            if (isset($audio->id3v2->APIC) && $audio->id3v2->APIC) {
                $apic = $audio->id3v2->APIC;
                $pos = strpos($apic,  "\0") + 1;
                $apic = substr($apic, $pos);
                $pos = strpos($apic,  "\0") + 1;
                $apic = substr($apic, $pos);


                function apicFix($apic)
                {
                    // fix 1
                    $pos = strpos($apic,  "\0") + 1;
                    $apic = substr($apic, $pos);

                    $apic = str_replace("\xFF\x00\x00", "\xFF\x00", $apic);
                    // end fix 1
                    return $apic;
                }

                function apicCheckFix($apic)
                {
                    // fix 2
                    $tmp = @imagecreatefromstring($apic);
                    if ($tmp) {
                        ob_start();
                        imagejpeg($tmp);
                        $apic = ob_get_contents();
                        ob_end_clean();
                        imagedestroy($tmp);
                    } else {
                        $apic = false;
                    }
                    // end fix 2
                    return $apic;
                }

                $fixApic = apicCheckFix($apic);
                if (!$fixApic) {
                    $fixApic = apicFix($apic);
                    if ($fixApic) {
                        $fixApic = apicCheckFix($fixApic);
                    }
                }

                $comments['APIC'] = $fixApic;
            } else {
                $comments['APIC'] = false;
            }
            if (isset($audio->id3_title)) {
                $comments['TITLE'] = Helper::str2utf8($audio->id3_title);
            } else {
                $comments['TITLE'] = '';
            }
            if (isset($audio->id3_artist)) {
                $comments['ARTIST'] = Helper::str2utf8($audio->id3_artist);
            } else {
                $comments['ARTIST'] = '';
            }
            if (isset($audio->id3_album)) {
                $comments['ALBUM'] = Helper::str2utf8($audio->id3_album);
            } else {
                $comments['ALBUM'] = '';
            }
            if (isset($audio->id3_year)) {
                $comments['DATE'] = Helper::str2utf8($audio->id3_year);
            } else {
                $comments['DATE'] = '';
            }
            if (isset($audio->id3_genre)) {
                $comments['GENRE'] = Helper::str2utf8($audio->id3_genre);
            } else {
                $comments['GENRE'] = '';
            }
            if (isset($audio->id3_comment)) {
                $comments['COMMENT'] = Helper::str2utf8($audio->id3_comment);
            } else {
                $comments['COMMENT'] = '';
            }

            $tmpa = array(
                'channels' => $audio->wave_channels,
                'sampleRate' => $audio->wave_framerate,
                'avgBitrate' => intval($audio->wave_byterate) * 1024,
                'streamLength' => $length,
                'tag' => array(
                    'title' => trim(str_replace(array(chr(0), chr(1)), '', $comments['TITLE'])),
                    'artist' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ARTIST'])),
                    'album' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ALBUM'])),
                    'date' => $comments['DATE'],
                    'genre' => $comments['GENRE'],
                    'comment' => trim(str_replace(array(chr(0), chr(1)), '', $comments['COMMENT'])),
                    'apic' => $comments['APIC']
                )
            );
        } elseif ($ext === 'ogg') {
            try {
                $ogg = new File_Ogg($path);
                $obj = & current($ogg->_streams);
                $comments = array();

                if (isset($obj->_comments['TITLE'])) {
                    $comments['TITLE'] = Helper::str2utf8($obj->_comments['TITLE']);
                } else {
                    $comments['TITLE'] = '';
                }
                if (isset($obj->_comments['ARTIST'])) {
                    $comments['ARTIST'] = Helper::str2utf8($obj->_comments['ARTIST']);
                } else {
                    $comments['ARTIST'] = '';
                }
                if (isset($obj->_comments['ALBUM'])) {
                    $comments['ALBUM'] = Helper::str2utf8($obj->_comments['ALBUM']);
                } else {
                    $comments['ALBUM'] = '';
                }
                if (isset($obj->_comments['DATE'])) {
                    $comments['DATE'] = Helper::str2utf8($obj->_comments['DATE']);
                } else {
                    $comments['DATE'] = '';
                }
                if (isset($obj->_comments['GENRE'])) {
                    $comments['GENRE'] = Helper::str2utf8($obj->_comments['GENRE']);
                } else {
                    $comments['GENRE'] = '';
                }
                if (isset($obj->_comments['COMMENT'])) {
                    $comments['COMMENT'] = Helper::str2utf8($obj->_comments['COMMENT']);
                } else {
                    $comments['COMMENT'] = '';
                }

                $tmpa = array(
                    'channels' => $obj->_channels,
                    'sampleRate' => $obj->_sampleRate,
                    'avgBitrate' => $obj->_avgBitrate,
                    'streamLength' => $obj->_streamLength,
                    'tag' => array(
                        'title' => trim(str_replace(array(chr(0), chr(1)), '', $comments['TITLE'])),
                        'artist' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ARTIST'])),
                        'album' => trim(str_replace(array(chr(0), chr(1)), '', $comments['ALBUM'])),
                        'date' => $comments['DATE'],
                        'genre' => $comments['GENRE'],
                        'comment' => trim(str_replace(array(chr(0), chr(1)), '', $comments['COMMENT'])),
                        'apic' => false
                    )
                );
            } catch (Exception $e) {}
        }

        file_put_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($tmpa));
        return $tmpa;
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
        return ($ext === 'mp3' || $ext === 'wav' || $ext === 'ogg' || $ext === 'aac');
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
}
