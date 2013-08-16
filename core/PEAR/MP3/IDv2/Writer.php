<?php
/**
 * This file contains the implementation for the Idv2-Tag writer class
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
 * @version    CVS: $Id: Writer.php 244455 2007-10-19 09:54:32Z alexmerz $
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
 * load the generic frame structure
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * load the reader class
 */ 
require_once 'MP3/IDv2/Reader.php';

/**
 * error number, if a file couldn't be open for writing
 */
define('PEAR_MP3_IDV2_FILENOWRITE', 210);

/**
 * error message, if a file couldn't be open for writing
 */
define('PEAR_MP3_IDV2_FILENOWRITE_S', 'Could not open file %s for writing!');

/**
 * error number, if the temporary file couldn't renamed to the given one
 */
define('PEAR_MP3_IDV2_FILENOCOPY', 220);

/**
 * error message, if the temporary file couldn't renamed to the given one
 */
define('PEAR_MP3_IDV2_FILENOCOPY_S', 'Could not rename temporary file to %s!');


/**
 * Writes Idv2 tags to a file
 *
 * This class writes the given Idv2 tags to a file.
 * Currently the first provided tag is written
 * at the top of the file, removing an existing one.
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
class MP3_IDv2_Writer {

    /**
     * Stores the tags to write.
     * @var array $_tags list of tags
     * @access private
     */
    protected $_tags = array();

    /**
     * Add a IDv2 tag tag to write to a file.
     *
     * @param object MP3_IDv2_Tag $tag tag to add
     * @access public
     */
    public function addTag($tag) {
        $this->tags[] = $tag;
    }

    /**
     * Writes the provided tags to a file.
     *
     * The tags are written to a temporary file
     * first and then renamed to the given
     * filename.
     *
     * @param string $filename
     * @return bool true if writing was successful
     * @throws PEAR_MP3_IDV2_FILENOCOPY, PEAR_MP3_IDV2_FILENOWRITE
     * @access public
     */
    public function write($filename) {
        $content = '';
        if (file_exists($filename)) {
            $fr = fopen($filename, "rb");
            // do not write old tags into file
            $this->jumpOverTag($fr);

            while (!feof($fr)) {
                // MP3 files are normally big
                $content = $content.fread($fr, 256000);
            }
            fclose($fr);
        }
-       $fh = fopen($filename."_", "wb");
        if (!$fh) {
            return PEAR::raiseError(
                sprintf(PEAR_MP3_IDV2_FILENOWRITE_S,$filename),
                PEAR_MP3_IDV2_FILENOWRITE);
        }
        foreach($this->tags as $tag) {
            // TODO check for return values!
            fwrite($fh, $tag->createTag());
            fwrite($fh, $content);
        }
        fclose($fh);
        if (!copy($filename."_", $filename)) {
            return PEAR::raiseError(
                sprintf(PEAR_MP3_IDV2_FILENOCOPY_S,$filename),
                PEAR_MP3_IDV2_FILENOCOPY);
        } else {
            unlink($filename."_");          
        }
        return true;
    }

    /**
     * Looks for the IDv2-tag in a stream and moves the filepointer
     * after the tag, if a tag could be found.
     *
     * @param int $fh the file handle
     * @return bool true if tag was found, false if no tag was found
     * @access private
     */
    public function jumpOverTag($fh) {
        if (MP3_IDv2_Reader::findHeader($fh)) {
            fseek($fh,3, SEEK_CUR);
            $sa=unpack('C4size', fread($fh, 4));
            $sb = substr(str_pad(decbin($sa['size1']),8, '0',STR_PAD_LEFT),1,7) .
                    substr(str_pad(decbin($sa['size2']),8, '0',STR_PAD_LEFT),1,7) .
                    substr(str_pad(decbin($sa['size3']),8, '0',STR_PAD_LEFT),1,7) .
                    substr(str_pad(decbin($sa['size4']),8, '0',STR_PAD_LEFT),1,7);                                    
            $size = bindec($sb);
            fseek($fh, $size);            
            return true;
        } else {
            return false;
        }
    }
}

?>