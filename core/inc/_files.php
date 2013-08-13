<?php
$directories = $files = array();

$sort = get2ses('sort');
$prev = get2ses('prev');
if ($prev != '0' && $prev != '1') {
    $prev = Config::get('prev');
}

$template->assign('prev', $prev);
$template->assign('sort', $sort);


foreach ($query as $v) {
    $screen = strstr($v['v'], '/'); // убираем папку с загрузками

    if (Config::get('desc') && file_exists(Config::get('opath') . $screen . '.txt')) {
        $v['description'] = file_get_contents(Config::get('opath') . $screen . '.txt');
    } else {
        $v['description'] = '';
    }

    if ($v['dir']) {
        if (file_exists($v['v'] . 'folder.png')) {
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
            if (Config::get('screen_change')
                && ($ext == 'gif' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'bmp')
            ) {
                if (file_exists(Config::get('picpath') . '/' . $prev_pic . '.gif')) {
                    $v['pre'] = DIRECTORY . Config::get('picpath') . '/' . $prev_pic . '.gif';
                } else {
                    $v['pre'] = DIRECTORY . 'im/' . $v['id'];
                }
            } else {
                if (Config::get('screen_change') && ($ext == 'avi' || $ext == '3gp' || $ext == 'mp4' || $ext == 'flv')
                    && extension_loaded('ffmpeg')
                ) {
                    if (file_exists(
                        Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . Config::get('ffmpeg_frame') . '.gif'
                    )
                    ) {
                        $v['pre']
                            = DIRECTORY . Config::get('ffmpegpath') . '/' . $prev_pic . '_frame_' . Config::get('ffmpeg_frame')
                            . '.gif';
                    } else {
                        $v['pre'] = DIRECTORY . 'ffmpeg/' . $v['id'];
                    }
                } else {
                    if (Config::get('screen_change')
                        && ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs'
                            || $ext == 'apk')
                    ) {
                        if (file_exists(Config::get('tpath') . '/' . $prev_pic . '.gif')) {
                            $v['pre'] = DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.gif';
                        } else {
                            if (Config::get('swf_change') && file_exists(Config::get('tpath') . '/' . $prev_pic . '.gif.swf')) {
                                $v['pre'] = DIRECTORY . Config::get('tpath') . '/' . $prev_pic . '.gif.swf';
                            } else {
                                if (!file_exists(Config::get('tpath') . '/' . $prev_pic . '.gif.swf')) {
                                    $v['pre'] = DIRECTORY . 'theme/' . $v['id'];
                                }
                            }
                        }
                    } else {
                        if (Config::get('jar_change') && $ext == 'jar') {
                            if (file_exists(Config::get('ipath') . '/' . $prev_pic . '.png')) {
                                $v['pre'] = DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
                            } else {
                                if (jar_ico($v['v'], Config::get('ipath') . '/' . $prev_pic . '.png')) {
                                    $v['pre'] = DIRECTORY . Config::get('ipath') . '/' . $prev_pic . '.png';
                                }
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
        if (file_exists('style/ext/' . $ext . '.png')) {
            $v['ico'] = DIRECTORY . 'style/ext/' . $ext . '.png';
        } else {
            $v['ico'] = DIRECTORY . 'style/ext/stand.png';
        }


        // скриншот
        if (Config::get('screen_change')) {
            $th_gif = file_exists(Config::get('spath') . $screen . '.thumb.gif');

            if (!$th_gif && file_exists(Config::get('spath') . $screen . '.gif')) {
                $th_gif = Image::resize(
                    Config::get('spath') . $screen . '.gif',
                    Config::get('spath') . $screen . '.thumb.gif',
                    0,
                    0,
                    Config::get('marker')
                );
            } elseif (!$th_gif && file_exists(Config::get('spath') . $screen . '.jpg')) {
                $th_gif = Image::resize(
                    Config::get('spath') . $screen . '.jpg',
                    Config::get('spath') . $screen . '.thumb.gif',
                    0,
                    0,
                    Config::get('marker')
                );
            } elseif (!$th_gif && file_exists(Config::get('spath') . $screen . '.png')) {
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
