<?php
/**
 * This file contains the implementation for the Idv2 tag data structure class
 *
 * Copyright (C) 2006 Alexander Merz
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
 * @category   File Formats
 * @package    MP3_IDv2
 * @author     Alexander Merz <alexander.merz@web.de>
 * @copyright  2006 Alexander Merz
 * @license    http://www.gnu.org/licenses/lgpl.html
 * @version    CVS: $Id: Tag.php 256765 2008-04-04 13:20:28Z alexmerz $
 * @link       http://pear.php.net/package/MP3_IDv2
 * @since      File available since Release 0.1
 */
 
/**
 * load the PEAR class for error handlng
 */ 
require_once 'PEAR.php';

/**
 * error number, if a tag couldn't parsed correctly
 */
define('PEAR_MP3_IDV2_NOIDTAG', 310);
/**
 * error message, if a tag couldn't parsed correctly
 */
define('PEAR_MP3_IDV2_NOIDTAG_S', "Not an ID-Tag or corrupted header!");

/**
 * Data stucture for Idv2 tag 
 *
 * This implementation supports currently 2.3 
 *
 * @category   File Formats
 * @package    MP3_IDv2
 * @author     Alexander Merz <alexander.merz@web.de>
 * @copyright  2006 Alexander Merz
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/MP3_IDv2
 * @since      Class available since Release 0.1.0
 */
class MP3_IDv2_Tag {

    /**
     * the size of the whole tag excluding the header
     * @var int
     */
    protected $_size = null;

    /**
     * holds the major version number of the tag
     * @var int
     */
    protected $_maxVersion = 3;

    /**
     * holds the minor version number of the tag
     * @var int
     */
    protected $_minVersion = 0;

    /**
     * holds the flag for unsynchonized tag data
     * (everything excluding the tag header)
     * @var bool
     */
    protected $_unsynchronisation = false;

    /**
     * holds the flag for that the tag has an extended header
     * @var bool
     */
    protected $_extendedHeader = false;

    /**
     * holds the flag for an experimental flag
     * @var bool
     */
    protected $_experimental = false;

    /**
     * holds the CRC size of the tag data
     * @var int
     */
    protected $_extCRC = null;

    /**
     * holds the list of frame of the tag
     *
     * @see MP3_IDv2_Frame
     * @var array a list of MP3_IDv2_Frame objects
     */
    protected $_frames = array();

    /**
     * holds the flag for CRC in the extended header
     * @var bool
     */
    protected $_useCRC = false;

    /**
     * Sets the version of the tag.
     * Actually unused for processing.
     *
     * @param int $max major version of the tag
     * @param int $min minor version
     * @access public
     */
    public function setVersion($max, $min) {

        $this->_minVersion = $min;
        $this->_maxVersion = $max;
    }

    /**
     * Returns the version used for building the tag.
     *
     * @return string the Version as "max.min
     * @access public
     */
    public function getVersion() {
        return $this->_maxVersion.".".$this->_minVersion;
    }

    /**
     * Sets the size of the tag data.
     *
     * @param int $size the tag data size including the extended header
     * @access public
     */
    public function setSize($size) {
        $this->_size = $size;
    }

    /**
     * Returns the size of the tag data.
     *
     * @return int the size of the tag data excluding the header and extended header
     * @access public
     */
    public function getSize() {
        if (null == $this->_size) {
            $size=0;
            foreach ($this->_frames as $frame) {
               $size = $size + $frame->getSize();
            }
            $this->_size = $size;
        }
        return $this->_size;
    }

    /**
     * Enables/disable the unsychonisation of the tag data
     *
     * @param bool true for enabling the unsychronisation, false for disabling
     * @access public
     */
    public function setUnsynchronisation($b) {
        $this->_unsynchronisation = $b;
    }

    /**
     * Returns ift the tag data is unsychonized or should be
     *
     * @return bool true is unsychronisation is activ
     * @access public
     */
    public function isUnsynchronisation() {
        return $this->_unsynchronisation;
    }

    /**
     * Enables/diables the usage of an extended tag header
     *
     * @param bool $b true for enable, false for disable
     * @access public
     */
    public function setExtendedHeader($b) {
        $this->_extendedHeader = $b;
    }

    /**
     * Checks if tag has an extended header or should create one
     *
     * @return bool true if has one/create one
     * @access public
     */
    public function isExtendedHeader() {
        return $this->_extendedHeader;
    }

    /**
     * Marks this tag as experimental
     * (This has no effect on tag handling currently)
     *
     * @param bool $b true to mark, false to demark
     * @access public
     */
    public function setExperimental($b) {
        $this->_experimental = $b;
    }

    /**
     * Checks if the tag is an experimental tag
     *
     * @return bool true if tag is experimental
     * @access public
     */
    public function isExperimental() {
        return $this->_experimental;
    }

    /**
     * Enables or disables the usage of a CRC
     * in the tag
     *
     * @param bool $b false for disable
     * @access public
     */
    public function useCRC($b = true) {
        $this->_useCRC = $b;
    }

    /**
     * Returns the CRC-32 checksum over the tag date
     *
     * @return int the CRC-32 checksum
     * @access public
     */
    public function getCRC() {
           return $this->_extCRC;
    }

    /**
     * Creates the tag header depending on the tag settings
     *
     * @return string the tag header
     * @access public
     */
    public function createHeader() {
        // TODO check for max size!
        $header = '';
        // set ID
        $header = $header."ID3";
        // set Version
        $header = $header.pack("cc", $this->_maxVersion, $this->_minVersion);
        // set flags
        // TODO use numbers
        $f=bindec('00000000');
        if ($this->isUnsynchronisation()) {
            $f = $f | bindec('10000000');
        }
        if ($this->isExtendedHeader()) {
            $f = $f | bindec('01000000');
        }
        if ($this->isExperimental()) {
            $f = $f | bindec('00100000');
        }
        $header = $header.pack("c", $f);

        // set Size
        $size = $this->sync8to7($this->getSize());
        $sb = str_pad(decbin($size), 32, '0', STR_PAD_LEFT);
        $sa = array();
        for ($i=3; $i>=0; $i--) {
            $sa[$i] = bindec(substr($sb, $i*8, 8));
        }
        $header = $header.pack("cccc", $sa[0],$sa[1],$sa[2],$sa[3]);
        if ($this->isExtendedHeader()) {
            $header = $header.$this->createExtendedHeader();
        }
        return $header;
    }

    /**
     * Creates the extended header of the tag depending on the tag settings.
     * This function does not check if the extended header flag is set!
     *
     * @return string the extended header of the tag
     * @access public
     */
    public function createExtendedHeader() {
        $extheader   = '';
        $exthcontent = '';
        // set flags
        $f = bindec('00000000');
        if ($this->_useCRC) {
            $f = $f | bindec('10000000');
            $sb = str_pad(decbin($this->_extCRC), 32, '0', STR_PAD_LEFT);
            $sa = array();
            for ($i=3; $i>=0; $i--) {
               $sa[$i] = bindec(substr($sb, $i*8, 8));
            }
            $exthcontent = $exthcontent.pack("cccc", $sa[0],$sa[1],$sa[2],$sa[3]);
        }
        $flag = pack('cc', $f, 0);
        // set padding - is 0, we set no padding
        $padd = "\0\0\0\0";
        // set size
        $sb = str_pad(decbin(strlen($exthcontent)), 32, '0', STR_PAD_LEFT);
        $sa = array();
        for ($i=3; $i>=0; $i--) {
            $sa[$i] = bindec(substr($sb, $i*8, 8));
        }
        $size = pack("cccc", $sa[0],$sa[1],$sa[2],$sa[3]);
        return $size.$flag.$padd.$exthcontent;
    }


    /**
     * Does the unsynchronisation for the tag size in the header
     *
     * @param int the size of the tag data+extended header
     * @return int the unsych size
     * @access private
     */
    public function sync8to7($s) {
        $sb = str_pad(decbin($s), 32, '0', STR_PAD_LEFT);
        $sa = array();
        for ($i=3; $i>=0; $i--){
            $sa[$i] = str_pad(substr($sb, $i*8+4-$i, 7), 8, '0', STR_PAD_LEFT);

        }
        $s = $sa[0].$sa[1].$sa[2].$sa[3];

        return bindec($s);
    }

    /**
     * Adds a frame to the tag
     *
     * @param object MP3_IDv2_Frame the frame to add
     * @access public
     */
    public function addFrame($frame) {
        $this->_frames[] = $frame;
    }

    /**
     * Creates the whole tag depending on the tag settings.
     * This includes also the creation of the frames. So you
     * really get the whole tag.
     *
     * @return string the tag
     */
    public function createTag() {
        $content = '';
        foreach ($this->_frames as $frame) {
            $content = $content.$frame->createFrame();
        }
        if ($this->_useCRC) {
            $this->_extCRC = crc32($content);
            $this->_extendedHeader=true;
        }
        $ff = chr(255);
        $nn = chr(0);
        if (strpos($content, $ff )) {
            // TODO some problems to figure out, need more testing
           /*
            $content = str_replace($ff.$nn, $ff, $content);
            $this->setUnsynchronisation(true);
            */
        }
        strlen($content);
        $this->setSize(strlen($content));
        $header = $this->createHeader();
        return $header.$content;
    }


    /**
     * Returns the frames of the tag.
     *
     * @return array the list of MP3_IDv2_Frame objects
     * @access public
     */
    public function getFrames() {
        return $this->_frames;
    }

    /**
     * Returns all frames identified by their identifiers of the tag.
     *
     * @param string $id the identifer of the frames to catch
     * @return array the list of founded frames as MP3_IDv2_Frame objects
     * @see MP3_IDv2_Tag::getFrameById()
     * @access public
     */
    public function getFramesById($id) {
        $ret = array();
        foreach ($this->_frames as $frame) {
            if ($id == $frame->getId()) {
                $ret[]=$frame;
            }
        }
        return $ret;
    }

    /**
     * Returns only the first frame with the given id in the tag
     *
     * @param string $id the identifer of the frame to catch
     * @return object MP3_IDv2_Frame the founded frame or null if not found
     * @see MP3_IDv2_Tag::getFramesById();
     * @access public
     */
    public function getFrameById($id) {
        foreach ($this->_frames as $frame) {
            if ($id == $frame->getId()) {
                return $frame;
            }
        }
        return null;
    }
}
    


?>