<?php
// убираем папку с загрузками
$screen = strstr($v['path'], '/');
$prev_pic = str_replace('/', '--', mb_substr($screen, 1));

// Скриншот
if (file_exists($setup['spath'] . $screen . '.gif')) {
    $v['screen'] = DIRECTORY . $setup['spath'] . $screen . '.gif';
} else if (file_exists($setup['spath'] . $screen . '.jpg')) {
    $v['screen'] = DIRECTORY . $setup['spath'] . $screen . '.jpg';
} else {
    $v['screen'] = '';
}

// Описание
if (file_exists($setup['opath'] . '/' . $screen . '.txt')) {
    $v['description'] = trim(file_get_contents($setup['opath'] . '/' . $screen . '.txt'));
} else if ($setup['lib_desc'] && $ext == 'txt') {
    $fp = fopen($v['path'], 'r');
    $v['description'] = trim(fgets($fp, 1024));
    fclose($fp);
} else {
    $v['description'] = '';
}

// Вложения
$v['attachments'] = '';
if ($v['attach']) {
    $attach = unserialize($v['attach']);
    if ($attach) {
        foreach ($attach as $k => $val) {
            $v['attachments'][DIRECTORY . $setup['apath'] . dirname($screen) . '/' . $id . '_' . $k . '_' . $val] = $val;
        }
    }
}



if ($ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'jpe' || $ext == 'png' || $ext == 'bmp') {
    if ($setup['screen_file_change']) {
        if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
            $v['screen_file'] = DIRECTORY . $setup['picpath'] . '/' . $prev_pic . '.gif';
        } else {
            $v['screen_file'] = DIRECTORY . 'im/' . $id;
        }
    }

    $size = getimagesize($v['path']);
    $v['imagesize'] = array('w' => $size[0], 'h' => $size[1]);
    $v['imagelink'] = array();

    foreach (explode(',', $setup['view_size']) as $val) {
        $wh = explode('*', $val);
        if (file_exists($setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif')) {
            $v['imagelink'][$val] = DIRECTORY . $setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif';
        } else {
            $v['imagelink'][$val] = DIRECTORY . 'im/' . $id . '?w=' . $wh[0] . '&h=' . $wh[1];
        }
    }
} else if ($ext == 'mp3' || $ext == 'wav' || $ext == 'ogg') {
    $tmpa = getMusicInfo($id, $v['path']);

    $out .= '<hr class="hr"/><strong>' . $language['info'] . ':</strong><br/>' . $language['channels'] . ': '
        . $tmpa['channels'] . '<br/>' . $language['framerate'] . ': ' . $tmpa['sampleRate'] . ' Hz<br/>'
        . $language['byterate'] . ': ' . round($tmpa['avgBitrate'] / 1024) . ' Kbps<br/>' . $language['length'] . ': '
        . date('H:i:s', mktime(0, 0, $tmpa['streamLength'])) . '<br/>';

    if ($tmpa['comments']['TITLE']) {
        $out .= $language['name'] . ': ' . htmlspecialchars($tmpa['comments']['TITLE'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['ARTIST']) {
        $out .= $language['artist'] . ': ' . htmlspecialchars($tmpa['comments']['ARTIST'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['ALBUM']) {
        $out .= $language['album'] . ': ' . htmlspecialchars($tmpa['comments']['ALBUM'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['DATE']) {
        $out .= $language['year'] . ': ' . htmlspecialchars($tmpa['comments']['DATE'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['GENRE']) {
        $out .= $language['genre'] . ': ' . htmlspecialchars($tmpa['comments']['GENRE'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['COMMENT']) {
        $out .= $language['comments'] . ': ' . htmlspecialchars($tmpa['comments']['COMMENT'], ENT_NOQUOTES) . '<br/>';
    }
    if ($tmpa['comments']['APIC']) {
        $out .= '<img src="' . DIRECTORY . 'apic/' . $id . '" alt=""/>';
    }
} else if (($ext == '3gp' || $ext == 'avi' || $ext == 'mp4' || $ext == 'flv') && extension_loaded('ffmpeg')) {
    $tmpa = getVideoInfo($id, $v['path']);

    if ($setup['screen_file_change']) {
        $frame = isset($_GET['frame']) ? abs($_GET['frame']) : $setup['ffmpeg_frame'];
        if (file_exists($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif')) {
            $out .= '<br/><img src="' . DIRECTORY . $setup['ffmpegpath'] . '/' . htmlspecialchars($prev_pic) . '_frame_'
                . $frame . '.gif" alt=""/><br/>';
        } else {
            $out .= '<br/><img src="' . DIRECTORY . 'ffmpeg/' . $id . '/' . $frame . '" alt=""/><br/>';
        }
        $i = 0;
        foreach (explode(',', $setup['ffmpeg_frames']) as $fr) {
            $out .= '<a href="' . DIRECTORY . 'view/' . $id . '/frame' . $fr . '">[' . (++$i) . ']</a>, ';
        }
        $out = rtrim($out, ', ') . '<hr class="hr"/>';
    }

    $out .= $language['codec'] . ': ' . htmlspecialchars($tmpa['getVideoCodec'], ENT_NOQUOTES) . '<br/>'
        . $language['screen resolution'] . ': ' . intval($tmpa['GetFrameWidth']) . ' x ' . intval(
        $tmpa['GetFrameHeight']
    ) . '<br/>' . $language['time'] . ': ' . date('H:i:s', mktime(0, 0, round($tmpa['getDuration']))) . '<br/>';


    if ($tmpa['getBitRate']) {
        $out .= $language['bitrate'] . ': ' . ceil($tmpa['getBitRate'] / 1024) . ' Kbps<br/>';
    }
} else if ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs' || $ext == 'apk') {
    if ($setup['screen_file_change']) {
        if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif')) {
            $out
                .=
                '<br/><img src="' . DIRECTORY . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif" alt=""/>';
        } else if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
            $out .= '<br/><object style="width:128px; height:128px;"><param name="movie" value="' . DIRECTORY
                . $setup['tpath'] . '/' . htmlspecialchars($prev_pic) . '.gif.swf"><embed src="' . DIRECTORY
                . $setup['tpath'] . '/' . htmlspecialchars($prev_pic)
                . '.gif.swf" style="width:128px; height:128px;"></embed></param></object>';
        } else {
            $out .= '<br/><img src="' . DIRECTORY . 'theme/' . $id . '" alt=""/>';
        }
    }

    if ($ext == 'thm') {
        $thm = getThmInfo($id, $v['path']);
        $str = '';
        if ($thm['author']) {
            $str .= $language['author'] . ': ' . htmlspecialchars($thm['author'], ENT_NOQUOTES) . '<br/>';
        }
        if ($thm['version']) {
            $str .= $language['version'] . ': ' . htmlspecialchars($thm['version'], ENT_NOQUOTES) . '<br/>';
        }
        if ($thm['models']) {
            $str .= $language['models'] . ': ' . htmlspecialchars($thm['models'], ENT_NOQUOTES) . '<br/>';
        }
        if ($str) {
            $out .= '<br/>' . $str;
        }
    }
} else if ($setup['swf_file_change'] && $ext == 'swf') {
    $out
        .= '<br/><object style="width:128px; height:128px;"><param name="movie" value="' . DIRECTORY . htmlspecialchars(
        $v['path']
    ) . '"><embed src="' . DIRECTORY . htmlspecialchars($v['path'])
        . '" style="width:128px; height:128px;"></embed></param></object>';
} else if ($setup['jar_file_change'] && $ext == 'jar') {
    if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
        $out .= '<br/><img style="margin: 1px;" src="' . DIRECTORY . $setup['ipath'] . '/' . htmlspecialchars($prev_pic)
            . '.png" alt=""/>';
    } else if (jar_ico($v['path'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
        $out .= '<br/><img style="margin: 1px;" src="' . DIRECTORY . $setup['ipath'] . '/' . htmlspecialchars($prev_pic)
            . '.png" alt=""/>';
    }
}
