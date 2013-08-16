<?php
/**
 * This file contains the implementation for PRIV frame
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
 * @copyright 2006 Alexander Merz
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version   CVS: $Id: PRIV.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for PRIV frame in a tag. (Private)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_PRIV extends MP3_IDv2_Frame
{

    /**
     * the owner identifier
     * @var string owner identifier
     */
    private $_owner = "\0";

    /**
     * the private data
     * @var string
     */
    private $_private = "";

    /**
     * Sets the owner identifier.
     *
     * @param string $oid the owner identifer
     *
     * @return void
     * @access public
     */
    public function setOwnerId($oid)
    {
        $this->_changed = true;
        if ("\0" == substr($oid, -1)) {
            $oid = substr($oid, 0, -1);
        }
        $this->_owner = $oid;
    }

    /**
     * Returns the owner identifier
     *
     * @param bool $b if true, null is appended
     *
     * @return string the owner identifier
     * @access public
     */
    public function getOwnerId($b = false)
    {
        $ret = $this->_owner;
        if ($b) {
            if ("\0" != substr($ret, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Sets the private data
     *
     * @param string $pd private data
     *
     * @return void
     * @access public
     */
    public function setPrivateData($pd)
    {
        $this->_changed = true;
        $this->_private = $pd;
    }

    /**
     * Returns the private data
     *
     * @return string private data
     * @access public
     */
    public function getPrivateData()
    {
        return $this->_private;
    }

    /**
     * Creates the content of the frame (encoding+language+tou)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return $this->getOwnerId().
               $this->getPrivateData();
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

        $p = strpos($content, "\0");

        $this->setOwnerId(substr($content, 0, $p));
        $this->setPrivateData(substr($content, $p+1));
    }

    /**
     * Sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "PRIV";
        $this->_purpose = "Private";
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     * @access public
     */
    public function toString()
    {
        return $this->getID()." (".$this->getPurpose().") ".$this->getOwnerId();
    }
}
?>