<?php
/**
 * This file contains the implementation for the Idv2-Tag reader class
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
 * @version    CVS: $Id: Reader.php 245655 2007-11-05 22:01:27Z alexmerz $
 * @link       http://pear.php.net/package/MP3_IDv2
 * @since      File available since Release 0.1
 */

/**
 * load the PEAR class for error handlng
 */ 
require_once 'PEAR.php';

/**
 * load the tag data structure
 */
require_once 'MP3/IDv2/Tag.php';

/**
 * load the generic frame data strucure
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * error number, if a file couldn't be found
 */
define('PEAR_MP3_IDV2_FILENOTFOUND', 110);

/**
 * error message, if a file couldn't be found
 */
define('PEAR_MP3_IDV2_FILENOTFOUND_S', 'File %s not found.');

/**
 * error number, if a file couldn't be open for reading
 */
define('PEAR_MP3_IDV2_FILENOTOPEN', 120);

/**
 * error message, if a file couldn't be open for reading
 */
define('PEAR_MP3_IDV2_FILENOTOPEN_S', 'Could not open file %s.');

/**
 * error number, if a file couldn'b read correctly
 * This error indicates a problem, but this could be
 * also a bug in the Reader!
 */
define('PEAR_MP3_IDV2_FILECORRUPTED', 130);

/**
 * error message, if a file couldn'b read correctly
 */
define('PEAR_MP3_IDV2_FILECORRUPTED_S', 'File %s looks corrupted.');

/**
 * Reads Idv2 tags from a file.
 *
 * This class reads Idv2 tags from a file.
 * Actually this class reads only the
 * first founded tag in a file
 * This will become more flexible in future
 * releases.
 *
 * @category   File Formats
 * @package    MP3_IDv2
 * @author     Alexander Merz <alexander.merz@web.de>
 * @copyright  2006 Alexander Merz
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/MP3_IDv2
 * @since      Class available since Release 0.1.0
 */ 
class MP3_IDv2_Reader {

    /**
     * Stores the readed tags
     * @var array $_tags list of tags
     * @access private 
     */
    private $_tags=array();
    
    /**
     * Reads the first tag found in a tag.
     * 
     * The method reads a tag from the file
     * and parses it into a Tag datastructure.
     * 
     * @param string $filename name of the file to read from
     * @return bool true, if reading was ok
     * @throws PEAR_MP3_IDV2_FILENOTFOUND, PEAR_MP3_IDV2_FILENOTOPEN,PEAR_MP3_IDV2_FILECORRUPTED
     * @access public  
     * @see getTag()
     */
    public function read($filename) {
        if (!file_exists($filename)) {
            return PEAR::raiseError(
                sprintf(PEAR_MP3_IDV2_FILENOTFOUND_S, $filename),
                PEAR_MP3_IDV2_FILENOTFOUND);
        }
        $fh = fopen($filename, "rb");
        if (!$fh) {
            return PEAR::raiseError(
                sprintf(PEAR_MP3_IDV2_FILENOTOPEN_S, $filename),
                PEAR_MP3_IDV2_FILENOTOPEN);

        }
        if (!$this->findHeader($fh)) {
            fclose($fh);
            return false;
        }
        
        $tag = new MP3_IDv2_Tag();
        
        $version = unpack('c1max/c1min', fread($fh, 2));
        $tag->setVersion($version['max'], $version['min']);
        
        $flags = unpack('c1flag',fread($fh, 1));
        $flags = bindec(str_pad(decbin($flags['flag']), 8, '0'));
        if ($flags && bindec('10000000')) { // Unsynchronize required
            $tag->setUnsynchronisation(true);
        }
        if ($flags && bindec('01000000')) {
            $tag->setExtendedHeader(true);
        }
        if ($flags && bindec('00100000')) {
            $tag->setExperimental(true);
        }
        
        $sa=unpack('C4size', fread($fh, 4));                
        $sb = substr(str_pad(decbin($sa['size1']), 8, '0', STR_PAD_LEFT), 1, 7) .
                substr(str_pad(decbin($sa['size2']), 8, '0', STR_PAD_LEFT), 1, 7) .
                substr(str_pad(decbin($sa['size3']), 8, '0', STR_PAD_LEFT), 1, 7) .
                substr(str_pad(decbin($sa['size4']), 8, '0', STR_PAD_LEFT), 1, 7);                
        $size = bindec($sb); 
        $data = fread($fh,$size);        
        fclose($fh);
        
        if (strlen($data) != $size) {
            return PEAR::raiseError(
                sprintf(PEAR_MP3_IDV2_FILECORRUPTED_S, $filename),
                PEAR_MP3_IDV2_FILECORRUPTED);            
        }
        if ($tag->isUnsynchronisation()) {
           
            $ff = chr(255);
            $nn = "\0";
            $data = str_replace($ff.$nn, $ff, $data);
        }
        
        while (strlen($data) > 0) {

            // there could be a padding space at the end of the
            // tag, so make sure that we not create empty frames from it
            $id=substr($data, 0, 4);
            $offset=0;
            if (''!=trim($id)) {
                $frame = MP3_IDv2_Frame::getInstance($id);
                
                // get the frame size
                $s=unpack('C4size',substr($data, 4, 4));
                
                $sb = str_pad(decbin($s['size1']),8,'0', STR_PAD_LEFT).
                        str_pad(decbin($s['size2']),8,'0', STR_PAD_LEFT).
                        str_pad(decbin($s['size3']),8,'0', STR_PAD_LEFT).
                        str_pad(decbin($s['size4']),8,'0', STR_PAD_LEFT);                      
                $size = bindec($sb); 
                                                
                // get the frame flags                                
                $flags = unpack('c1flaga/c1flagb',substr($data, 8, 2));
                $flaga = bindec(str_pad(decbin($flags['flaga']), 8, '0'));
                $flagb = bindec(str_pad(decbin($flags['flagb']), 8, '0'));
                if ($flaga && bindec('10000000')) {
                    $frame->setFlagTagAlterPres(true);
                }
                if ($flaga && bindec('01000000')) {
                    $frame->setFlagFileAlterPres(true);
                }
                if ($flaga && bindec('00100000')) {
                    $frame->setFlagReadOnly(true);
                }
                if ($flagb && bindec('10000000')) {
                    $frame->useCompression(true);                    
                }
                if ($flagb && bindec('01000000')) {
                    $frame->useEncryption();
                }                 
                if ($flagb && bindec('00100000')) {
                    $frame->useGroupingIdentifier(true);                   
                }
                // depending on the flags there could
                // be additional header data
                
                if ($frame->isCompressed()) {
                    // TODO we do not store the compressed size,
                    // but should do this in future for error detection                    
                    $offset=$offset+4;
                }
                if ($frame->isEncrypted()) {
                    $frame->setEncryptionMethod(substr($data, 10+$offset, 1));
                    $offset = $offset+1;
                }
                if ($frame->hasGroupingIdentifier()) {                    
                    // maybe wrong decoding
                    $t = unpack('c1gid',substr($data, 10+$offset, 1));
                    $frame->setGroupIdentifier($t['gid']);
                    $offset = $offset+1;
                }

                $framedata = substr($data, 10+$offset, $size-$offset);

                $frame->setRawContent($framedata);
                $tag->addFrame($frame);
                $data = substr($data, $size+10);
                
            } else {
                $data = '';
            }
        }
        
        $this->_tags[] = $tag;
        return true;
    }    
    
    /**
     * Find the beginning of a tag in a file.
     * 
     * @param int $fh the file stream to read
     * @return bool true, if a tag was found, false if not
     * @access private
     * @todo Make it faster, use a larger read buffer 
     */
    public function findHeader($fh) {
        $i=0;
        $c='';
        while (!feof($fh)) {
            $c=$c.fread($fh, 1);
            if (2==$i && 'ID3'==$c) {
                return true;
            } else if (2==$i) {
                $i=-1;
                $c='';
            }
            $i++;

        }
        rewind($fh);
        return false;
    }

    /**
     * Returns the tag found in the file
     *
     * @return object MP3_IDv2_Tag the tag data structure
     * @access public
     */
    public function getTag() {
        return $this->_tags[0];
    }
}

?>
