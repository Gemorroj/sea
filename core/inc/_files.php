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
            $v['ico'] = DIRECTORY . $v['v'] . 'folder.png';
        } else {
            $v['ico'] = DIRECTORY . 'style/ext/dir.png';
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
                if (is_file(Config::get('picpath') . '/' . $prev_pic . '.gif')) {
                    $v['pre'] = DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.gif';
                } else {
                    $v['pre'] = DIRECTORY . 'im/' . $v['id'];
                }
            } else {
                if (Config::get('screen_change') && Media_Video::isSupported($ext)) {
                    if (is_file(Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . Config::get('ffmpeg_frame') . '.gif')) {
                        $v['pre']
                            = DIRECTORY . Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . Config::get('ffmpeg_frame')
                            . '.gif';
                    } else {
                        $v['pre'] = DIRECTORY . 'ffmpeg/' . $v['id'];
                    }
                } else {
                    if (Config::get('screen_change') && Media_Theme::isSupported($ext)) {
                        if (is_file(Config::get('tpath') . '/' . $prev_pic . '.gif')) {
                            $v['pre'] = DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.gif';
                        } else {
                            if (Config::get('swf_change') && is_file(Config::get('tpath') . '/' . $prev_pic . '.gif.swf')) {
                                $v['pre'] = DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.gif.swf';
                            } else {
                                if (!is_file(Config::get('tpath') . '/' . $prev_pic . '.gif.swf')) {
                                    $v['pre'] = DIRECTORY . 'theme/' . $v['id'];
                                }
                            }
                        }
                    } else {
                        if (Config::get('jar_change') && Media_Jar::isSupported($ext)) {
                            if (is_file(Config::get('ipath') . '/' . $prev_pic . '.png')) {
                                $v['pre'] = DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
                            } else {
                                $v['pre'] = DIRECTORY . 'jar/' . $v['id'];
                            }
                        } else {
                            if (Config::get('swf_change') && $ext == 'swf') {
                                $v['pre'] = DIRECTORY . $v['v'];
                            }
                        }
                    }
                }
            }
        }

        //Иконка к файлу
        if (is_file('style/ext/' . $ext . '.png')) {
            $v['ico'] = DIRECTORY . 'style/ext/' . $ext . '.png';
        } else {
            $v['ico'] = DIRECTORY . 'style/ext/stand.png';
        }


        // скриншот
        if (Config::get('screen_change')) {
            $th_gif = is_file(Config::get('spath') . $screen . '.thumb.gif');

            if (!$th_gif && is_file(Config::get('spath') . $screen . '.gif')) {
                $th_gif = Image::resize(
                    Config::get('spath') . $screen . '.gif',
                    Config::get('spath') . $screen . '.thumb.gif',
                    0,
                    0,
                    Config::get('marker')
                );
            } elseif (!$th_gif && is_file(Config::get('spath') . $screen . '.jpg')) {
                $th_gif = Image::resize(
                    Config::get('spath') . $screen . '.jpg',
                    Config::get('spath') . $screen . '.thumb.gif',
                    0,
                    0,
                    Config::get('marker')
                );
            } elseif (!$th_gif && is_file(Config::get('spath') . $screen . '.png')) {
                $th_gif = Image::resize(
                    Config::get('spath') . $screen . '.png',
                    Config::get('spath') . $screen . '.thumb.gif',
                    0,
                    0,
                    Config::get('marker')
                );
            }

            if ($th_gif) {
                $v['screen'] = DIRECTORY . Config::get('spath') . $screen . '.thumb.gif';
            }
        }

        $files[] = $v;
    }
}
