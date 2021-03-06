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
class Media_Image
{
    /**
     * Поддерживается ли картинка
     *
     * @param string $ext
     *
     * @return bool
     */
    public static function isSupported($ext)
    {
        return ($ext === 'gif' || $ext === 'jpg' || $ext === 'jpe' || $ext === 'jpeg' || $ext === 'png' || $ext === 'bmp');
    }


    /**
     * @param string $file
     *
     * @return bool
     */
    public static function toPng($file)
    {
        switch (self::getType($file)) {
            case IMAGETYPE_GIF:
                $im = imagecreatefromgif($file);
                $result = imagepng($im, $file);
                imagedestroy($im);
                break;

            case IMAGETYPE_JPEG:
                $im = imagecreatefromjpeg($file);
                $result = imagepng($im, $file);
                imagedestroy($im);
                break;

            case IMAGETYPE_BMP:
                $im = Image_Bmp::imagecreatefrombmp($file);
                $result = imagepng($im, $file);
                imagedestroy($im);
                break;

            case IMAGETYPE_PNG:
                $result = true;
                break;

            default:
                $result = false;
                break;
        }

        return $result;
    }


    /**
     * @param string $file
     *
     * @return int|null
     */
    public static function getType($file)
    {
        $image = getimagesize($file);
        switch ($image[2]) {
            case IMAGETYPE_GIF:
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
            case IMAGETYPE_BMP:
                return $image[2];
                break;
            default:
                return null;
                break;
        }
    }
}
