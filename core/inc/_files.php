<?php
$directories = $files = array();

$sort = get2ses('sort');
$prew = get2ses('prew');
if ($prew != '0' && $prew != '1') {
    $prew = $setup['preview'];
}

$template->assign('prew', $prew);
$template->assign('sort', $sort);


foreach ($query as $v) {
    $screen = strstr($v['v'], '/'); // убираем папку с загрузками

    if ($setup['desc'] && file_exists($setup['opath'] . $screen . '.txt')) {
        $v['description'] = file_get_contents($setup['opath'] . $screen . '.txt');
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
        if ($prew) {
            if ($setup['screen_change']
                && ($ext == 'gif' || $ext == 'jpeg' || $ext == 'jpg' || $ext == 'png' || $ext == 'bmp')
            ) {
                if (file_exists($setup['picpath'] . '/' . $prev_pic . '.gif')) {
                    $v['pre'] = DIRECTORY . $setup['picpath'] . '/' . $prev_pic . '.gif';
                } else {
                    $v['pre'] = DIRECTORY . 'im/' . $v['id'];
                }
            } else {
                if ($setup['screen_change'] && ($ext == 'avi' || $ext == '3gp' || $ext == 'mp4' || $ext == 'flv')
                    && extension_loaded('ffmpeg')
                ) {
                    if (file_exists(
                        $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $setup['ffmpeg_frame'] . '.gif'
                    )
                    ) {
                        $v['pre']
                            = DIRECTORY . $setup['ffmpegpath'] . '/' . $prev_pic . '_frame_' . $setup['ffmpeg_frame']
                            . '.gif';
                    } else {
                        $v['pre'] = DIRECTORY . 'ffmpeg/' . $v['id'];
                    }
                } else {
                    if ($setup['screen_change']
                        && ($ext == 'thm' || $ext == 'nth' || $ext == 'utz' || $ext == 'sdt' || $ext == 'scs'
                            || $ext == 'apk')
                    ) {
                        if (file_exists($setup['tpath'] . '/' . $prev_pic . '.gif')) {
                            $v['pre'] = DIRECTORY . $setup['tpath'] . '/' . $prev_pic . '.gif';
                        } else {
                            if ($setup['swf_change'] && file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
                                $v['pre'] = DIRECTORY . $setup['tpath'] . '/' . $prev_pic . '.gif.swf';
                            } else {
                                if (!file_exists($setup['tpath'] . '/' . $prev_pic . '.gif.swf')) {
                                    $v['pre'] = DIRECTORY . 'theme/' . $v['id'];
                                }
                            }
                        }
                    } else {
                        if ($setup['jar_change'] && $ext == 'jar') {
                            if (file_exists($setup['ipath'] . '/' . $prev_pic . '.png')) {
                                $v['pre'] = DIRECTORY . $setup['ipath'] . '/' . $prev_pic . '.png';
                            } else {
                                if (jar_ico($v['v'], $setup['ipath'] . '/' . $prev_pic . '.png')) {
                                    $v['pre'] = DIRECTORY . $setup['ipath'] . '/' . $prev_pic . '.png';
                                }
                            }
                        } else {
                            if ($setup['swf_change'] && $ext == 'swf') {
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
        if ($setup['screen_change']) {
            $th_gif = file_exists($setup['spath'] . $screen . '.thumb.gif');

            if (!$th_gif && file_exists($setup['spath'] . $screen . '.gif')) {
                $th_gif = img_resize(
                    $setup['spath'] . $screen . '.gif',
                    $setup['spath'] . $screen . '.thumb.gif',
                    0,
                    0,
                    $setup['marker']
                );
            } elseif (!$th_gif && file_exists($setup['spath'] . $screen . '.jpg')) {
                $th_gif = img_resize(
                    $setup['spath'] . $screen . '.jpg',
                    $setup['spath'] . $screen . '.thumb.gif',
                    0,
                    0,
                    $setup['marker']
                );
            } elseif (!$th_gif && file_exists($setup['spath'] . $screen . '.png')) {
                $th_gif = img_resize(
                    $setup['spath'] . $screen . '.png',
                    $setup['spath'] . $screen . '.thumb.gif',
                    0,
                    0,
                    $setup['marker']
                );
            }

            if ($th_gif) {
                $v['screen'] = DIRECTORY . $setup['spath'] . $screen . '.thumb.gif';
            }
        }

        $files[] = $v;
    }
}
