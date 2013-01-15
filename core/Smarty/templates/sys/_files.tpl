{if $paginatorConf.items < 1}
    <strong>[{$language.empty}]</strong>
    {else}
    {* папки *}
    {foreach $directories as $dir}
        <div class="{cycle values="row,row2"}">

            <img src="{$dir.ico}" alt=""/>
                                                                                                            {* новые файлы в папке *}
            <strong><a href="{$smarty.const.DIRECTORY}{$dir.id}">{$dir.name}</a></strong> ({$dir.dir_count}{if ($setup.day_new && $dir.count)}<span class="yes"> +{$dir.count}</span>{/if})

            {* описание *}
            {if ($setup.desc && $dir.description)}
                <br/>{$dir.description|truncate:$setup.desc}
            {/if}


            {* администрирование *}
            {if $smarty.const.IS_ADMIN}
                <br/>
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=scan" title="Сканировать директорию">F</a>]
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=seo" title="SEO">K</a>]
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=rename" title="Переименовать директорию">R</a>]
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=about" title="Описание">O</a>]
                {if $dir.ico|substr:-10 == 'folder.png'}
                    [<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?action=del_ico&amp;id={$dir.id}" title="Удалить иконку">S</a>]
                {else}
                    [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?action=add_ico&amp;id={$dir.id}" title="Добавить иконку">S</a>]
                {/if}
                {if $dir.hidden}
                    [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=hidden&amp;hide=0" title="Сделать видимым">H</a>]
                {else}
                    [<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=hidden&amp;hide=1" title="Сделать невидимым">H</a>]
                {/if}
                {if $setup.delete_dir}
                    [<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?action=del_dir&amp;id={$dir.id}" title="Удалить директорию">D</a>]
                {/if}
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=priority&amp;to=up" title="Выше">Up</a>/<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$dir.id}&amp;action=priority&amp;to=down" title="Ниже">Down</a>]
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


            {* перенос строки, если был скриншот или превью *}
            {if ($prew && $f.pre) || ($setup.screen_change && $f.screen)}
                <br/>
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
                <br/>{$f.description|truncate:$setup.desc}
            {/if}


            {* администрирование *}
            {if $smarty.const.IS_ADMIN}
                <br/>
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=seo" title="SEO">K</a>]
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=rename" title="Переименовать файл">R</a>]
                [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=about" title="Описание">O</a>]
                {if $f.ext == 'mp3'}
                    [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?action=id3_file&amp;id={$f.id}" title="Idv1/Idv2 теги">M</a>]
                {/if}
                {if $f.screen}
                    [<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=del_screen" title="Удалить скриншот">S</a>]
                {else}
                    [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=add_screen" title="Добавить скриншот">S</a>]
                {/if}
                {if $f.hidden}
                    [<a class="yes" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=hidden&amp;hide=0" title="Сделать видимым">H</a>]
                {else}
                    [<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?id={$f.id}&amp;action=hidden&amp;hide=1" title="Сделать невидимым">H</a>]
                {/if}
                {if $setup.delete_file}
                    [<a class="no" href="{$smarty.const.DIRECTORY}apanel/apanel.php?action=del_file&amp;id={$f.id}" title="Удалить файл">D</a>]
                {/if}
            {/if}

        </div>
    {/foreach}
{/if}

{* администрирование *}
{if $smarty.const.IS_ADMIN}
    <div class="iblock"><code>[F] - сканировать директорию, [K] - SEO, [R] - переименование, [O] - описание, [M] - Idv1/Idv2 теги, [D] - удаление, [S] - скриншот, [H] - видимость, [Up/Down] - приоритет</code></div>
{/if}