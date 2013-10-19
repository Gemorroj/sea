<?php
$directories = $files = array();

$sort = Helper::get2ses('sort');
$prev = Helper::get2ses('prev');
if ($prev != '0' && $prev != '1') {
    $prev = Config::get('prev');
}

$template = Http_Response::getInstance()->getTemplate();
$template->assign('prev', $prev);
$template->assign('sort', $sort);


foreach ($query as $v) {
    $screen = strstr($v['v'], '/'); // убираем папку с загрузками

    if (Config::get('desc') && is_file(Config::get('opath') . $screen . '.txt')) {
        $v['description'] = file_get_contents(Config::get('opath') . $screen . '.txt');
    } else {
        $v['description'] = '';
    }

    if ($v['dir']) {
        if (is_file($v['v'] . 'folder.png')) {
            $v['ico'] = SEA_PUBLIC_DIRECTORY . $v['v'] . 'folder.png';
        } else {
            $v['ico'] = SEA_PUBLIC_DIRECTORY . 'style/ext/dir.png';
        }

        $directories[] = $v;
    } else {
        $prev_pic = str_replace('/', '--', mb_substr($screen, 1));
        $ext = strtolower(pathinfo($v['v'], PATHINFO_EXTENSION));
        $v['pre'] = '';
        $v['screen'] = '';
        $v['ext'] = $ext;

        //Предпросмотр
        if ($prev) {
            if (Config::get('screen_change') && Media_Image::isSupported($ext)) {
                if (is_file(Config::get('picpath') . '/' . $prev_pic . '.png')) {
                    $v['pre'] = SEA_PUBLIC_DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.png';
                } elseif (is_file(Config::get('picpath') . '/' . $prev_pic . '.png.gif')) {
                    $v['pre'] = SEA_PUBLIC_DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.png.gif';
                } else {
                    $v['pre'] = SEA_PUBLIC_DIRECTORY . 'im/' . $v['id'];
                }
            } else {
                if (Config::get('screen_change') && Media_Video::isSupported($ext)) {
                    if (is_file(Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . Config::get('ffmpeg_frame') . '.png')) {
                        $v['pre'] = SEA_PUBLIC_DIRECTORY . Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . Config::get('ffmpeg_frame') . '.png';
                    } else {
                        $v['pre'] = SEA_PUBLIC_DIRECTORY . 'ffmpeg/' . $v['id'];
                    }
                } else {
                    if (Config::get('screen_change') && Media_Theme::isSupported($ext)) {
                        if (is_file(Config::get('tpath') . '/' . $prev_pic . '.png')) {
                            $v['pre'] = SEA_PUBLIC_DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.png';
                        } else {
                            if (Config::get('swf_change') && is_file(Config::get('tpath') . '/' . $prev_pic . '.png.swf')) {
                                $v['pre'] = SEA_PUBLIC_DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.png.swf';
                            } else {
                                if (!is_file(Config::get('tpath') . '/' . $prev_pic . '.png.swf')) {
                                    $v['pre'] = SEA_PUBLIC_DIRECTORY . 'theme/' . $v['id'];
                                }
                            }
                        }
                    } else {
                        if (Config::get('jar_change') && Media_Jar::isSupported($ext)) {
                            if (is_file(Config::get('ipath') . '/' . $prev_pic . '.png')) {
                                $v['pre'] = SEA_PUBLIC_DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
                            } else {
                                $v['pre'] = SEA_PUBLIC_DIRECTORY . 'jar/' . $v['id'];
                            }
                        } else {
                            if (Config::get('swf_change') && $ext == 'swf') {
                                $v['pre'] = SEA_PUBLIC_DIRECTORY . $v['v'];
                            }
                        }
                    }
                }
            }
        }

        //Иконка к файлу
        if (is_file('style/ext/' . $ext . '.png')) {
            $v['ico'] = SEA_PUBLIC_DIRECTORY . 'style/ext/' . $ext . '.png';
        } else {
            $v['ico'] = SEA_PUBLIC_DIRECTORY . 'style/ext/stand.png';
        }


        // скриншот
        if (Config::get('screen_change')) {
            $thumb = Config::get('spath') . $screen . '.thumb.png';
            $th = is_file($thumb);
            if (!$th) {
                $thumb = $thumb . '.gif';
                $th = is_file($thumb);
            }

            if (!$th) {
                if (is_file(Config::get('spath') . $screen . '.png')) {
                    $th = Image::resize(
                        Config::get('spath') . $screen . '.png',
                        $thumb,
                        0,
                        0,
                        Config::get('marker')
                    );
                } elseif (is_file(Config::get('spath') . $screen . '.gif')) {
                    $th = Image::resize(
                        Config::get('spath') . $screen . '.gif',
                        $thumb,
                        0,
                        0,
                        Config::get('marker')
                    );
                } elseif (is_file(Config::get('spath') . $screen . '.jpg')) {
                    $th = Image::resize(
                        Config::get('spath') . $screen . '.jpg',
                        $thumb,
                        0,
                        0,
                        Config::get('marker')
                    );
                }
            }

            if ($th) {
                $v['screen'] = SEA_PUBLIC_DIRECTORY . $thumb;
            }
        }

        $files[] = $v;
    }
}
