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
class Media_Video
{
    /**
     * Данные о видео файле
     *
     * @param int $id
     * @param string $path
     *
     * @return array
     */
    public static function getInfo($id, $path)
    {
        if (is_file(CORE_DIRECTORY . '/cache/' . $id . '.dat') === true) {
            return unserialize(file_get_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat'));
        }

        $tmpa = array();
        $mov = new ffmpeg_movie($path, false);
        if ($mov) {
            $tmpa = array(
                'getVideoCodec' => $mov->getVideoCodec(),
                'GetFrameWidth' => $mov->GetFrameWidth(),
                'GetFrameHeight' => $mov->GetFrameHeight(),
                'getDuration' => $mov->getDuration(),
                'getBitRate' => $mov->getBitRate()
            );
            file_put_contents(CORE_DIRECTORY . '/cache/' . $id . '.dat', serialize($tmpa));
        }

        return $tmpa;
    }


    /**
     * Поддерживается ли видео
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isSupported($ext)
    {
        return (($ext === '3gp' || $ext === 'avi' || $ext === 'mp4' || $ext === 'flv' || $ext === 'webm') && extension_loaded('ffmpeg'));
    }


    /**
     * Поддерживается ли проигрывание видео в плеере
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isPlayerSupported($ext)
    {
        return ($ext === 'mp4' || $ext === 'flv' || $ext === 'webm');
    }
}
