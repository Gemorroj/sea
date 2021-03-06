{extends file='sys/layout.tpl'}

{block header}
    {* только если это главная *}
    {if $smarty.const.SEA_IS_INDEX}
        {* новости, поиск, топ *}
        <div class="iblock">
            {if $news}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}news">{$language.news}</a> ({$news.time|dateFormatExtended})<br/>
                <span class="comment">{$news.news|bbcode nofilter}</span><br/>
            {/if}

            {if $setup.search_change}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}search">{$language.search}</a><br/>
            {/if}

            {if $setup.top_change}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}top">{$language.top20|replace:'%files%':$setup.top_num}</a><br/>
            {/if}

            {if $setup.new_change}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}new">{$language.new_files}</a><br/>
            {/if}
        </div>
    {/if}
{/block}


{block content}
    {* загрузки *}
    {include file='sys/_files.tpl'}

    {* пагинация *}
    {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.SEA_PUBLIC_DIRECTORY}{Http_Request::get('id')|default:'0'}"}
{/block}


{block footer}
    {* нижнее меню *}
    <ul class="iblock">
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}settings/{Http_Request::get('id')}">{$language.settings}</a></li>
        {if $setup.stat_change}
            <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}stat/{Http_Request::get('id')}">{$language.statistics}</a></li>
        {/if}
        {if $setup.zakaz_change}
            <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}table/{Http_Request::get('id')}">{$language.orders}</a></li>
        {/if}
        {if $setup.exchanger_change}
            <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}exchanger/{Http_Request::get('id')}">{$language.add_file}</a></li>
        {/if}
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}