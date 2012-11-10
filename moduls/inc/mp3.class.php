<?php
/*
//Merge two files
$path = 'path.mp3';
$path1 = 'path1.mp3';
$mp3 = new mp3($path);

$newpath = 'path.mp3';
$mp3->striptags();

$mp3_1 = new mp3($path1);
$mp3->mergeBehind($mp3_1);
$mp3->striptags();
$mp3->setIdv3_2('01','Track Title','Artist','Album','Year','Genre','Comments','Composer','OrigArtist',
'Copyright','url','encodedBy');
$mp3->save($newpath);


//Extract 30 seconds starting after 10 seconds.
$path = 'path.mp3';
$mp3 = new mp3($path);
$mp3_1 = $mp3->extract(10,30);
$mp3_1->save('newpath.mp3');

//Extract the exact length of time
$path = 'path.mp3';
$mp3 = new mp3($path);
$mp3->setFileInfoExact();
echo $mp3->time;
//note that this is the exact length!
*/

//
//   taken from: http://www.sourcerally.net/Scripts/20-PHP-MP3-Class
//
class mp3
{
    var $str;
    var $time;
    var $frames;


    function mp3($path = '')
    {
        if ($path != '') {
            $this->str = file_get_contents($path);
        }
    }


    function setStr($str)
    {
        $this->str = $str;
    }


    function getStart()
    {
        $currentStrPos = -1;
        while (true) {
            $currentStrPos = strpos($this->str, chr(255), $currentStrPos + 1);
            if ($currentStrPos === false) {
                return 0;
            }

            $str = substr($this->str, $currentStrPos, 4);
            $strlen = strlen($str);
            $parts = array();
            for ($i = 0; $i < $strlen; ++$i) {
                $parts[] = $this->decbinFill(ord($str[$i]), 8);
            }

            if ($this->doFrameStuff($parts) === false) {
                continue;
            }

            return $currentStrPos;
        }
    }


    function setFileInfoExact()
    {
        $maxStrLen = strlen($this->str);
        $currentStrPos = $this->getStart();

        $framesCount = $time = 0;
        while ($currentStrPos < $maxStrLen) {
            $str = substr($this->str, $currentStrPos, 4);
            $strlen = strlen($str);
            $parts = array();
            for ($i = 0; $i < $strlen; ++$i) {
                $parts[] = $this->decbinFill(ord($str[$i]), 8);
            }

            if ($parts[0] != '11111111') {
                if (($maxStrLen - 128) > $currentStrPos) {
                    return false;
                } else {
                    $this->time = $time;
                    $this->frames = $framesCount;
                    return true;
                }
            }
            $a = $this->doFrameStuff($parts);
            $currentStrPos += $a[0];
            $time += $a[1];
            $framesCount++;
        }
        $this->time = $time;
        $this->frames = $framesCount;
        return true;
    }


    function extract($start, $length)
    {
        $maxStrLen = strlen($this->str);
        $currentStrPos = $this->getStart();
        $framesCount = $time = 0;
        $startCount = $endCount = -1;
        while ($currentStrPos < $maxStrLen) {
            if ($startCount == -1 && $time >= $start) {
                $startCount = $currentStrPos;
            }
            if ($endCount == -1 && $time >= ($start + $length)) {
                $endCount = $currentStrPos - $startCount;
            }
            $doFrame = true;
            $str = substr($this->str, $currentStrPos, 4);
            $strlen = strlen($str);
            $parts = array();
            for ($i = 0; $i < $strlen; ++$i) {
                $parts[] = $this->decbinFill(ord($str[$i]), 8);
            }
            if ($parts[0] != '11111111') {
                if (($maxStrLen - 128) > $currentStrPos) {
                    $doFrame = false;
                } else {
                    $doFrame = false;
                }
            }
            if ($doFrame) {
                $a = $this->doFrameStuff($parts);
                $currentStrPos += $a[0];
                $time += $a[1];
                $framesCount++;
            } else {
                break;
            }
        }

        $mp3 = new mp3();

        if ($endCount == -1) {
            $endCount = $maxStrLen - $startCount;
        }
        if ($startCount != -1 && $endCount != -1) {
            $mp3->setStr(substr($this->str, $startCount, $endCount));
        }
        return $mp3;
    }


    function decbinFill($dec, $length = 0)
    {
        $str = decbin($dec);
        $nulls = $length - strlen($str);
        if ($nulls > 0) {
            for ($i = 0; $i < $nulls; ++$i) {
                $str = '0' . $str;
            }
        }
        return $str;
    }


    function doFrameStuff($parts)
    {
        //Get Audio Version
        $seconds = 0;
        $errors = array();
        switch (substr($parts[1], 3, 2)) {
            case '00':
                $audio = '2.5';
                break;
            case '10':
                $audio = '2';
                break;
            case '11':
                $audio = '1';
                break;
            default:
                return false;
                break;
        }
        //Get Layer
        switch (substr($parts[1], 5, 2)) {
            case '01':
                $layer = '3';
                break;
            case '10':
                $layer = '2';
                break;
            default:
                return false;
                break;
        }
        //Get Bitrate
        $bitFlag = substr($parts[2], 0, 4);
        $bitArray = array(
            '0000' => array(0, 0, 0, 0, 0),
            '0001' => array(32, 32, 32, 32, 8),
            '0010' => array(64, 48, 40, 48, 16),
            '0011' => array(96, 56, 48, 56, 24),
            '0100' => array(128, 64, 56, 64, 32),
            '0101' => array(160, 80, 64, 80, 40),
            '0110' => array(192, 96, 80, 96, 48),
            '0111' => array(224, 112, 96, 112, 56),
            '1000' => array(256, 128, 112, 128, 64),
            '1001' => array(288, 160, 128, 144, 80),
            '1010' => array(320, 192, 160, 160, 96),
            '1011' => array(352, 224, 192, 176, 112),
            '1100' => array(384, 256, 224, 192, 128),
            '1101' => array(416, 320, 256, 224, 144),
            '1110' => array(448, 384, 320, 256, 160),
            '1111' => array(-1, -1, -1, -1, -1)
        );
        $bitPart = $bitArray[$bitFlag];
        $bitArrayNumber = null;
        if ($audio == 1) {
            switch ($layer) {
                case 1:
                    $bitArrayNumber = 0;
                    break;
                case 2:
                    $bitArrayNumber = 1;
                    break;
                case 3:
                    $bitArrayNumber = 2;
                    break;
            }
        } else {
            switch ($layer) {
                case 1:
                    $bitArrayNumber = 3;
                    break;
                case 2:
                    $bitArrayNumber = 4;
                    break;
                case 3:
                    $bitArrayNumber = 4;
                    break;
            }
        }
        $bitRate = $bitPart[$bitArrayNumber];
        if ($bitRate <= 0) {
            return false;
        }
        //Get Frequency
        $frequencies = array(
            '1'   => array('00' => 44100, '01' => 48000, '10' => 32000, '11' => 'reserved'),
            '2'   => array(),
            '2.5' => array()
        );
        $freq = $frequencies[$audio][substr($parts[2], 4, 2)];
        //IsPadded?
        $padding = substr($parts[2], 6, 1);

        //FrameLengthInBytes = 144 * BitRate / SampleRate + Padding
        $frameLength = floor(144 * $bitRate * 1000 / $freq + $padding);

        if (!$frameLength) {
            return false;
        }

        $seconds += $frameLength * 8 / ($bitRate * 1000);
        return array($frameLength, $seconds);
        //Calculate next when next frame starts.
        //Capture next frame.
    }


    function setIdv3_2($track, $title, $artist, $album, $year, $genre, $comments, $composer, $origArtist, $copyright, $url, $encodedBy)
    {
        $urlLength = strlen($url) + 2;
        $copyrightLength = strlen($copyright) + 1;
        $origArtistLength = strlen($origArtist) + 1;
        $composerLength = strlen($composer) + 1;
        $commentsLength = strlen($comments) + 5;
        $titleLength = strlen($title) + 1;
        $artistLength = strlen($artist) + 1;
        $albumLength = strlen($album) + 1;
        $genreLength = strlen($genre) + 1;
        $encodedByLength = strlen($encodedBy) + 1;
        $trackLength = strlen($track) + 1;
        $yearLength = strlen($year) + 1;

        $str  = '';
        $str .= chr(73); //I
        $str .= chr(68); //D
        $str .= chr(51); //3
        $str .= chr(3); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(8); //.
        $str .= chr(53); //5
        $str .= chr(84); //T
        $str .= chr(82); //R
        $str .= chr(67); //C
        $str .= chr(75); //K
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($trackLength); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $track;
        $str .= chr(84); //T
        $str .= chr(69); //E
        $str .= chr(78); //N
        $str .= chr(67); //C
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($encodedByLength); //
        $str .= chr(64); //@
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $encodedBy;
        $str .= chr(87); //W
        $str .= chr(88); //X
        $str .= chr(88); //X
        $str .= chr(88); //X
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($urlLength); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $url;
        $str .= chr(84); //T
        $str .= chr(67); //C
        $str .= chr(79); //O
        $str .= chr(80); //P
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($copyrightLength); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $copyright;
        $str .= chr(84); //T
        $str .= chr(79); //O
        $str .= chr(80); //P
        $str .= chr(69); //E
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($origArtistLength); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $origArtist;
        $str .= chr(84); //T
        $str .= chr(67); //C
        $str .= chr(79); //O
        $str .= chr(77); //M
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($composerLength); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $composer;
        $str .= chr(67); //C
        $str .= chr(79); //O
        $str .= chr(77); //M
        $str .= chr(77); //M
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($commentsLength); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(9); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $comments;
        $str .= chr(84); //T

        $str .= chr(67); //C
        $str .= chr(79); //O
        $str .= chr(78); //N
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($genreLength); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $genre;
        $str .= chr(84); //T
        $str .= chr(89); //Y
        $str .= chr(69); //E
        $str .= chr(82); //R
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($yearLength); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $year;
        $str .= chr(84); //T
        $str .= chr(65); //A
        $str .= chr(76); //L
        $str .= chr(66); //B
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($albumLength); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $album;
        $str .= chr(84); //T
        $str .= chr(80); //P
        $str .= chr(69); //E
        $str .= chr(49); //1
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($artistLength); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $artist;
        $str .= chr(84); //T
        $str .= chr(73); //I
        $str .= chr(84); //T
        $str .= chr(50); //2
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr($titleLength); //.
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= chr(0); //
        $str .= $title;
        $this->str = $str . $this->str;
    }


    function mergeBehind(mp3 $mp3)
    {
        $this->str .= $mp3->str;
    }


    function mergeInfront(mp3 $mp3)
    {
        $this->str = $mp3->str . $this->str;
    }


    function getIdvEnd()
    {
        $str = substr($this->str, (strlen($this->str) - 128));
        if (strtolower(substr($str, 0, 3)) == 'tag') {
            return $str;
        } else {
            return false;
        }
    }


    function striptags()
    {
        //Remove start stuff...
        $s = $start = $this->getStart();
        if ($s === false) {
            return false;
        } else {
            $this->str = substr($this->str, $start);
        }
        //Remove end tag stuff
        $end = $this->getIdvEnd();
        if ($end !== false) {
            $this->str = substr($this->str, 0, (strlen($this->str) - 129));
        }
    }


    function save($path)
    {
        $fp = fopen($path, 'w');
        fwrite($fp, $this->str);
        fclose($fp);
    }


    //join various MP3s
    function multiJoin($newpath, $array)
    {
        foreach ($array as $path) {
            $mp3 = new mp3($path);
            $mp3->striptags();
            $mp3_1 = new mp3($newpath);
            $mp3->mergeBehind($mp3_1);
            $mp3->save($newpath);
        }
    }

}

?>
