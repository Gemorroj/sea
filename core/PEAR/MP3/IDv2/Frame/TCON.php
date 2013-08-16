<?php
/**
 * This file contains the implementation for the TCON frame
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
 * @version   CVS: $Id: TCON.php 248624 2007-12-20 19:07:33Z alexmerz $
 * @link      http://pear.php.net/package/MP3_IDv2
 * @since     File available since Release 0.1
 */

/**
 * load parent class
 */
require_once 'MP3/IDv2/Frame/CommonText.php';

/**
 * Data stucture for TCON frames in a tag
 * (Content type)
 *
 * @category File_Formats
 * @package  MP3_IDv2
 * @author   Alexander Merz <alexander.merz@web.de>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL 2.1
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/MP3_IDv2
 * @since    Class available since Release 0.1.0
 */
class MP3_IDv2_Frame_TCON extends MP3_IDv2_Frame_CommonText
{

    /**
     * the list of genres found in the text
     * @var array list of genres in the frame
     */
    private $_genrelist = array();

    /**
     * list of genres defintions given by the text
     * @var array list of custom genre definitions
     */
    private $_customgenres = array();

    /**
     * list of genre definitions given by IDv1
     * @var array list of predefined genres
     */
    private $_genresV1 = array(
             0   => 'Blues',
             1   => 'Classic Rock',
             2   => 'Country',
             3   => 'Dance',
             4   => 'Disco',
             5   => 'Funk',
             6   => 'Grunge',
             7   => 'Hip-Hop',
             8   => 'Jazz',
             9   => 'Metal',
             10  => 'New Age',
             11  => 'Oldies',
             12  => 'Other',
             13  => 'Pop',
             14  => 'R&B',
             15  => 'Rap',
             16  => 'Reggae',
             17  => 'Rock',
             18  => 'Techno',
             19  => 'Industrial',
             20  => 'Alternative',
             21  => 'Ska',
             22  => 'Death Metal',
             23  => 'Pranks',
             24  => 'Soundtrack',
             25  => 'Euro-Techno',
             26  => 'Ambient',
             27  => 'Trip-Hop',
             28  => 'Vocal',
             29  => 'Jazz+Funk',
             30  => 'Fusion',
             31  => 'Trance',
             32  => 'Classical',
             33  => 'Instrumental',
             34  => 'Acid',
             35  => 'House',
             36  => 'Game',
             37  => 'Sound Clip',
             38  => 'Gospel',
             39  => 'Noise',
             40  => 'Alternative Rock',
             41  => 'Bass',
             42  => 'Soul',
             43  => 'Punk',
             44  => 'Space',
             45  => 'Meditative',
             46  => 'Instrumental Pop',
             47  => 'Instrumental Rock',
             48  => 'Ethnic',
             49  => 'Gothic',
             50  => 'Darkwave',
             51  => 'Techno-Industrial',
             52  => 'Electronic',
             53  => 'Pop-Folk',
             54  => 'Eurodance',
             55  => 'Dream',
             56  => 'Southern Rock',
             57  => 'Comedy',
             58  => 'Cult',
             59  => 'Gangsta',
             60  => 'Top 40',
             61  => 'Christian Rap',
             62  => 'Pop/Funk',
             63  => 'Jungle',
             64  => 'Native US',
             65  => 'Cabaret',
             66  => 'New Wave',
             67  => 'Psychadelic',
             68  => 'Rave',
             69  => 'Showtunes',
             70  => 'Trailer',
             71  => 'Lo-Fi',
             72  => 'Tribal',
             73  => 'Acid Punk',
             74  => 'Acid Jazz',
             75  => 'Polka',
             76  => 'Retro',
             77  => 'Musical',
             78  => 'Rock & Roll',
             79  => 'Hard Rock',
             80  => 'Folk',
             81  => 'Folk-Rock',
             82  => 'National Folk',
             83  => 'Swing',
             84  => 'Fast Fusion',
             85  => 'Bebob',
             86  => 'Latin',
             87  => 'Revival',
             88  => 'Celtic',
             89  => 'Bluegrass',
             90  => 'Avantgarde',
             91  => 'Gothic Rock',
             92  => 'Progressive Rock',
             93  => 'Psychedelic Rock',
             94  => 'Symphonic Rock',
             95  => 'Slow Rock',
             96  => 'Big Band',
             97  => 'Chorus',
             98  => 'Easy Listening',
             99  => 'Acoustic',
             100 => 'Humour',
             101 => 'Speech',
             102 => 'Chanson',
             103 => 'Opera',
             104 => 'Chamber Music',
             105 => 'Sonata',
             106 => 'Symphony',
             107 => 'Booty Bass',
             108 => 'Primus',
             109 => 'Porn Groove',
             110 => 'Satire',
             111 => 'Slow Jam',
             112 => 'Club',
             113 => 'Tango',
             114 => 'Samba',
             115 => 'Folklore',
             116 => 'Ballad',
             117 => 'Power Ballad',
             118 => 'Rhytmic Soul',
             119 => 'Freestyle',
             120 => 'Duet',
             121 => 'Punk Rock',
             122 => 'Drum Solo',
             123 => 'Acapella',
             124 => 'Euro-House',
             125 => 'Dance Hall',
             126 => 'Goa',
             127 => 'Drum & Bass',
             128 => 'Club-House',
             129 => 'Hardcore',
             130 => 'Terror',
             131 => 'Indie',
             132 => 'BritPop',
             133 => 'Negerpunk',
             134 => 'Polsk Punk',
             135 => 'Beat',
             136 => 'Christian Gangsta Rap',
             137 => 'Heavy Metal',
             138 => 'Black Metal',
             139 => 'Crossover',
             140 => 'Contemporary Christian',
             141 => 'Christian Rock',
             142 => 'Merengue',
             143 => 'Salsa',
             144 => 'Trash Metal',
             145 => 'Anime',
             146 => 'Jpop',
             147 => 'Synthpop',
             'RX' => 'Remix',
             'CR' => 'Cover',
        );

    /**
     * Sets the id and purpose of the frame only
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        $this->setId("TCON");
        $this->setPurpose("Content type");
    }

    /**
     * Returns the used numbers/identifers.
     *
     * @return array list of genres found in the frame
     * @access public
     */
    public function getGenres()
    {
        return $this->_genrelist;
    }

    /**
     * Returns the Genre descriptions index by numbers/identifers.
     *
     * @return array list of founded genres and there description
     * @access public
     */
    public function getGenresDescr()
    {
        $ret = array();
        foreach ($this->_genrelist as $g) {
            // first look if there is custom descr
            if (!empty($this->_customgenres[$g])) {
                $ret[$g] = $this->_customgenres[$g];
            } else if (!empty($this->_genresV1[$g])) { // the offical list
                $ret[$g] = $this->_genresV1[$g];
            } else {
                $ret[$g] = '';
            }
        }
        return $ret;
    }

    /**
     * Returns a genre description for a given number
     *
     * @param int $number the number of the genre to look up
     *
     * @return string the genre description or null if not found
     * @access public
     */
    public function getGenreDescr($number)
    {
        if (isset($this->_customgenres[$number])) {
                return $this->_customgenres[$number];
        } else if (isset($this->_genresV1[$number])) {

                return $this->_genresV1[$number];
        }
        return null;
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

        $data = substr($content, 1);

        while (0!=strlen($data)) {
            if ('(' != $data[0]) {              // check if the first is a bracket
                $this->_genrelist[] = $data;    // no number given, store the data directly as is

                $data = '';

            } else {
                $a  = 1;                        // there is a number or alpha ->read until closing bracket
                $no = '';
                while (')'!=$data[$a]) {
                    $no = $no.$data[$a];
                    $a++;
                }
                $this->_genrelist[] = $no;

                $data = substr($data, $a);      // remove the readen stuff from the string

                if (0!=strlen($data) && '('!=$data[0]) {  // now check if there is an explanation (
                    $a    = 1;                            // @TODO: handle the '((' stuff
                    $desc = '';
                    while (!empty($data[$a]) && '('!=$data[$a] && 0!=strlen($data)) {
                        $desc = $desc.$data[$a];
                        $a++;
                    }
                    $data = substr($data, $a);
                    $desc = trim($desc);                  // the standard isn't really clear about spaces or not, ie: (13)_(65)
                                                          // so first make sure, that there is really text, else ignore it
                    if (0!=strlen($desc)) {
                        $this->_customgenres[$no] = $desc;
                    }
                }
            }
        }
    }

    /**
     * Returns the text
     *
     * @return string the text
     * @access public
     */
    public function getText()
    {
        if ($this->_changed == true) {
            $text = '';
            foreach ($this->_genrelist as $k => $v) {
                $text = $text."(".$k.")".trim($v);
            }
            $this->_text = $text;
        }
        return $this->_text;
    }

    /**
     * Add a new genre.
     *
     * @param int    $genreno    number of the genre to add
     * @param string $genredescr description of the genre to add
     *
     * @return void
     * @access public
     */
    public function addGenre($genreno, $genredescr = '')
    {
        $this->_changed             = true;
        $this->_genrelist[$genreno] = $genredescr;

        if ('' != $genredescr) {
            $this->_customgenres[$genreno] = $genredescr;
        }
    }

    /**
     * Returns the frame content as something printable
     *
     * @return string the frame content
     * @access public
     */
    public function toString()
    {
        $ret = $this->getID()." (".$this->getPurpose().") ";
        $ge  = $this->getGenres();

        foreach ($ge as $g => $k) {
            $ret = $ret.$g."->".$this->getGenreDescr($g)." ";
        }
        return $ret;
    }

}
?>