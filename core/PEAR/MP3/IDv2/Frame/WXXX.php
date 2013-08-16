<?php
/**
 * This file contains the implementation for the WXXX frame
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
 * @version   CVS: $Id: WXXX.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame/CommonLink.php';

/**
 * Data stucture for WXXX frame in a tag
 * (User-defined URL)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_WXXX extends MP3_IDv2_Frame_CommonLink
{

    /**
     * the encoding of the frame data
     * @var string the encoding identifier
     */
    private $_encoding="\n";

    /**
     * The description of the frame
     * @var string
     */
     private $_description = "";

    /**
     * The url of the frame
     * @var string
     */
    private $_url = "";

    /**
     * Sets the id and purpose of the frame only
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->setId("WXXX");
        $this->setPurpose("User-defined URL");
    }

    /**
     * Returns the frame description.
     *
     * @param bool $nul if true appends a null
     *
     * @return string the frame description
     * @access public
     */
    public function getDescription($nul = false)
    {
        $ret = $this->_description;
        if ($nul) {
            if ("\0" != substr($nul, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Returns the frame value.
     *
     * @return string the frame value
     * @access public
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Sets the Url for the frame
     *
     * @param string $url the url
     *
     * @return void
     * @access public
     */
    public function setUrl($url)
    {
        $this->_changed = true;
        $this->_url     = $url;
    }

    /**
     * Sets the description for the URL
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

        $c = unpack('C1enc', $content[0]);

        $this->setEncoding($c['enc']);

        $t = explode("\0", substr($content, 1));

        $this->setUrl($t[0]);

        if (isset($t[1])) {
            $this->setDescription($t[1]);
        }
    }

    /**
     * Sets the encoding for the text for the frame.
     *
     * @param string $enc the byte containing the encoding identifier
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
     * Returns the encoding identifier for the text in the frame.
     *
     * @return string the identifier
     * @access public
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Creates the content of the frame
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return pack("c", $this->getEncoding()).
                            $this->getDescription(true).
                            $this->getUrl();
    }
}
?>