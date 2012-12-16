<div class="row">
    <strong>{$language.size}:</strong> {$file.size|sizeFormatExtended}<br/>
    <strong>{$language.downloaded}:</strong> {$file.loads} {$language.times}<br/>
    <strong>{$language.time_additions}:</strong> {$file.timeupload|dateFormatExtended}<br/>
{if $file.timeload}
    <strong>{$language.recent}:</strong> {$file.timeload|dateFormatExtended}<br/>
{/if}
</div>
<div class="row">

    {if $setup.screen_file_change}
        {if $file.screen_file}
            <img src="{$file.screen_file}" alt=""/><br/>
        {/if}
        {if $file.flash_file}
            <object style="width:128px; height:128px;">
                <param name="movie" value="{$file.flash_file}">
                    <embed src="{$file.flash_file}" style="width:128px; height:128px;"></embed>
                </param>
            </object><br/>
        {/if}
    {/if}

    {if ($file.ext == 'gif' || $file.ext == 'jpg' || $file.ext == 'jpeg' || $file.ext == 'jpe' || $file.ext == 'png' || $file.ext == 'bmp')}

        {$file.imagesize.w}x{$file.imagesize.h}<br/>
        <strong>{$language.custom_size}:</strong>

        {foreach $file.imagelink as $val => $link}
            <a href="{$link}">{$val}</a>
        {/foreach}

        <form action="{$smarty.const.DIRECTORY}im/{$id}" method="get">
            <div class="row">
                <input class="enter" type="number" size="5" name="w" required="required" min="1" max="65536"/>x<input class="enter" type="number" size="5" name="h" required="required" min="1" max="65536"/><br/>
                <input class="buttom" type="submit" value="{$language.download}"/>
            </div>
        </form>
    {/if}


    {if ($file.ext == 'mp3' || $file.ext == 'wav' || $file.ext == 'ogg')}
        <strong>{$language.info}:</strong><br/>
        {$language.channels}: {$file.info.channels}<br/>
        {$language.framerate}: {$file.info.sampleRate} Hz<br/>
        {$language.byterate}: {round($file.info.avgBitrate / 1024)} Kbps<br/>
        {$language.length}: {mktime(0, 0, $file.info.streamLength)|date_format:'H:i:s'}<br/>

        {if $file.info.tag.title}
            {$language.name}: {$file.info.tag.title}<br/>
        {/if}
        {if $file.info.tag.artist}
            {$language.artist}: {$file.info.tag.artist}<br/>
        {/if}
        {if $file.info.tag.album}
            {$language.album}: {$file.info.tag.album}<br/>
        {/if}
        {if $file.info.tag.date}
            {$language.year}: {$file.info.tag.date}<br/>
        {/if}
        {if $file.info.tag.genre}
            {$language.genre}: {$file.info.tag.genre}<br/>
        {/if}
        {if $file.info.tag.comment}
            {$language.comments}: {$file.info.tag.comment}<br/>
        {/if}
        {if $file.info.tag.apic}
            <img src="{$smarty.const.DIRECTORY}apic/{$id}" alt=""/><br/>
        {/if}
    {/if}

    {if (($file.ext == '3gp' || $file.ext == 'avi' || $file.ext == 'mp4' || $file.ext == 'flv') && extension_loaded('ffmpeg'))}

        {if $setup.screen_file_change}
            {foreach from=$setup.ffmpeg_frames|explode:',' key=i item=frame}
                <a href="{$smarty.const.DIRECTORY}view/{$id}?frame={$frame}">[{$i + 1}]</a>,
            {/foreach}
        {/if}

        {$language.codec}: {$file.info.getVideoCodec}<br/>
        {$language.screen_resolution}: {$file.info.GetFrameWidth}x{$file.info.GetFrameHeight}<br/>
        {$language.time}: {mktime(0, 0, round($file.info.getDuration))|date_format:'H:i:s'}<br/>
        {$language.bitrate}: {round($file.info.getBitRate / 1024)} Kbps<br/>
    {/if}

    {if ($file.ext == 'thm' || $file.ext == 'nth' || $file.ext == 'utz' || $file.ext == 'sdt' || $file.ext == 'scs' || $file.ext == 'apk')}
        {if $file.info.author}
            {$language.author}: {$file.info.author}<br/>
        {/if}
        {if $file.info.version}
            {$language.version}: {$file.info.version}<br/>
        {/if}
        {if $file.info.models}
            {$language.models}: {$file.info.models}<br/>
        {/if}
    {/if}


    {if $file.screen}
        <strong>{$language.screenshot}:</strong><br/>
        <img style="margin: 1px;" src="{$file.screen}" alt=""/><br/>
    {/if}

    {if $file.description}
        <strong>{$language.description}:</strong><br/>
        {$file.description|bbcode nofilter}<br/>
    {/if}

    {if $file.attachments}
        <strong>{$language.attachments}:</strong><br/>
        {foreach $file.attachments as $key => $val}
            <a href="{$val.link}">{$val.name}</a> [<a href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=del_attach&amp;attach={$key}" class="no">X</a>]<br/>
        {/foreach}
    {/if}
</div>


{* администрирование *}
{if $smarty.const.IS_ADMIN}
<div class="iblock">
    <form action="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$id}&amp;action=move" method="post">
        <div>
            <label for="topath">Директория:</label>
            {html_options class='buttom' id='topath' name='topath' options=$dirs selected=$file.infolder}
            <br/>
            <input type="submit" value="Переместить" class="buttom"/>
        </div>
    </form>

    <form action="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$id}&amp;action=add_attach" method="post" enctype="multipart/form-data">
        <div>
            <label for="attach">Вложение:</label>
            <input id="attach" name="attach" type="file" class="buttom"/><br/>
            <input class="buttom" type="submit" value="Добавить"/>
        </div>
    </form>
</div>
{/if}
