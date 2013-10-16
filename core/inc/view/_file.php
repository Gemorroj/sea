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
if (is_file(Config::get('spath') . $screen . '.png')) {
    $file['screen'] = SEA_PUBLIC_DIRECTORY . Config::get('spath') . $screen . '.png';
} elseif (is_file(Config::get('spath') . $screen . '.gif')) {
    $file['screen'] = SEA_PUBLIC_DIRECTORY . Config::get('spath') . $screen . '.gif';
} elseif (is_file(Config::get('spath') . $screen . '.jpg')) {
    $file['screen'] = SEA_PUBLIC_DIRECTORY . Config::get('spath') . $screen . '.jpg';
}

// Описание
if (is_file(Config::get('opath') . '/' . $screen . '.txt')) {
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
                'link' => SEA_PUBLIC_DIRECTORY . $dir . $id . '_' . $k . '_' . $val,
                'size' => filesize($dir . $id . '_' . $k . '_' . $val)
            );
        }
    }
}



if (Media_Image::isSupported($ext)) {
    if (Config::get('screen_file_change')) {
        if (is_file(Config::get('picpath') . '/' . $prev_pic . '.png')) {
            $file['screen_file'] = SEA_PUBLIC_DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.png';
        } else {
            $file['screen_file'] = SEA_PUBLIC_DIRECTORY . 'im/' . $id;
        }
    }

    $size = getimagesize($file['path']);
    $file['imagesize'] = array('w' => $size[0], 'h' => $size[1]);

    foreach (explode(',', Config::get('view_size')) as $val) {
        $wh = explode('*', $val);
        if (is_file(Config::get('picpath') . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.png')) {
            $file['imagelink'][$val] = SEA_PUBLIC_DIRECTORY . Config::get('picpath') . '/' . $wh[0] . 'x' . $wh[1] . '_' . $prev_pic . '.png';
        } else {
            $file['imagelink'][$val] = SEA_PUBLIC_DIRECTORY . 'im/' . $id . '?w=' . $wh[0] . '&h=' . $wh[1];
        }
    }
}

if (Media_Audio::isSupported($ext)) {
    $file['info'] = Media_Audio::getInfo($id, $file['path']);
}

if (Media_Video::isSupported($ext)) {
    $file['info'] = Media_Video::getInfo($id, $file['path']);

    if (Config::get('screen_file_change')) {
        $frame = abs(Http_Request::get('frame', Config::get('ffmpeg_frame')));
        if (is_file(Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . $frame . '.png')) {
            $file['screen_file'] = SEA_PUBLIC_DIRECTORY . Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . $frame . '.png';
        } else {
            $file['screen_file'] = SEA_PUBLIC_DIRECTORY . 'ffmpeg/' . $id . '?frame=' . $frame;
        }
    }
}

if (Media_Theme::isSupported($ext)) {
    if (Config::get('screen_file_change')) {
        if (is_file(Config::get('tpath') . '/' . $prev_pic . '.png')) {
            $file['screen_file'] = SEA_PUBLIC_DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.png';
        } elseif (is_file(Config::get('tpath') . '/' . $prev_pic . '.png.swf')) {
            $file['flash_file'] = SEA_PUBLIC_DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.png.swf';
        } else {
            $file['screen_file'] = SEA_PUBLIC_DIRECTORY . 'theme/' . $id;
        }
    }

    $file['info'] = Media_Theme::getInfo($id, $file['path']);
}

if (Config::get('swf_file_change') && $ext == 'swf') {
    $file['flash_file'] = SEA_PUBLIC_DIRECTORY . $file['path'];
}

if (Config::get('jar_file_change') && Media_Jar::isSupported($ext)) {
    if (is_file(Config::get('ipath') . '/' . $prev_pic . '.png')) {
        $file['screen_file'] = SEA_PUBLIC_DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
    } else {
        $file['screen_file'] = SEA_PUBLIC_DIRECTORY . 'jar/' . $id;
    }
}
