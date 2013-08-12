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
class Translit
{
    /**
     * @var array
     */
    protected static $russian = array(
        ' ',
        'Я',
        'Я',
        'я',
        'ые',
        'Ё',
        'ё',
        'Ё',
        'Ж',
        'ж',
        'Ж',
        'З',
        'з',
        'Ч',
        'ч',
        'Ch',
        'Ш',
        'ш',
        'Ш',
        'Э',
        'э',
        'Э',
        'Ю',
        'ю',
        'Ю',
        'Я',
        'я',
        'Я',
        'А',
        'а',
        'Б',
        'б',
        'В',
        'в',
        'Г',
        'г',
        'Д',
        'д',
        'Е',
        'е',
        'И',
        'и',
        'Й',
        'й',
        'К',
        'к',
        'Л',
        'л',
        'М',
        'м',
        'Н',
        'н',
        'О',
        'о',
        'П',
        'п',
        'Р',
        'р',
        'С',
        'с',
        'Т',
        'т',
        'У',
        'у',
        'Ф',
        'ф',
        'Х',
        'х',
        'Щ',
        'щ',
        'ъ',
        'ь',
        'Ы',
        'ы',
        'Ц',
        'ц',
    );

    /**
     * @var array
     */
    protected static $translit = array(
        '_',
        'YA',
        'Ya',
        'ya',
        'yee',
        'YO',
        'yo',
        'Yo',
        'ZH',
        'zh',
        'Zh',
        'Z',
        'z',
        'CH',
        'ch',
        'Ch',
        'SH',
        'sh',
        'Sh',
        'YE',
        'ye',
        'Ye',
        'YU',
        'yu',
        'Yu',
        'JA',
        'ja',
        'Ja',
        'A',
        'a',
        'B',
        'b',
        'V',
        'v',
        'G',
        'g',
        'D',
        'd',
        'E',
        'e',
        'I',
        'i',
        'J',
        'j',
        'K',
        'k',
        'L',
        'l',
        'M',
        'm',
        'N',
        'n',
        'O',
        'o',
        'P',
        'p',
        'R',
        'r',
        'S',
        's',
        'T',
        't',
        'U',
        'u',
        'F',
        'f',
        'H',
        'h',
        'W',
        'w',
        'x',
        'q',
        'Y',
        'y',
        'C',
        'c'
    );


    /**
     * Транслит с латиницы на русский
     *
     * @param string $t
     *
     * @return string
     */
    public static function trans($t)
    {
        return str_replace(self::$translit, self::$russian, $t);
    }


    /**
     * Транслит с русского на латиницу
     *
     * @param string $t
     *
     * @return string
     */
    public static function retrans($t)
    {
        return str_replace(self::$russian, self::$translit, $t);
    }
}
