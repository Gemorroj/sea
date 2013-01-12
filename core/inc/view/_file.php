<?php
// убираем папку с загрузками
$screen = strstr($file['path'], '/');
$prev_pic = str_replace('/', '--', mb_substr($screen, 1));


$file['attachments'] = '';
$file['description'] = '';
$file['screen'] = '';
$file['screen_file'] = '';
$file['imagesize'] = array('w' => '', 'h' => '');
$file['imagelink'] = array();
$file['info'] = array();
$file['flash_file'] = '';


// Скриншот
if (file_exists($setup['spath'] . $screen . '.gif')) {
    $file['screen'] = DIRECTORY . $setup['spath'] . $screen . '.gif';
} elseif (file_exists($setup['spath'] . $screen . '.jpg')) {
    $file['screen'] = DIRECTORY . $setup['spath'] . $screen . '.jpg';
} elseif (file_exists($setup['spath'] . $screen . '.png')) {
    $file['screen'] = DIRECTORY . $setup['spath'] . $screen . '.png';
}

// Описание
if (file_exists($setup['opath'] . '/' . $screen . '.txt')) {
    $file['description'] = trim(file_get_contents($setup['opath'] . '/' . $screen . '.txt'));
} elseif ($setup['lib_desc'] && $ext == 'txt') {
    $fp = fopen($file['path'], 'r');
    $file['description'] = trim(fgets($fp, 1024));
    fclose($fp);
}

// Вложения
if ($file['attach']) {
    $attach = unserialize($file['attach']);
    if ($attach) {
        foreach ($attach as $k => $val) {
            $file['attachments'][$k] = array(
                'name' => $val,
                'link' => DIRECTORY . $setup['apath'] . dirname($screen) . '/' . $id . '_' . $k . '_' . $val
            );
        }
    }
}



if ($ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'jpe' || $ext == 'png' || $ext == 'bmp') {
    if ($setup['screen_file_change']) {
        if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
            $file['screen_file'] = DIRECTORY . $setup['picpath'] . '/' . $prev_pic . '.gif';
        } else {
            $file['screen_file'] = DIRECTORY . 'im/' . $id;
        }
    }

    $size = getimagesize($file['path']);
    $file['imagesize'] = array('w' => $size[0], 'h' => $size[1]);

    foreach (explode(',', $setup['view_size']) as $val) {
        $wh = explode('*', $val);
        if (file_exists($setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif')) {
            $file['imagelink'][$val] = DIRECTORY . $setup['picpath'] . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif';
        } else {
            $file['imagelink'][$val] = DIRECTORY . 'im/' . $id . '?w=' . $wh[0] . '&h=' . $wh[1];
        }
    }
}

if ($ext == 'mp3' || $ext == 'wav' || $ext == 'ogg') {
    $file['info'] = getMusicInfo($id, $file['path']);
}

if (($ext == '3gp' || $ext == 'avi' || $ext == 'mp4' || $ext == 'flv') && extension_loaded('ffmpeg')) {
    $file['info'] = getVideoInfo($id, $file['path']);

    if ($setup['screen_file_change']) {
        $frame = isset($_GET['frame']) ? abs($_GET['frame']) : $setup['ffmpeg_frame'];
        if (file_exists($setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif')) {
            $file['screen_file'] = DIRECTORY . $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $frame . '.gif';
        } else {
            $file['screen_file'] = DIRECTORY . 'ffmpeg/' . $id . '?frame=' . $frame;
        }
    }
}

if ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs' || $ext == 'apk') {
    $file['info'] = array('author' => '', 'version' => '', 'models' => '');

    if ($setup['screen_file_change']) {
        if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif')) {
            $file['screen_file'] = DIRECTORY . $setup['tpath'] . '/' . $prev_pic . '.gif';
        } elseif (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
            $file['flash_file'] = DIRECTORY . $setup['tpath'] . '/' . $prev_pic . '.gif.swf';
        } else {
            $file['screen_file'] = DIRECTORY . 'theme/' . $id;
        }
    }

    if ($ext == 'thm') {
        $file['info'] = getThmInfo($id, $file['path']);
    }
}

if ($setup['swf_file_change'] && $ext == 'swf') {
    $file['flash_file'] = DIRECTORY . $file['path'];
}

if ($setup['jar_file_change'] && $ext == 'jar') {
    if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
        $file['screen_file'] = DIRECTORY . $setup['ipath'] . '/' . $prev_pic . '.png';
    } elseif (jar_ico($file['path'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
        $file['screen_file'] = DIRECTORY . $setup['ipath'] . '/' . $prev_pic . '.png';
    }
}
