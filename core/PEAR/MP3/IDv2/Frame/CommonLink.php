<?php
/**
 * This file contains the generic implementation for link frames
 *
 * PHP version 5
 *
 * Copyright (C) 2006-2007 Alexander Merz
 *
 * LICENSE This library is free software; you can redistribute it and/or
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
 * @version   CVS: $Id: CommonLink.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame.php';

/**
 * Data stucture for link frames in a tag.
 * These frames starts with a W in the frame identifier
 * except 'wxxx'.
 *
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL License 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
abstract class MP3_IDv2_Frame_CommonLink extends MP3_IDv2_Frame
{

    /**
     * The url stored in the frame
     *
     * @var string $_url
     */
    private $_url = null;

    /**
     * Creates the content of the frame
     *
     * @return string the frame content
     *
     * @access public
     */
    public function createContent()
    {
        return $this->getUrl();
    }

    /**
     * Sets the data of the frame and processes it.
     *
     * @param string $content the unproccess content for the frame
     *
     * @return void
     */
    public function setRawContent($content)
    {
        $this->_changed = true;
        $this->_content = $content;

        $this->setUrl($content);
    }

    /**
     * Sets the URL in the frame.
     *
     * @param string $url the url
     *
     * @return void
     */
    public function setUrl($url)
    {
        $this->_changed = true;
        $this->_url     = $url;
    }

    /**
     * Gets the URL in the frame.
     *
     * @return string the URL
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     */
    public function toString()
    {
        return $this->getID()." (".$this->getPurpose().") ".$this->getUrl();
    }
}
?>