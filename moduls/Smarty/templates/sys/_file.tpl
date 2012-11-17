<div class="row">
    <strong>{$language.size}:</strong> {$file.size|sizeFormatExtended}<br/>
    <strong>{$language.downloaded}:</strong> {$file.loads} {$language.times}<br/>
    <strong>{$language.time_additions}:</strong> {$file.timeupload|dateFormatExtended}<br/>
{if $file.timeload}
    <strong>{$language.recent}:</strong> {$file.timeload|dateFormatExtended}<br/>
{/if}
</div>
<div class="row">

    {if ($file.ext == 'gif' || $file.ext == 'jpg' || $file.ext == 'jpeg' || $file.ext == 'jpe' || $file.ext == 'png' || $file.ext == 'bmp')}
        {if ($setup['screen_file_change'])}
            <img src="{$file.screen_file}" alt=""/><br/>
        {/if}

        {$file.imagesize.w}x{$file.imagesize.h}<br/>
        <strong>{$language.custom_size}:</strong>

        {foreach $file.imagelink as $val => $link}
            <a href="{$link}">{$val}</a>
        {/foreach}

        <form action="{$smarty.const.DIRECTORY}im/{$id}" method="get">
            <div class="row">
                <input class="enter" type="number" size="5" name="w" required="required" pattern="^[0-9]+$" min="1" max="65536"/>x<input class="enter" type="number" size="5" name="h" required="required" pattern="^[0-9]+$" min="1" max="65536"/><br/>
                <input class="buttom" type="submit" value="{$language.download}"/>
            </div>
        </form>
    {/if}




    {if $file.screen}
        <strong>{$language.screenshot}:</strong><br/>
        <img style="margin: 1px;" src="{$file.screen}" alt=""/><br/>
    {/if}

    {if $file.description}
        <strong>{$language.description}:</strong><br/>
        {$file.description nofilter}<br/>
    {/if}


    {if $file.attachments}
        <strong>{$language.attachments}:</strong>
        {foreach $file.attachments as $link => $val}
            <a href="{$link}">{$val}</a><br/>
        {/foreach}
    {/if}

</div>

{*
if ($ext == 'mp3' || $ext == 'wav' || $ext == 'ogg') {
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

*}