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
if (file_exists(Config::get('spath') . $screen . '.gif')) {
    $file['screen'] = DIRECTORY . Config::get('spath') . $screen . '.gif';
} elseif (file_exists(Config::get('spath') . $screen . '.jpg')) {
    $file['screen'] = DIRECTORY . Config::get('spath') . $screen . '.jpg';
} elseif (file_exists(Config::get('spath') . $screen . '.png')) {
    $file['screen'] = DIRECTORY . Config::get('spath') . $screen . '.png';
}

// Описание
if (file_exists(Config::get('opath') . '/' . $screen . '.txt')) {
    $file['description'] = trim(file_get_contents(Config::get('opath') . '/' . $screen . '.txt'));
} elseif (Config::get('lib_desc') && $ext == 'txt') {
    $fp = fopen($file['path'], 'r');
    $file['description'] = trim(fgets($fp, 1024));
    fclose($fp);
}

// Вложения
if ($file['attach']) {
    $attach = unserialize($file['attach']);
    if ($attach) {
        $dir = Config::get('apath') . dirname($screen) . '/';
        foreach ($attach as $k => $val) {
            $file['attachments'][$k] = array(
                'name' => $val,
                'link' => DIRECTORY . $dir . $id . '_' . $k . '_' . $val,
                'size' => filesize($dir . $id . '_' . $k . '_' . $val)
            );
        }
    }
}



if ($ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'jpe' || $ext == 'png' || $ext == 'bmp') {
    if (Config::get('screen_file_change')) {
        if (file_exists(Config::get('picpath') . '/' . $prev_pic . '.gif')) {
            $file['screen_file'] = DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.gif';
        } else {
            $file['screen_file'] = DIRECTORY . 'im/' . $id;
        }
    }

    $size = getimagesize($file['path']);
    $file['imagesize'] = array('w' => $size[0], 'h' => $size[1]);

    foreach (explode(',', Config::get('view_size')) as $val) {
        $wh = explode('*', $val);
        if (file_exists(Config::get('picpath') . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif')) {
            $file['imagelink'][$val] = DIRECTORY . Config::get('picpath') . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.gif';
        } else {
            $file['imagelink'][$val] = DIRECTORY . 'im/' . $id . '?w=' . $wh[0] . '&h=' . $wh[1];
        }
    }
}

if (Media_Audio::isSupported($ext)) {
    $file['info'] = Media_Audio::getInfo($id, $file['path']);
}

if (Media_Video::isSupported($ext)) {
    $file['info'] = Media_Video::getInfo($id, $file['path']);

    if (Config::get('screen_file_change')) {
        $frame = isset($_GET['frame']) ? abs($_GET['frame']) : Config::get('ffmpeg_frame');
        if (file_exists(Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . $frame . '.gif')) {
            $file['screen_file'] = DIRECTORY . Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . $frame . '.gif';
        } else {
            $file['screen_file'] = DIRECTORY . 'ffmpeg/' . $id . '?frame=' . $frame;
        }
    }
}

if (Media_Theme::isSupported($ext)) {
    if (Config::get('screen_file_change')) {
        if (file_exists(Config::get('tpath') . '/' . $prev_pic . '.gif')) {
            $file['screen_file'] = DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.gif';
        } elseif (file_exists(Config::get('tpath') . '/' . $prev_pic . '.gif.swf')) {
            $file['flash_file'] = DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.gif.swf';
        } else {
            $file['screen_file'] = DIRECTORY . 'theme/' . $id;
        }
    }

    $file['info'] = Media_Theme::getInfo($id, $file['path']);
}

if (Config::get('swf_file_change') && $ext == 'swf') {
    $file['flash_file'] = DIRECTORY . $file['path'];
}

if (Config::get('jar_file_change') && $ext == 'jar') {
    if (file_exists(Config::get('ipath') . '/' . $prev_pic . '.png')) {
        $file['screen_file'] = DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
    } elseif (jar_ico($file['path'], Config::get('ipath') . '/' . $prev_pic . '.png')) {
        $file['screen_file'] = DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
    }
}
