<?php
/**
 * This file contains the implementation for USLT frame
 *
 * PHP version 5
 *
 * Copyright (C) 2006-2007 Alexander Merz
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category  File_Formats
 * @package   MP3_IDv2
 * @author    Alexander Merz <alexander.merz@web.de>
 * @copyright 2006-2007 Alexander Merz
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version   CVS: $Id: USLT.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for USLT frame in a tag.
 * (Unsynchronized lyrics/text transcription)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_USLT extends MP3_IDv2_Frame
{

    /**
     * the text encoding
     * @var string text encoding identifier
     */
    private $_encoding = "\0";

    /**
     * language
     * @var string ISO-3 language code
     */
    private $_language = "   ";

    /**
     * description of the frame text
     * @var string
     */
    private $_descr = "\0";

    /**
     * lyrics
     * @var string the lyrics/text
     */
    private $_lyrics = "";

    /**
     * Sets the encoding identifier
     *
     * @param string $enc the encoding identifier
     *
     * @return void
     * @access public
     */
    public function setEncoding($enc)
    {
        $this->_changed  = true;
        $this->_encoding = $enc;
    }

    /**
     * Returns the encoding for the text and description
     *
     * @return string the encoding identifier
     * @access public
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Sets the language of the description a/o lyrics
     *
     * @param string $lang the ISO-3 language code
     *
     * @return void
     * @access public
     */
    public function setLanguage($lang)
    {
        $this->_changed = true;
        if (3 < strlen($lang)) {
            $lang = substr($lang, 0, 2);
        }
        $this->_language = $lang;
    }

    /**
     * Returns the language oft the description/lyrics
     *
     * @return string ISO-3 language code
     * @access public
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Sets the description.
     *
     * @param string $descr the description
     *
     * @return void
     * @access public
     */
    public function setDescription($descr)
    {
        $this->_changed     = true;
        $this->_description = $descr;
    }

    /**
     * Returns the description.
     *
     * @param bool $nul if true, a null is appended
     *
     * @return string the description
     * @access public
     */
    public function getDescription($nul = false)
    {
        $ret = $this->_description;
        if ($nul) {
            if ("\0" == substr($ret, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Sets the lyrics.
     *
     * @param string $lyrics the lyrics/text
     *
     * @return void
     * @access public
     */
    public function setLyrics($lyrics)
    {
        $this->_changed = true;
        $this->_lyrics  = $lyrics;
    }

    /**
     * Returns the lyrics/text.
     *
     * @return string the description
     * @access public
     */
    public function getLyrics()
    {
        return $this->_lyrics;
    }

    /**
     * Creates the content of the frame (encoding+language+description+lyrics)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return pack("c", $this->getEncoding()).
               $this->getLanguage().
               $this->getDescription(true).
               $this->getLyrics();
    }

    /**
     * Sets the data of the frame and processes it.
     *
     * @param string $content the unproccess content for the frame
     *
     * @return void
     * @access public
     */
    public function setRawContent($content)
    {
        $this->_changed = true;
        $this->_content = $content;

        $e = unpack("C1enc", $content[0]);

        $this->setEncoding($e['enc']);
        $this->setLanguage(substr($content, 1, 3));

        $content = substr($content, 3);
        $p       = strpos($content, "\0");

        $this->setDescription(substr($content, 0, $p));
        $this->setLyrics(substr($content, $p+1));
    }

    /**
     * Sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "USLT";
        $this->_purpose = "Unsynchronized lyrics/text transcription";
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     * @access public
     */
    public function toString()
    {
        return $this->getID()." (".$this->getPurpose().") ".
                $this->getLanguage()." ".$this->getDescription(true).
                " ".$this->getLyrics();
    }
}
?>