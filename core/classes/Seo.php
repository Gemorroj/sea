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
class Seo
{
    protected static $_title;
    protected static $_keywords;
    protected static $_description;

    /**
     * @param string $description
     */
    public static function setDescription($description)
    {
        self::$_description = $description;
    }

    /**
     * @return string
     */
    public static function getDescription()
    {
        return self::$_description;
    }

    /**
     * @param string $keywords
     */
    public static function setKeywords($keywords)
    {
        self::$_keywords = $keywords;
    }

    /**
     * @return string
     */
    public static function getKeywords()
    {
        return self::$_keywords;
    }

    /**
     * @param string $title
     */
    public static function setTitle($title)
    {
        self::$_title = $title;
    }

    /**
     * @param string $title
     * @param string $prefix
     */
    public static function addTitle($title, $prefix = ' - ')
    {
        self::$_title .= (self::$_title != '' ? $prefix : '') . $title;
    }

    /**
     * @return string
     */
    public static function getTitle()
    {
        return self::$_title;
    }

    /**
     * @return string
     */
    public static function serialize()
    {
        return serialize(array(
            'title' => self::$_title,
            'description' => self::$_description,
            'keywords' => self::$_keywords
        ));
    }

    /**
     * @param string
     */
    public static function unserialize($str)
    {
        $seo = unserialize($str);

        self::$_title = $seo['title'];
        self::$_description = $seo['description'];
        self::$_keywords = $seo['keywords'];
    }
}
