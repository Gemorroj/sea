{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}

{* реклама *}
{include file='sys/reklama.tpl'}



{* только если это главная *}
{if $id < 1}
    {* новости, поиск, топ *}
    <div class="iblock">
        {if $news}
            <a href="{$smarty.const.DIRECTORY}news.php">{$language.news}</a> ({$news.time|dateFormatExtended})<br/><span style="font-size:9px;">{$news.news}</span><br/>
        {/if}

        {if $setup.search_change}
            <a href="{$smarty.const.DIRECTORY}search.php">{$language.search}</a><br/>
        {/if}

        {if $setup.top_change}
            <a href="{$smarty.const.DIRECTORY}top.php">{$language.top20|replace:'%files%':$setup.top_num}</a><br/>
        {/if}
    </div>
{/if}


{* загрузки *}
<div class="mainzag">
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
</div>


{* пагинация *}
{paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}{$id}"}


{* нижнее меню *}
<div class="iblock">
    - <a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a><br/>
    - <a href="{$smarty.const.DIRECTORY}stat/{$id}">{$language.statistics}</a><br/>
    - <a href="{$smarty.const.DIRECTORY}table.php">{$language.orders}</a><br/>
    - <a href="{$smarty.const.DIRECTORY}exchanger.php">{$language.add_file}</a><br/>
    - <a href="{$setup.site_url}">{$language.home}</a><br/>
    {if $setup.online}
        - Online: <strong>{$online}</strong><br/>
    {/if}
</div>

{* баннеры *}
{include file='sys/banner.tpl'}

{* футер *}
{include file='footer.tpl'}