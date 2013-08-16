<?php
/**
 * This file contains the implementation for the Idv2 frame data structure class
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
 * @version   CVS: $Id: Frame.php 289925 2009-10-26 02:47:16Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */


/**
 * Load PEAR for Error handling
 */ 
require_once 'PEAR.php';

/**
 * Data stucture for Idv2 frames in a tag 
 *
 * This implementation supports currently 2.3 
 *
 * @category  File_Formats
 * @package   MP3_IDv2
 * @author    Alexander Merz <alexander.merz@web.de>
 * @copyright 2006-2007 Alexander Merz
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     Class available since Release 0.1.0
 */ 
class MP3_IDv2_Frame
{
    
    /**
     * The identifier of the frame (name of the frame)
     * 
     * @var string
     */
    protected $_id = null;

    /**
     * Indicates that the content of the frame was changed
     *
     * @var bool true if content was changed and must created again
     */
    protected $_changed = false;

    /**
     * The purpose of this frame
     * 
     * @var string
     */
    protected $_purpose = null;

    /**
     * The raw content of the tag
     * 
     * @var string
     */
    protected $_content = null;

    /**
     * Flag to indicate to drop this frame or not while changing the tag if unknown
     *
     * @var bool
     */
    protected $_flagTagAlterPres = false;

    /**
     * Flag to indicate to drop this frame or not while changing the file if unknown
     *
     * @var bool
     */
    protected $_flagFileAlterPres = false;

    /**
     * Flag to indicate to that tis frame should nor be changed
     *
     * @var bool
     */
    protected $_flagReadOnly = false;

    /**
     * Flag to indicate that the frame content is compressed
     *
     * @var bool
     */
    protected $_flagCompression = false;

    /**
     * Flag to indicate that the frame has a group identifier
     *
     * @var bool
     */
    protected $_flagGroup = false;

    /**
     * Flag to indicate that the frame encrypted
     *
     * @var bool
     */
    protected $_flagEncryption = false;

    /**
     * Size of the frame data after uncompression
     *
     * @var int
     */
    protected $_decompressedSize = null;

    /**
     * Identifier of the method used for encryption
     *
     * @var string
     */
    protected $_encryptionMethod = null;

    /**
     * The identifier for the group
     *
     * @var string
     */
    protected $_groupid = null;

    /**
     * Singleton method the create a specific frame object.
     * If a specific frame class does not exists, a Frame
     * object is returned.
     *
     * @param String $frameId the frame type
     * 
     * @return Object the specific frame object            
     */
    public static function getInstance($frameId = '') 
    {
        $frameId = trim($frameId);
        if ($frameId != '') {
            if(!class_exists('MP3_IDv2_Frame_'.$frameId)) {
                @include_once 'MP3/IDv2/Frame/'.$frameId.'.php';
            }
            $class = 'MP3_IDv2_Frame_'.$frameId;
            if (class_exists($class)) {
                $frame = new $class();
            } else {
                $frame = new MP3_IDv2_Frame();
            }
        } else {
            $frame = new MP3_IDv2_Frame();
        }
        return $frame;
    }

    /**
     * Constructor
     * 
     * An instance of the frame class should be never created
     * directly. Use getInstance instead, to receive 
     * a frame type specific instance. 
     * 
     * A generic frame object may be used for placeholder
     * purposes only. Never apply a generic frame to a file,
     * the result is declared as unpredictable and a subject to
     * change. 
     *
     * @param String $id the frame identifier
     * @param unknown_type $purpose the purpose of the frame
     * 
     * @return MP3_IDv2_Frame
     * @see MP3_IDv2_Frame::getInstance()
     */    
    function __construct($id = null, $purpose = '')
    {
        $this->_id      = $id;
        $this->_purpose = $purpose;
    }
    
    /**
     * Returns the purpose of the frame
     *
     * @return string the frame purpose
     */
    public function getPurpose() 
    {
        return $this->_purpose;
    }

    /**
     * Sets the purpose text of the frame
     *
     * @param string $purpose the frame purpose
     * 
     * @return void
     */
    protected function setPurpose($purpose) 
    {
        $this->_purpose = $purpose;
    }


    /**
     * Enables/disables the use of encryption for the frame data
     *
     * @param bool $b true for enabling, false to disable
     * 
     * @return void
     */
    public function useEncryption($b = true) 
    {
        $this->_flagEncryption = $b;
    }
    
    /**
     * Checks if the frame data is encrypted or should be
     * 
     * @return bool true if encryption is/should be used
     */
    public function isEncrypted()
    {
        return $this->_flagEncryption;
    }
    
    
    /**
     * Enables/disables the use of a group identifier
     *
     * @param bool $b true to use a group identifier
     * 
     * @return void
     */
    public function useGroupingIdentifier($b = true) 
    {
        $this->_flagGroup = $b;
    }

    /**
     * Checks if the frame belongs to a group or should be
     * 
     * @return bool true if should belongs to a group
     */    
    public function hasGroupingIdentifier() 
    {
        return $this->_flagGroup;
    }

    /**
     * Enables/disables the compression of the frame data
     *
     * @param bool $b true if compression should be used
     * 
     * @return void
     */
    public function useCompression($b = true) 
    {
        $this->_flagCompression = $b;
    }


    /**
     * Checks if the frame data is compressed or should be
     * 
     * @return bool true if compression is/should be used
     */    
    public function isCompressed() 
    {
        return $this->_flagCompression;
    }

    /**
     * Sets the grouping identifier for the frame.
     * 
     * @param string $gid one character as group identifier
     *
     * @return void 
     * @see MP3_IDv2_Frame::useGroupingIdentifier()
     */
    public function setGroupIdentifier($gid) 
    {
        $this->_groupid = $gid;
        $this->useGroupingIdentifier();
    }

    /**
    * Get the group identifier of the frame
    *
    * @return bool the group identifier or null  
    * @see MP3_IDv2_Frame::useGroupingIdentifier() 
    */
    public function getGroupIdentifier() 
    {
        return $this->_groupid;
    }

    /**
    * Sets the encryption method
    *
    * @param string $m one character for the method identifier
    * 
    * @return void
    * @see MP3_IDv2_Frame::useEncryption()  
    */
    public function setEncryptionMethod($m) 
    {
        $this->_encryptionMethod = $m;
    }

    /**
    * Get the encryption method for the frame data.
    *
    * @return string the method identifier or null
    * @see MP3_IDv2_Frame::useGroupingIdentifier()
    */
    public function getEncryptionMethod() 
    {
        return $this->_encryptionMethod;
    }


    /**
    * Sets the identifier of the frame.
    *
    * @param string $id a four byte string for the name of the frame
    *
    * @return void
    */
    protected function setId($id) 
    {
        $this->_id = $id;
    }

    /**
    * Returns the identifier of the frame.
    *
    * @return string the frame identifier, a four byte text
    */
    public function getId() 
    {
        return $this->_id;
    }

    /**
    * Sets the unproccess content of the frame (excluding header!)
    *
    * @param string $content the raw frame data without the header
    * 
    * @return void
    */
    public function setRawContent($content) 
    {
        $this->_changed = true;
        $this->_content = $content;
    }

    /**
    * Sets the header flag "Tag alter preservation"
    *
    * @param bool $f true to set the tag   
    * 
    * @return void
    */
    public function setFlagTagAlterPres($f = true) 
    {
        $this->flagTagAlterPres = $f;
    }

    /**
    * Sets the header flag "File alter preservation"
    *
    * @param bool $f true to set the tag
    * 
    * @return void
    */
    public function setFlagFileAlterPres($f = true) 
    {
        $this->flagFileAlterPres = $f;
    }

    /**
    * Sets the header flag "Read only".
    *
    * @param bool $f true to set the tag
    * 
    * @return void
    */
    public function setFlagReadOnly($f) 
    {
        $this->_flagReadOnly = $f;
    }


    /**
    * Checks if the header flag "Tag alter preservation" is set.
    *
    * @return bool true if flag is set
    */
    public function isFlagTagAlterPres() 
    {
        return $this->_flagTagAlterPres;
    }

    /**
     * Checks if the header flag "File alter preservation" is set.
     *
     * @return bool true if flag is set
     */
    public function isFlagFileAlterPres() 
    {
        return $this->_flagFileAlterPres;
    }

    /**
     * Checks if the frame is marked as read only
     *
     * @return bool true if flag is set
     */
    public function isFlagReadOnly() 
    {
        return $this->_flagReadOnly;
    }

    /**
     * Creates the header of the frame including the header extension
     * according to the frame settings.
     *
     * @return string the created header
     */
    public function createHeader() 
    {
        $header    = '';
        $extheader = '';
        
        $header = $header.$this->getId();

        // TODO use numbers!
        $flag1 = bindec('00000000');
        $flag2 = bindec('00000000');        
        if ($this->isFlagTagAlterPres()) {
            $flag1 = $flag1 | bindec('10000000');
        }
        if ($this->isFlagFileAlterPres()) {
            $flag1 = $flag1 | bindec('01000000');
        }
        if ($this->isFlagReadOnly()) {
            $flag1 = $flag1 | bindec('00100000');
        }                
        if ($this->isCompressed()) {
            $flag2 = $flag2 | bindec('10000000');
            // TODO this is nonsense
            //$sb = str_pad(decbin($this->decompressedSize), 32, '0', STR_PAD_LEFT);
            $sb = '0';
            $sa = array();
            for ($i=3; $i>=0; $i--) {
                $sa[$i] = str_pad(substr($sb, $i*8, 8), 8, '0', STR_PAD_LEFT);
            }
            $extheader = $extheader.pack("cccc", $sb[0], $sb[1], $sb[2], $sb[3]);
        }
        if ($this->isEncrypted()) {
            $flag2     = $flag2 | bindec('01000000');
            $extheader = $extheader.pack('c', $this->getEncryptionMethod);
        }
        if ($this->hasGroupingIdentifier()) {            
            $flag2     = $flag2 | bindec('00100000');
            $extheader = $extheader.$this->getGroupIdentifier();
        }                
        $size = strlen($this->getRawContent());
        $size = $size+strlen($extheader);

        $sb = str_pad(decbin($size), 32, '0', STR_PAD_LEFT);
        $sa = array();
        for ($i=3; $i>=0;$i--) {
            $sa[$i] = bindec(str_pad(substr($sb, $i*8, 8), 8, '0', STR_PAD_LEFT));
        }        
        
        $header = $header.pack("cccc", $sa[0], $sa[1], $sa[2], $sa[3]);
        $header = $header.pack('cc', $flag1, $flag2);
        $header = $header.$extheader;
                
        return $header;
    }

    /**
     * Returns the unprocessed content of the frame (excluding header)
     * 
     * @return string the content 'as is'description
     */
    public function getRawContent() 
    {
        if ($this->_changed) {
            $this->_content = $this->createContent();
            $this->changed  = false;            
        }
        return $this->_content;
    }

    /**
     * Creates the content from the frame data parts.
     * 
     * This method must be overwritten by the FrameID-specific implementations.
     * 
     * @return string the created content 
     */
    public function createContent() 
    {
        return $this->_content;
    }
    
    /**
     * Creates the whole frame (header + headerdata + content).
     * 
     * @return string
     */
    public function createFrame() 
    {
        $header = $this->createHeader();
        // TODO check for compression and encryption
        return $header.$this->getRawContent();
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     */
    public function toString() 
    {
        return $this->getID();
    }
}
?>
