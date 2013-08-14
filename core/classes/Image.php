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
class Image
{
    /**
     * Маркер картинок
     *
     * @param resource $image
     * @param resource $watermark
     *
     * @return resource
     */
    public static function marker($image, $watermark)
    {
        if (!is_resource($image) || !is_resource($watermark)) {
            return null;
        }


        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        $tmpW = $watermarkWidth = imagesx($watermark);
        $tmpH = $watermarkHeight = imagesy($watermark);

        if ($imageWidth < $watermarkWidth || $imageHeight < $watermarkHeight) {
            if ($imageWidth < $watermarkWidth) {
                $watermarkWidth = $imageWidth;
                $watermarkHeight *= $watermarkWidth / $tmpW;
            } else {
                $watermarkHeight = $imageHeight / 2;
                $watermarkWidth *= $watermarkHeight / $tmpH;
            }

            $f = imagecreatetruecolor($watermarkWidth, $watermarkHeight);

            $transparencyIndex = imagecolortransparent($watermark);
            $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);

            if ($transparencyIndex >= 0) {
                $transparencyColor = imagecolorsforindex($watermark, $transparencyIndex);
            }

            $transparencyIndex = imagecolorallocate(
                $f,
                $transparencyColor['red'],
                $transparencyColor['green'],
                $transparencyColor['blue']
            );
            imagefill($f, 0, 0, $transparencyIndex);
            imagecolortransparent($f, $transparencyIndex);

            imagecopyresampled($f, $watermark, 0, 0, 0, 0, $watermarkWidth, $watermarkHeight, $tmpW, $tmpH);
            $watermark = & $f;
        }

        $new = imagecreatetruecolor($imageWidth, $imageHeight);
        $footH = $imageHeight - $watermarkHeight;

        for ($j = 0; $j < $imageHeight; ++$j) {
            for ($i = 0; $i < $imageWidth; ++$i) {
                $rgb = imagecolorsforindex($image, imagecolorat($image, $i, $j));

                if (Config::get('marker_where') === 'top' && $j < $watermarkHeight && $i < $watermarkWidth) {
                    $rgb2 = imagecolorsforindex($watermark, @imagecolorat($watermark, $i, $j));
                    if ($rgb2['alpha'] != 127) {
                        $rgb['red'] = intval(($rgb['red'] + $rgb2['red']) / 2);
                        $rgb['green'] = intval(($rgb['green'] + $rgb2['green']) / 2);
                        $rgb['blue'] = intval(($rgb['blue'] + $rgb2['blue']) / 2);
                    }
                } else {
                    if (Config::get('marker_where') === 'foot' && $j >= $footH && $i < $watermarkWidth) {
                        $rgb2 = imagecolorsforindex($watermark, @imagecolorat($watermark, $i, $j - $footH));
                        if ($rgb2['alpha'] != 127) {
                            $rgb['red'] = intval(($rgb['red'] + $rgb2['red']) / 2);
                            $rgb['green'] = intval(($rgb['green'] + $rgb2['green']) / 2);
                            $rgb['blue'] = intval(($rgb['blue'] + $rgb2['blue']) / 2);
                        }
                    }
                }

                $ind = imagecolorexact($new, $rgb['red'], $rgb['green'], $rgb['blue']);
                if ($ind < 1) {
                    $ind = imagecolorallocate($new, $rgb['red'], $rgb['green'], $rgb['blue']);
                    if ($ind < 1) {
                        $ind = imagecolorclosest($new, $rgb['red'], $rgb['green'], $rgb['blue']);
                    }
                }
                imagesetpixel($new, $i, $j, $ind);
            }
        }

        return $new;
    }


    /**
     * Упрощенный ресайзер картинок
     *
     * @param resource $data
     * @return resource
     */
    public static function resizeSimple($data)
    {
        if (false === is_resource($data)) {
            return false;
        }

        $hn = imagesy($data);
        $wn = imagesx($data);

        list($w, $h) = explode('*', Config::get('prev_size'));

        $sxy = round($wn / $hn, 3);
        if ($sxy < 1) {
            $w = intval($h * $sxy);
        } else {
            $h = intval($w / $sxy);
        }

        $im = imagecreatetruecolor($w, $h);
        imagecopyresampled($im, $data, 0, 0, 0, 0, $w, $h, $wn, $hn);

        return $im;
    }


    /**
     * Ресайзер картинок
     *
     * @param string $in
     * @param string $out
     * @param int $w
     * @param int $h
     * @param bool $marker
     *
     * @return bool
     */
    public static function resize($in = '', $out = '', $w = 0, $h = 0, $marker = false)
    {
        if (false === is_writable(dirname($out))) {
            return false;
        }

        //$out = pathinfo($out);
        //$out = realpath($out['dirname']) . '/' . $out['basename'];

        if (!$w || !$h) {
            list($w, $h) = explode('*', Config::get('prev_size'));
        }


        list($wn, $hn, $type) = getimagesize($in);


        $sxy = round($wn / $hn, 3);
        if ($sxy < 1) {
            $w = intval($h * $sxy);
        } else {
            $h = intval($w / $sxy);
        }

        switch ($type) {
            case 1:
                if (Config::get('anim_change')) {
                    ini_set('memory_limit', '256M');

                    // GIF Поддержка анимации
                    $gif = new Image_GIFDecoder(file_get_contents($in));

                    $arr = $gif->GIFGetFrames();
                    $dly = $gif->GIFGetDelays();
                    $frames = $framed = array();

                    $a = sizeof($arr);
                    for ($i = 0; $i < $a; ++$i) {
                        $tmp1 = CORE_DIRECTORY . '/tmp/' . uniqid('img_') . '.gif';
                        $tmp2 = CORE_DIRECTORY . '/tmp/' . uniqid('img_') . '.gif';

                        file_put_contents($tmp1, $arr[$i]);
                        $resize = imagecreatefromgif($tmp1);

                        $image_p = imagecreatetruecolor($w, $h);
                        imagecopyresampled($image_p, $resize, 0, 0, 0, 0, $w, $h, $wn, $hn);


                        if ($marker) {
                            $image_p = self::marker($image_p, imagecreatefrompng(CORE_DIRECTORY . '/resources/marker.png'));
                        }

                        imagegif($image_p, $tmp2);
                        imagedestroy($image_p);
                        imagedestroy($resize);

                        $frames[] = file_get_contents($tmp2);
                        $framed[] = $dly[$i];

                        unlink($tmp1);
                        unlink($tmp2);
                    }
                    unset($gif, $arr, $dly);

                    $gif = new Image_GIFEncoder(
                        $frames,
                        $framed,
                        0,
                        2,
                        0, 0, 0,
                        0,
                        'bin'
                    );

                    unset($frames, $framed);

                    return file_put_contents($out, $gif->GetAnimation());
                    break;
                } else {
                    // GIF
                    $old = imagecreatefromgif($in);
                }
                break;


            case 2:
                // JPEG
                $old = imagecreatefromjpeg($in);
                break;


            case 3:
                // PNG
                $old = imagecreatefrompng($in);
                break;


            case 4:
            case 13:
                // SWF
                rename($in, $out);

                return true;
                break;


            case 6:
                // BMP
                $old = Image_Bmp::imagecreatefrombmp($in, CORE_DIRECTORY . '/tmp');
                break;


            default:
                return false;
                break;
        }


        $new = imagecreatetruecolor($w, $h);
        imagecopyresampled($new, $old, 0, 0, 0, 0, $w, $h, $wn, $hn);

        if ($marker) {
            $new = self::marker($new, imagecreatefrompng(CORE_DIRECTORY . '/resources/marker.png'));
        }


        $f = imagegif($new, $out);
        imagedestroy($old);
        imagedestroy($new);

        return $f;
    }
}
