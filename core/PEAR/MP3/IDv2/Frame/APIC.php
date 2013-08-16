<?php
/**
 * This file contains the generic implementation for APIC frames
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
 * @version   CVS: $Id: APIC.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/*
 * Load PEAR for error handling
 */
if (!class_exists('PEAR_Exception')) {
    include_once 'PEAR/Exception.php';
}

/*
 * load parent class
 */
if (!class_exists('MP3_IDv2_Frame')) {
    include_once 'MP3/IDv2/Frame.php';
}

/**
 * error number, if the image file couldn't be found
 */
define('PEAR_MP3_IDV2_IMAGENOTFOUND', 1010);

/**
 * error message, if a file couldn't be found
 */
define('PEAR_MP3_IDV2_IMAGENOTFOUND_S', 'Image %s not found.');

/**
 * error number, if the image file couldn't be wrote
 */
define('PEAR_MP3_IDV2_IMAGENOTWROTE', 1020);

/**
 * error message, if a file couldn't be wrote
 */
define('PEAR_MP3_IDV2_IMAGENOTWROTE_S', 'Could not wrote image to %s.');


/**
 * Data stucture for APIC frames in a tag.
 * (Attached Picture)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL License 2.1
 * @version  Release: @package_ version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_APIC extends MP3_IDv2_Frame
{

    /**
     * the encoding of text.
     * @var string one byte representing the encoding
     */
    private $_encoding = "\0";

    /**
     * the MIME type of the image
     * @var string
     */
    private $_mime = "\n";

    /**
     * the type of the image
     */
    private $_pictype = 0;

    /**
     * description of the image
     * @var string
     */
    private $_descr = "\n";

    /**
     * picture data
     * @var string
     */
    private $_picdata = null;

    /**
     * the list of picture types and there description
     */
    private $_ptlist = array(
     "Other",
     "PNG-File icon",
     "Other file icon",
     "Cover (front)",
     "Cover (back)",
     "Leaflet page",
     "Media",
     "Lead artist/lead performer/soloist",
     "Artist/performer",
     "Conductor",
     "Band/Orchestra",
     "Composer",
     "Lyricist/text writer",
     "Recording Location",
     "During recording",
     "During performance",
     "Movie/video screen capture",
     "A bright coloured fish",
     "Illustration",
     "Band/artist logotype",
     "Publisher/Studio logotype"
    );


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
     * Sets the mime type of the picture
     *
     * @param string $mime the mime type
     *
     * @return void
     * @access public
     */
    public function setMimeType($mime)
    {
        $this->_changed = true;
        if (substr($mime, -1) == "\n") {
            $mime = substr($mime, 0, -1);
        }
        $this->_mime = $mime;
    }

    /**
     * Returns the mime type of the picture
     *
     * @param bool $nul if true, deliever with ending null
     *
     * @return string the mime type
     * @access public
     */
    public function getMimeType($nul=false)
    {
        $ret = $this->_mime;
        if ($nul) {
            if ("\0" != substr($ret, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Sets the picture type.
     *
     * @param string $type the picture type
     *
     * @return void
     * @access public
     */
    public function setType($type)
    {
        $this->_changed = true;
        $this->_pictype = $type;
    }

    /**
     * Returns the picture type.
     *
     * @return string the picture type
     * @access public
     */
    public function getType()
    {
        return $this->_pictype;
    }

    /**
     * Returns the description of the picture type.
     *
     * @return string the picture type
     * @access public
     */
    public function getTypeDescr()
    {
        if ($this->_pictype > 0 && $this->_pictype < 22) {
            return $this->_ptlist[$this->_pictype];
        } else {
            return null;
        }
    }

    /**
     * Sets the description of the picture
     *
     * @param string $descr the description
     *
     * @return void
     * @access public
     */
    public function setDescription($descr)
    {
        $this->_changed = true;
        if (substr($descr, -1) == "\n") {
            $descr = substr($descr, 0, -1);
        }
        $this->_descr = $descr;
    }

    /**
     * Returns the description of the picture
     *
     * @param bool $nul if true, deliever with ending null
     *
     * @return string the description
     * @access public
     */
    public function getDescription($nul = false)
    {
        $ret = $this->_descr;
        if ($nul) {
            if ("\0" != substr($ret, -1)) {
                $ret = $ret."\0";
            }
        }
        return $ret;
    }

    /**
     * Sets the picture binary data
     *
     * @param string $data the binary picture data
     *
     * @return void
     * @access public
     */
    public function setPicture($data)
    {
        $this->_changed = true;
        if (substr($data, -1) != "\n") {
            $data = $data."\n";
        }
        $this->_picdata = $data;
    }

    /**
     * Returns the mime type of the picture
     *
     * @return string the mime type
     * @access public
     */
    public function getPicture()
    {
        return $this->_picdata;
    }


    /**
     * Creates the content of the frame (encoding+text)
     *
     * @return string the frame content
     * @access public
     */
    public function createContent()
    {
        return pack("c", $this->getEncoding()).
                    $this->getMimeType(true).
                    pack("c", $this->getType()).
                    $this->getDescription(true).
                    $this->getPicture();
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

        $i    = 1;
        $mime = '';

        while ($content[$i]!="\0") {
            $mime = $mime.$content[$i];
            $i++;
        }
        $this->setMimeType($mime);

        $i++;

        $t = unpack("c1pt", $content[$i]);

        $this->setType($t['pt']);

        $i++;

        $desc = '';
        while ($content[$i]!="\0") {
            $desc = $desc.$content[$i];
            $i++;
        }

        $this->setDescription($desc);
        $this->setPicture(substr($content, $i+1));
    }

    /**
     * Writes the picture data to a file
     *
     * @param string $file the file name
     *
     * @return bool true if file could written
     * @access public
     */
    public function writeToFile($file)
    {
        $h = fopen($file, "wb");
        if (is_resource($h)) {
            fwrite($h, $this->getPicture());
            fclose($h);
            return true;
        } else {
            $message = sprintf(PEAR_MP3_IDV2_IMAGENOTWROTE_S, $file);
            throw new PEAR_Exception($message, PEAR_MP3_IDV2_FILENOTFOUND);
        }
    }

    /**
     * Reads the picture data from a file
     *
     * @param string $file the file name
     *
     * @return void
     * @access public
     */
    public function readFromFile($file)
    {
        if (file_exists($file)) {
            $this->_picdata = file_get_contents($file);
        } else {
            $message = sprintf(PEAR_MP3_IDV2_IMAGENOTFOUND_S, $file);
            throw new PEAR_Exception($message, PEAR_MP3_IDV2_FILENOTFOUND);
        }
        return true;
    }

    /**
     * Sets the id and purpose of the frame only
     *
     * @access public
     */
    public function __construct()
    {
        $this->setId("APIC");
        $this->setPurpose("Attached Picture");
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
                $this->getMimeType()." ".$this->getTypeDescr()." ".
                $this->getDescription();
    }
}
?>