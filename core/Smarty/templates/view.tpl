{extends file='sys/layout.tpl'}


{block header}
    <div class="iblock">
        {$language.file_information} "<strong>{$file.name}</strong>"
    </div>
{/block}


{block javascripts}
    {assign var="audio_player" value=($setup.audio_player_change && Media_Audio::isPlayerSupported($file.ext))}
    {assign var="video_player" value=($setup.video_player_change && Media_Video::isPlayerSupported($file.ext))}

    {if $audio_player}
        <script type="text/javascript" src="{$smarty.const.DIRECTORY}style/jwplayer/jwplayer.js"></script>
        <script type="text/javascript">
            window.onload = function () {
                jwplayer("audio_player").setup({
                    'file': "{$smarty.const.DIRECTORY}{$file.path}",
                    'title': "{$file.name}",
                    'height': 40,
                    'width': 320,
                    analytics: {
                        enabled: false,
                        cookies: false
                    }
                });
            };
        </script>
    {elseif $video_player}
        <script type="text/javascript" src="{$smarty.const.DIRECTORY}style/jwplayer/jwplayer.js"></script>
        <script type="text/javascript">
            window.onload = function () {
                jwplayer("video_player").setup({
                    {if extension_loaded('ffmpeg')}'image': "{$smarty.const.DIRECTORY}ffmpeg/{$id}",{/if}
                    'file': "{$smarty.const.DIRECTORY}{$file.path}",
                    'title': "{$file.name}",
                    'height': 180,
                    'width': 320,
                    analytics: {
                        enabled: false,
                        cookies: false
                    }
                });
            };
        </script>
    {/if}
{/block}


{* просмотр файла *}
{block content}
    {include file='sys/_file.tpl'}

    {if $setup.prev_next && ($prevNext.prev || $prevNext.next)}
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
                <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=clearrate" title="Очистить" class="no" onclick="return window.confirm('Очистить рейтинг?');">[X]</a>
            {/if}


            <br/>
            <img src="{$smarty.const.DIRECTORY}rate/{$rate}" alt="" style="margin: 1px;"/><br/>
            {if $vote === null}
                {$language.net}: <span class="yes"><a href="{$smarty.const.DIRECTORY}view/{$id}?eval=1">{$language.yes}</a></span>/<span class="no"><a href="{$smarty.const.DIRECTORY}view/{$id}?eval=0">{$language.no}</a></span>
            {elseif $vote === true}
                {$language.true_voice}
            {elseif $vote === false}
                {$language.false_voice}
            {/if}
        </div>
    {/if}

    {if $setup.comments_view && $comments}
        <div class="iblock">
            {foreach $comments as $comment}
                <div class="{cycle values="row,row2"}">
                    <strong>{$comment.name}</strong> ({$comment.time|dateFormatExtended})<br/>
                    <span class="comment">{$comment.text|bbcode nofilter}</span>
                </div>
            {/foreach}
        </div>
    {/if}
    <div class="iblock">
        <div id="audio_player"></div><div id="video_player"></div>

        {if ($setup.cut_change && $file.ext == 'mp3')}
            <strong><a href="{$smarty.const.DIRECTORY}cut/{$id}">{$language.splitting}</a></strong><br/>
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
        {if ($setup.jad_change && Media_Jar::isSupported($file.ext))}
            <strong><a href="{$smarty.const.DIRECTORY}jad/{$id}">{$language.download} [JAD]</a></strong><br/>
        {/if}

        <input class="enter" size="50" type="url" value="http://{$smarty.server.HTTP_HOST}{$smarty.const.DIRECTORY}{$file.path}"/><br/>
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
                <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=clearcomm" title="Очистить" class="no" onclick="return window.confirm('Очистить комментарии?');">[X]</a>
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