<?php
/**
 * This file contains the implementation for RBUF frame
 *
 * PHP version
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
 * @version   CVS: $Id: RBUF.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for RBUF frame in a tag.
 * (Recommended buffer size)
 *
 * @category  File_Formats
 * @package   MP3_IDv2
 * @author    Alexander Merz <alexander.merz@web.de>
 * @copyright 2006-2007 The PHP Group
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_RBUF extends MP3_IDv2_Frame
{

    /**
     * the buffer size
     * @var int
     */
    private $buffer = 0;


    /**
     * embedded flag
     * @var bool
     */
    private $_emb = false;

    /**
     * the offset to next flag
     * @var int
     */
    private $_next = 0;

    /**
     * Sets the buffer size.
     *
     * @param int $c buffer size
     *
     * @return void
     * @access public
     */
    public function setBufferSize($c)
    {
        $this->_changed = true;
        $this->_buffer  = $c;
    }

    /**
     * Returns the buffer size.
     *
     * @return int the buffer size
     * @access public
     */
    public function getBufferSize()
    {
        return $this->_buffer;
    }

    /**
     * Sets the embedded flag.
     *
     * @param bool $b embedded flag
     *
     * @return void
     * @access public
     */
    public function useEmbedding($b = true)
    {
        $this->_changed = true;
        $this->_emb     = $b;
    }

    /**
     * Returns the embedded flag
     *
     * @return bool true if flag is set
     * @access public
     */
    public function isEmbedded()
    {
        return $this->_emb;
    }

    /**
     * Sets the offset to the next tag containing this frame
     *
     * @param int $offset the offset
     *
     * @return void
     * @access public
     */
    public function setOffsetToNextTag($offset)
    {
        $this->_changed = true;
        $this->_offset  = $offset;
    }

    /**
     * Returns the offset to the next tag containing this frame
     *
     * @return string the email address
     * @access public
     */
    public function getOffsetToNextTag()
    {
        return $this->_offset;
    }


    /**
     * Creates the content of the frame (encoding+language+tou)
     *
     * @return string the frame content
     * @access public
     * @todo this look like a bug here
     */
    public function createContent()
    {
        $size = str_pad(decbin($this->getBufferSize()), 32, "0", STR_PAD_LEFT);
        $size = substr($size, 3);
        $flag = bindec("00000000");
        if ($this->isEmbedded()) {
            $flag = bindec("00000001");
        }

        // TODO bug?
        return $size.
                pack("C", decbin($flag)).
                pack("N", $this->getOffsetToNextTag());
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

        $s  = substr($content, 0,3);
        $sa = '';

        for ($i=2; $i>=0; $i--) {
            $sa = $sa.decbin($s[$i]);
        }

        $this->setBufferSize(bindec($sa));

        $flag = substr($content, 3, 1);

        if ($flag & bindec("00000001")) {
            $this->setEmbedded();
        }

        $n  = substr($content, 4);
        $na = '';

        for ($i=3; $i>=0; $i--) {
            $na = $na.decbin($n[$i]);
        }
        $this->setOffsetToNextTag(bindec($na));
    }

    /**
     * Sets the id and the purpose of the frame
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->_id      = "RBUF";
        $this->_purpose = "Recommended buffer size";
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
                $this->getBufferSize()." ".$this->getOffsetToNextTag();
    }
}
?>