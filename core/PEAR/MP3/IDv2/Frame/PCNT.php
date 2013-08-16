<?php
/**
 * This file contains the implementation for PCNT frame
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
 * @version   CVS: $Id: PCNT.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for PCNT frame in a tag.
 * (Play counter)
 *
 * The implementation is an optimistic one,
 * it expects, that the counter doesn't become
 * larger then 32 bit.
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_PCNT extends MP3_IDv2_Frame
{

    /**
     * the counter
     * @var int
     */
    private $_counter = 0;

    /**
     * Sets the counter.
     *
     * @param int $c counter value
     *
     * @return void
     * @access public
     */
    public function setCounter($c)
    {
        $this->_changed = true;
        $this->_counter = $c;
    }

    /**
     * Returns the counter.
     *
     * @return int the counter value
     * @access public
     */
    public function getCounter()
    {
        return $this->_counter;
    }


    /**
     * Creates the content of the frame (encoding+language+tou)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return pack("N", $this->getCounter());
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
        $this->setCounter(substr($content, -4, 4));
    }

    /**
     * Sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "PCNT";
        $this->_purpose = "Play counter";
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
                $this->getCounter();
    }
}
?>