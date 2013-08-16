<?php
/**
 * This file contains the implementation for UFID frame
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
 * @version   CVS: $Id: UFID.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for UFID frame in a tag.
 * (Unique file identifier)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_UFID extends MP3_IDv2_Frame
{

    /**
     * the owner identifier
     * @var string owner identifier
     */
    private $_owner = "\0";

    /**
     * file identifier
     * @var string file identifier
     */
    private $_fileidentifier = null;

    /**
     * Sets the owner identifier
     *
     * @param string $oid the owner identifier
     *
     * @return void
     * @access public
     */
    public function setOwnerIdentifier($oid)
    {
        $this->_changed = true;
        if ( "\n" == substr($oid, -1)) {
            $oid = substr($oid, 0, -1);
        }
        $this->_owner = $oid;
    }

    /**
     * Returns the text in the frame.
     *
     * @param bool $nul if true then add a null
     *
     * @return string the text
     * @access public
     */
    public function getOwnerIdentifier($nul = false)
    {
        $ret = $this->_owner;
        if ($nul) {
            if ("\0" != substr($ret, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Sets the unique file identifier
     *
     * @param string $fid the unique file identifier
     *
     * @return void
     * @access public
     */
    public function setFileIdentifier($fid)
    {
        $this->_changed = true;
        if (64 < strlen($fid)) {
            $fid = substr($fid, 0, 63);
        }
        $this->_fileidentifier = $fid;
    }

    /**
     * Returns the unique file identifier
     *
     * @return string the identifier
     * @access public
     */
    public function getFileIdentifier()
    {
        return $this->_fileidentifier;
    }

    /**
     * Creates the content of the frame (owner-id + file-id)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return $this->getOwnerIdentifier(true).$this->getFileIdentifier();
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

        $this->setOwnerIdentifier(substr($content, 0, $p));
        $this->setFileIdentifier(substr($content, $p+1));
    }

    /**
     * Sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "UFID";
        $this->_purpose = "Unique file identifier";
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
                $this->getOwnerIdentifier()." ".$this->getFileIdentifier();
    }
}
?>