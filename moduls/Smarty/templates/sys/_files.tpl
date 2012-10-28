{if $allItemsInDir < 1}
    <strong>[{$language.empty}]</strong>
    {else}
{* папки *}
    {foreach $directories as $dir}
        <div class="{cycle values="row,row2"}">

            <img src="{$dir.ico}" alt=""/>
            <strong><a href="{$smarty.const.DIRECTORY}{$dir.id}">{$dir.name}</a></strong> ({$dir.dir_count})

        {* новые файлы в папке *}
            {if ($setup.day_new && $dir.count)}
                (<span class="yes">+{$dir.count}</span>)
            {/if}

        {* описание *}
            {if ($setup.desc && $dir.description)}
                <br/>{$dir.description|truncate:$setup.desc}
            {/if}

        </div>
    {/foreach}
{* файлы *}
    {foreach $files as $f}
        <div class="{cycle values="row,row2"}">

        {* превью *}
            {if $prew && $f.pre}
                {if $f.ext == 'swf'}
                    <object style="width: 128px; height: 128px;"><param name="movie" value="{$f.pre}"><embed src="{$f.pre}" style="width: 128px; height: 128px;"></embed></param></object>
                    {else}
                {* gif,png,jpg *}
                    <img style="margin: 1px;" src="{$f.pre}" alt=""/>
                {/if}
            {/if}

        {* скриншот *}
            {if $setup.screen_change && $f.screen}
                <img style="margin: 1px;" src="{$f.screen}" alt=""/>
            {/if}

            <img src="{$f.ico}" alt=""/>
            <strong><a href="{$smarty.const.DIRECTORY}view/{$f.id}">{$f.name}</a></strong>

        {* расширение *}
            {if $setup.ext}
                ({$f.ext})
            {/if}
            {$f.size|sizeFormatExtended}

            {if $sort == 'load'}
                (<span class="yes">{$f.loads}</span>)
                {elseif $sort == 'data'}
                ({$f.timeupload|dateFormatExtended});
                {elseif $setup.eval_change && $sort == 'eval'}
                (<span class="yes">{$f.yes}</span>/<span class="no">{$f.no}</span>)
            {/if}

            [<a class="yes" href="{$smarty.const.DIRECTORY}load/{$f.id}">L</a>]


        {* новизна файла *}
            {if ($setup.day_new && ((86400 * $setup.day_new) + $f.timeupload) >= $smarty.server.REQUEST_TIME)}
                <span class="yes">{$language.new}</span>
            {/if}


        {* описание *}
            {if ($setup.desc && $f.description)}
                <br/>{$dir.description|truncate:$setup.desc}
            {/if}
        </div>
    {/foreach}
{/if}