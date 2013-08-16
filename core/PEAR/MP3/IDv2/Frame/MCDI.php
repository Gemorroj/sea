<?php
/**
 * This file contains the generic implementation for MCDI frames
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
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL License 2.1
 * @version   CVS: $Id: MCDI.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for MCDI frames in a tag.
 * (Music CD identifier)
 *
 * @category  File_Formats
 * @package   MP3_IDv2
 * @author    Alexander Merz <alexander.merz@web.de>
 * @copyright 2006-2007 Alexander Merz
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL License 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_MCDI extends MP3_IDv2_Frame
{

    /**
     * Sets the identifier data for the frame.
     *
     * @param string $data the data to set
     *
     * @return void
     * @access public
     */
    public function setMCDI($data)
    {
        $this->_changed = true;
        $this->_content = $data;
    }

    /**
     * Returns the identifier in the frame.
     *
     * @return string the identifier
     * @access public
     */
    public function getMCDI()
    {
        return $this->_content;
    }

    /**
     * Creates the content of the frame (encoding+text)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return $this->getMCDI();
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
        $this->setMCDI($content, 1);
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     * @access public
     */
    public function toString()
    {
        return $this->getID()." (".$this->getPurpose().") ".$this->getMCDI();
    }

    /**
     * Sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "MCDI";
        $this->_purpose = "Music CD Identifier";
    }

}
?>