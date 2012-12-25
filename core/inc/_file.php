<?php
// убираем папку с загрузками
$screen = strstr($v['path'], '/');
$prev_pic = str_replace('/', '--', mb_substr($screen, 1));


$v['attachments'] = '';
$v['description'] = '';
$v['screen'] = '';
$v['screen_file'] = '';
$v['imagesize'] = array('w' => '', 'h' => '');
$v['imagelink'] = array();
$v['info'] = array();
$v['flash_file'] = '';


// Скриншот
if (file_exists($setup['spath'] . $screen . '.gif')) {
    $v['screen'] = DIRECTORY . $setup['spath'] . $screen . '.gif';
} elseif (file_exists($setup['spath'] . $screen . '.jpg')) {
    $v['screen'] = DIRECTORY . $setup['spath'] . $screen . '.jpg';
}

// Описание
if (file_exists($setup['opath'] . '/' . $screen . '.txt')) {
    $v['description'] = trim(file_get_contents($setup['opath'] . '/' . $screen . '.txt'));
} elseif ($setup['lib_desc'] && $ext == 'txt') {
    $fp = fopen($v['path'], 'r');
    $v['description'] = trim(fgets($fp, 1024));
    fclose($fp);
}

// Вложения
if ($v['attach']) {
    $attach = unserialize($v['attach']);
    if ($attach) {
        foreach ($attach as $k => $val) {
            $v['attachments'][$k] = array('name' => $val, 'link' => DIRECTORY . $setup['apath'] . dirname($screen) . '/' . $id . '_' . $k . '_' . $val);
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

    foreach (explode(',', $setup['view_size']) as $val) {
        $wh = explode('*', $val);
        if (file_exists($setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif')) {
            $v['imagelink'][$val] = DIRECTORY . $setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif';
        } else {
            $v['imagelink'][$val] = DIRECTORY . 'im/' . $id . '?w=' . $wh[0] . '&h=' . $wh[1];
        }
    }
}

if ($ext == 'mp3' || $ext == 'wav' || $ext == 'ogg') {
    $v['info'] = getMusicInfo($id, $v['path']);
}

if (($ext == '3gp' || $ext == 'avi' || $ext == 'mp4' || $ext == 'flv') && extension_loaded('ffmpeg')) {
    $v['info'] = getVideoInfo($id, $v['path']);

    if ($setup['screen_file_change']) {
        $frame = isset($_GET['frame']) ? abs($_GET['frame']) : $setup['ffmpeg_frame'];
        if (file_exists($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif')) {
            $v['screen_file'] = DIRECTORY . $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif';
        } else {
            $v['screen_file'] = DIRECTORY . 'ffmpeg/' . $id . '?frame=' . $frame;
        }
    }
}

if ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs' || $ext == 'apk') {
    $v['info'] = array('author' => '', 'version' => '', 'models' => '');

    if ($setup['screen_file_change']) {
        if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif')) {
            $v['screen_file'] = DIRECTORY . $setup['tpath'] . '/' . $prev_pic . '.gif';
        } elseif (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
            $v['flash_file'] = DIRECTORY . $setup['tpath'] . '/' . $prev_pic . '.gif.swf';
        } else {
            $v['screen_file'] = DIRECTORY . 'theme/' . $id;
        }
    }

    if ($ext == 'thm') {
        $v['info'] = getThmInfo($id, $v['path']);
    }
}

if ($setup['swf_file_change'] && $ext == 'swf') {
    $v['flash_file'] = DIRECTORY . $v['path'];
}

if ($setup['jar_file_change'] && $ext == 'jar') {
    if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
        $v['screen_file'] = DIRECTORY . $setup['ipath'] . '/' . $prev_pic . '.png';
    } elseif (jar_ico($v['path'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
        $v['screen_file'] = DIRECTORY . $setup['ipath'] . '/' . $prev_pic . '.png';
    }
}
