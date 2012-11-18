{extends file='sys/layout.tpl'}

{block header}
    {* только если это главная *}
    {if $id < 1}
        {* новости, поиск, топ *}
        <div class="iblock">
            {if $news}
                <a href="{$smarty.const.DIRECTORY}news">{$language.news}</a> ({$news.time|dateFormatExtended})<br/>
                <span style="font-size:9px;">{$news.news|bbcode nofilter}</span><br/>
            {/if}

            {if $setup.search_change}
                <a href="{$smarty.const.DIRECTORY}search">{$language.search}</a><br/>
            {/if}

            {if $setup.top_change}
                <a href="{$smarty.const.DIRECTORY}top">{$language.top20|replace:'%files%':$setup.top_num}</a><br/>
            {/if}
        </div>
    {/if}
{/block}


{block content}
    {* загрузки *}
    {include file='sys/_files.tpl'}

    {* пагинация *}
    {paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}{$id}"}
{/block}


{block footer}
    {* нижнее меню *}
    <ul class="iblock">
        <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}stat/{$id}">{$language.statistics}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}table/{$id}">{$language.orders}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}exchanger/{$id}">{$language.add_file}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}