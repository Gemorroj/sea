<?php
/**
 * This file contains the generic implementation for text frames
 *
 * PHP version 5
 * 
 * Copyright (C) 2006-2007 Alexander Merz
 *
 * LICENSE: This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
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
 * @license   LGPL http://www.gnu.org/licenses/lgpl.html
 * @version   CVS: $Id: CommonText.php 247974 2007-12-11 07:19:09Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 * 
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for text frames in a tag.
 * These frames starts with a T in the frame identifier
 * except 'txxx'
 * 
 * A text frame consists of a starting single byte for the encoding
 * of the text and is followed by a string of arbitrary length. A string
 * may separated into multiple parts using a null value.  
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @version  Release: @package_version@  
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
abstract class MP3_IDv2_Frame_CommonText extends MP3_IDv2_Frame
{
    
    /**
     * Contains the encoding of the text.
     * 
     * @var string one byte representing the encoding
     */
    private $_encoding = "\0";

    /**
     * Contains the text in the frame
     * 
     * @var string a string of an arbitrary length
     */
    private $_text = null;

    /** 
     * Sets the text for the frame.
     * 
     * @param string $text the text to set
     * 
     * @return void
     * @access public
     */
    public function setText($text) 
    {
        $this->_changed = true;
        $this->_text    = $text;
    }

    /**
     * Returns the text in the frame.
     * 
     * @return string the text
     * 
     * @access public
     */
    public function getText() 
    {
        return $this->_text;
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
     * 
     * @access public
     */
    public function getEncoding() 
    {
        return $this->_encoding;
    }

    /**
     * Creates the content of the frame (encoding+text)
     * 
     * @return string the frame content
     * 
     * @access public
     */
    public function createContent() 
    {
        return pack("c", $this->getEncoding()).$this->getText();
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
        $this->setText(substr($content, 1));
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     * 
     * @access public
     */
    public function toString() 
    {
        return $this->getID()." (".$this->getPurpose().") ".$this->getText();
    }
}
?>