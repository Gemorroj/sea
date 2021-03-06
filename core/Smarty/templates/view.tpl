{extends file='sys/layout.tpl'}


{block header}
    <div class="iblock">
        {$language.file_information} "<strong>{$file.name}</strong>"
    </div>
{/block}


{block javascripts}
    {assign var="audio_player" value=($setup.audio_player_change && Media_Audio::isPlayerSupported($file.ext))}
    {assign var="video_player" value=($setup.video_player_change && Media_Video::isPlayerSupported($file.ext))}

    {if $audio_player || $video_player}
        <script type="text/javascript" src="//content.jwplatform.com/libraries/aZNPD7oe.js"></script>
        <script type="text/javascript">
            function seaAddLoadEvent(func) {
                if (window.addEventListener) {
                    window.addEventListener('load', func, false);
                } else if (window.attachEvent) {
                    window.attachEvent('onload', func);
                }
            }
        </script>
    {/if}

    {if $audio_player}
        <script type="text/javascript">
            seaAddLoadEvent(function () {
                jwplayer("audio_player").setup({
                    'file': "{$smarty.const.SEA_PUBLIC_DIRECTORY}{$file.path}",
                    //'title': "{$file.name}",
                    'height': 40,
                    'width': 320
                });
            });
        </script>
    {elseif $video_player}
        <script type="text/javascript">
            seaAddLoadEvent(function () {
                jwplayer("video_player").setup({
                    {if extension_loaded('ffmpeg')}'image': "{$smarty.const.SEA_PUBLIC_DIRECTORY}ffmpeg/{Http_Request::get('id')}",{/if}
                    'file': "{$smarty.const.SEA_PUBLIC_DIRECTORY}{$file.path}",
                    'title': "{$file.name}",
                    'height': 180,
                    'width': 320
                });
            });
        </script>
    {/if}
{/block}


{* просмотр файла *}
{block content}
    {include file='sys/_file.tpl'}

    {if $setup.prev_next && ($prevNext.prev || $prevNext.next)}
        <div class="iblock">
            {if $prevNext.prev}
                &#171; ({$prevNext.prev.index})<a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}view/{$prevNext.prev.id}">{$language.prev}</a>
            {/if}
            [{$prevNext.count}]
            {if $prevNext.next}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}view/{$prevNext.next.id}">{$language.next}</a>({$prevNext.next.index}) &#187;
            {/if}
        </div>
    {/if}

    {if ($setup['eval_change'])}
        <div class="iblock">
            <strong>{$language.rating}</strong>: (<span class="yes">+{$file.yes}</span>/<span class="no">-{$file.no}</span>)


            {* администрирование *}
            {if $smarty.const.SEA_IS_ADMIN}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=clearrate" title="Очистить" class="no" onclick="return window.confirm('Очистить рейтинг?');">[X]</a>
            {/if}


            <br/>
            <img src="{$smarty.const.SEA_PUBLIC_DIRECTORY}rate/{$rate}" alt="" style="margin: 1px;"/><br/>
            {if $vote === null}
                {$language.net}: <span class="yes"><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}view/{Http_Request::get('id')}?eval=1">{$language.yes}</a></span>/<span class="no"><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}view/{Http_Request::get('id')}?eval=0">{$language.no}</a></span>
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
            <strong><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}cut/{Http_Request::get('id')}">{$language.splitting}</a></strong><br/>
        {/if}

        {if ($setup.zip_change && $file.ext == 'zip')}
            <strong><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}zip/{Http_Request::get('id')}">{$language.view_archive}</a></strong><br/>
        {/if}

        {if $file.ext == 'txt'}
            {if $setup.lib_change}
                <strong><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}read/{Http_Request::get('id')}">{$language.read}</a></strong><br/>
            {/if}

            <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}txt_zip/{Http_Request::get('id')}">{$language.download} [ZIP]</a><br/>
            <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}txt_jar/{Http_Request::get('id')}">{$language.download} [JAR]</a><br/>
        {/if}


        <strong><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}load/{Http_Request::get('id')}">{$language.download} [{$file.ext|upper}]</a></strong><br/>
        {if ($setup.jad_change && Media_Jar::isSupported($file.ext))}
            <strong><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}jad/{Http_Request::get('id')}">{$language.download} [JAD]</a></strong><br/>
        {/if}

        <input class="enter" size="50" type="url" value="{Helper::getUrl()}{$smarty.const.SEA_PUBLIC_DIRECTORY}{$file.path}"/><br/>
        {if $setup.send_email}
            <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}email/{Http_Request::get('id')}">{$language.send_a_link_to_email}</a><br/>
        {/if}
        {if $setup.abuse_change}
            <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}abuse/{Http_Request::get('id')}">{$language.complain_about_a_file}</a><br/>
        {/if}

        {if ($setup['comments_change'])}
            <strong><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}view_comments/{Http_Request::get('id')}">{$language.comments} [{$commentsCount}]</a></strong>


            {* администрирование *}
            {if $smarty.const.SEA_IS_ADMIN}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}apanel/apanel.php?id={$file.id}&amp;action=clearcomm" title="Очистить" class="no" onclick="return window.confirm('Очистить комментарии?');">[X]</a>
            {/if}

            <br/>
        {/if}
    </div>
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}{$directory.id}">{$language.go_to_the_category}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}settings/{Http_Request::get('id')}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}