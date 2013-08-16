<?php
/**
 * This file contains the implementation for the TXXX frame
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
 * @version   CVS: $Id: TXXX.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame/CommonText.php';

/**
 * Data stucture for TXXX frame in a tag
 * (User-defined text frame)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_TXXX extends MP3_IDv2_Frame_CommonText
{

    /**
     * the description of the frame
     * @var string
     */
    private $_descr="";

    /**
     * the content of the frame
     * @var string
     */
    private $_value="";

    /**
     * Sets the id and purpose of the frame only
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->setId("TXXX");
        $this->setPurpose("User-defined Text");
    }

    /**
     * Returns the frame description.
     *
     * @param bool $b if true adds a null
     *
     * @return string the frame description
     * @access public
     */
    function getDescription($b = false)
    {
        $ret = $this->_descr;
        if ($b) {
            if ("\0" != substr($ret, -1)) {
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
    function getValue()
    {
        return $this->_value;
    }

    /**
     * Sets the frame description
     *
     * @param string $descr the frame description
     *
     * @return void
     * @access public
     */
    function setDescription($descr)
    {
        $this->_changed = true;
        if ("\0" == substr($descr, -1)) {
            $descr = substr($descr, 0, -1);
        }
        $this->_descr = $descr;
    }

    /**
     * Sets the frame value
     *
     * @param string $value the frame value
     *
     * @return void
     * @access public
     */
    function setValue($value)
    {
        $this->changed = true;
        $this->_value  = $value;
    }

    /**
     * Creates the whole content (descr+value).
     *
     * @return string
     * @access public
     */
    function createContent()
    {
        return $this->getDescription(true).
                $this->getValue();
    }

    /**
     * Sets the unproccess content of the frame (excluding header!)
     *
     * @param string $content the raw frame data without the header
     *
     * @return void
     * @access public
     */
    function setRawContent($content)
    {
        $this->_changed = true;
        $this->_content = $content;

        $t = explode("\0", $content);

        $this->setDescription($t[0]);

        if (isset($t[1])) {
            $this->setValue($t[1]);
        }
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     * @access public
     */
    function toString()
    {
        return $this->getID()." (".$this->getPurpose().") ".
                $this->getDescription()." ".
                $this->getValue();
    }
}
?>