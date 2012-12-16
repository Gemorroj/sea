{extends file='sys/layout.tpl'}


{block header}
    <div class="iblock">
        {$language.file_information} "<strong>{$file.name}</strong>"
    </div>
{/block}

{* просмотр файла *}
{block content}
    {include file='sys/_file.tpl'}

    {if $setup.prev_next}
        <div class="iblock">
            {if $prevNext.prev}
                &#171; ({$prevNext.prev.index})<a href="{$smarty.const.DIRECTORY}view/{$prevNext.prev.id}">{$language.prev}</a>
            {/if}
            [{$prevNext.count}]
            {if $prevNext.next}
                <a href="{$smarty.const.DIRECTORY}view/{$prevNext.next.id}">{$language.next}</a>({$prevNext.next.index}) &#187;
            {/if}
        </div>
    {/if}

    {if ($setup['eval_change'])}
        <div class="iblock">
            <strong>{$language.rating}</strong>: (<span class="yes">+{$file.yes}</span>/<span class="no">-{$file.no}</span>)


            {* администрирование *}
            {if $smarty.const.IS_ADMIN}
                <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=clearrate" title="Очистить" class="no">[X]</a>
            {/if}


            <br/>
            <img src="{$smarty.const.DIRECTORY}rate/{$rate}" alt="" style="margin: 1px;"/><br/>
            {if $vote == null}
                {$language.net}: <span class="yes"><a href="{$smarty.const.DIRECTORY}view/{$id}?eval=1">{$language.yes}</a></span>/<span class="no"><a href="{$smarty.const.DIRECTORY}view/{$id}?eval=0">{$language.no}</a></span>
            {elseif $vote == 'success'}
                {$language.true_voice}
            {elseif $vote == 'fail'}
                {$language.false_voice}
            {/if}
        </div>
    {/if}

    {if $setup.comments_view && $comments}
        <div class="iblock">
            {foreach $comments as $comment}
            <div class="{cycle values="row,row2"}">
                <strong>{$comment.name}</strong> ({$comment.time|dateFormatExtended})<br/>
                {$comment.text|bbcode nofilter}
            </div>
            {/foreach}
        </div>
    {/if}
    <div class="iblock">
        {if ($setup.cut_change && $file.ext == 'mp3')}
            <strong><a href="{$smarty.const.DIRECTORY}cut/{$id}">{$language.splitting}</a></strong><br/>
        {/if}

        {if ($setup.audio_player_change && $file.ext == 'mp3')}
        <object type="application/x-shockwave-flash" data="{$smarty.const.DIRECTORY}moduls/flash/player_mp3_maxi.swf" width="180" height="20">
            <param name="FlashVars" value="mp3={$smarty.const.DIRECTORY}{$file.path|rawurlencode|replace:'%2F':'/'}&amp;width=180&amp;volume=50&amp;showvolume=1&amp;buttonwidth=20&amp;sliderheight=8&amp;volumewidth=50&amp;volumeheight=8" />
        </object><br/>
        {/if}

        {if ($setup.video_player_change && ($file.ext == 'flv' || $file.ext == 'mp4'))}
        <object type="application/x-shockwave-flash" data="{$smarty.const.DIRECTORY}moduls/flash/player_flv_maxi.swf" width="240" height="180">
            <param name="allowFullScreen" value="true" />
            <param name="FlashVars" value="flv={$smarty.const.DIRECTORY}{$file.path|rawurlencode|replace:'%2F':'/'}&amp;title={$file.name|rawurlencode}&amp;startimage={$smarty.const.DIRECTORY}ffmpeg/{$id}?frame={$setup.ffmpeg_frame}&amp;width=240&amp;height=180&amp;margin=3&amp;volume=100&amp;showvolume=1&amp;showtime=1&amp;showplayer=always&amp;showloading=always&amp;showfullscreen=1&amp;showiconplay=1" />
        </object><br/>
        {/if}


        {if ($setup.zip_change && $file.ext == 'zip')}
            <strong><a href="{$smarty.const.DIRECTORY}zip/{$id}">{$language.view_archive}</a></strong><br/>
        {/if}

        {if $file.ext == 'txt'}
            {if $setup.lib_change}
                <strong><a href="{$smarty.const.DIRECTORY}read/{$id}">{$language.read}</a></strong><br/>
            {/if}

            <a href="{$smarty.const.DIRECTORY}txt_zip/{$id}">{$language.download} [ZIP]</a><br/>
            <a href="{$smarty.const.DIRECTORY}txt_jar/{$id}">{$language.download} [JAR]</a><br/>
        {/if}


        <strong><a href="{$smarty.const.DIRECTORY}load/{$id}">{$language.download} [{$file.ext|upper}]</a></strong><br/>
        {if ($setup.jad_change && $file.ext == 'jar')}
            <strong><a href="{$smarty.const.DIRECTORY}jad/{$id}">{$language.download} [JAD]</a></strong><br/>
        {/if}

        <input class="enter" size="50" type="text" value="http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}{$file.path|rawurlencode|replace:'%2F':'/'}"/><br/>
        {if $setup.send_email}
            <a href="{$smarty.const.DIRECTORY}email/{$id}">{$language.send_a_link_to_email}</a><br/>
        {/if}
        {if $setup.abuse_change}
            <a href="{$smarty.const.DIRECTORY}abuse/{$id}">{$language.complain_about_a_file}</a><br/>
        {/if}

        {if ($setup['comments_change'])}
            <strong><a href="{$smarty.const.DIRECTORY}view_comments/{$id}">{$language.comments} [{$commentsCount}]</a></strong>


            {* администрирование *}
            {if $smarty.const.IS_ADMIN}
                <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=clearcomm" title="Очистить" class="no">[X]</a>
            {/if}

            <br/>
        {/if}
    </div>
{/block}


{block footer}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}{$directory.id}">{$language.go_to_the_category}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
</ul>
{/block}