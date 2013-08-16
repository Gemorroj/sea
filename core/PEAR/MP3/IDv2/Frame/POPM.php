<?php
/**
 * This file contains the implementation for POPM frame
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
 * @version   CVS: $Id: POPM.php 248530 2007-12-18 21:36:37Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for POPM frame in a tag.
 * (Popularimeter)
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
class MP3_IDv2_Frame_POPM extends MP3_IDv2_Frame
{

    /**
     * the rating
     * @var int
     */
    public $_rating = 0;

    /**
     * email address
     * @var string
     */
    private $_email = "\0";

    /**
     * the counter
     * @var int
     */
    private $_counter = "";

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
     * Sets the rating.
     * Must between 0 (worst) and 255 (best).
     * 
     * @param int $c rating value
     * 
     * @return void
     * @access public
     */
    public function setRating($c) 
    {
        $this->_changed = true;
        if ($c < 0) {
            $c = 0;
        } else if ($c > 255) {
            $c = 255;
        }
        $this->_rating = $c;
    }

    /**
     * Returns the rating.
     * 
     * @return int the rating value
     * @access public
     */
    public function getRating() 
    {
        return $this->_rating;
    }

    /**
     * Sets the email address.
     * 
     * @param string $email the email adsress
     * 
     * @return void
     * @access public
     */
    public function setEmail($email) 
    {
        $this->_changed = true;
        if ("\0" == substr($email, -1)) {
            $email = substr($email, 0, -1);
        }
        $this->_email = $email;
    }

    /**
     * Returns the email address.
     *
     * @param bool $nul if true appends a null
     * 
     * @return string the email address
     * @access public
     */
    public function getEmail($nul = false) 
    {
        $ret = $this->_email;
        if ($nul) {
            if ("\0" != substr($ret, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Creates the content of the frame (encoding+language+tou)
     * 
     * @return string the frame content
     * @access public
     */
    public function createContent() 
    {
        return $this->getEmail(true).
                pack("C", $this->getRating()).
                pack("N", $this->getCounter());
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
        
        $c = explode("\0", $content);
        
        $this->setEmail($c[0]);
        $this->setRating(substr($c[1], 0, 1));
        
        if (5 <= strlen($c[1])) {
            $this->setCounter(substr($c[1], 1));
        }
    }

    /**
     * Sets the id and the purpose of the frame
     * 
     * @return void
     * @access public
     */
    function __construct() 
    {
        $this->_id      = "POPM";
        $this->_purpose = "Popularimeter";
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
                $this->getEmail()." ".$this->getRating()." ".
                $this->getCounter();
    }

}
?>