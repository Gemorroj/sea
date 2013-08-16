<?php
/**
 * This file contains the implementation for USER frame
 *
 * PHP versions 5
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
 * @version   CVS: $Id: USER.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1

 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for USER frame in a tag.
 * (Terms of use)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_USER extends MP3_IDv2_Frame
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
     * terms of use of the frame text
     * @var string
     */
    private $_terms = "\0";

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
     * Sets the terms of use.
     *
     * @param string $tou the terms of use
     *
     * @return void
     * @access public
     */
    public function setTermsOfUse($tou)
    {
        $this->_changed = true;
        $this->_terms   = $tou;
    }

    /**
     * Returns the terms of use.
     *
     * @return string the description
     * @access public
     */
    public function getTermsOfUse()
    {
        return $this->_terms;
    }

    /**
     * Creates the content of the frame (encoding+language+tou)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return pack("c", $this->getEncoding()).
                            $this->getLanguage().
                            $this->getTermsOfUse();
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
        $this->setTermsOfUse(substr($content, 3));
    }

    /**
     * sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "USER";
        $this->_purpose = "Terms of Use";
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
                $this->getLanguage()." ".$this->getTermsOfUse();
    }
}
?>