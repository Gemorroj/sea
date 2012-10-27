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
            <a href="{$smarty.const.DIRECTORY}search">{$language.search}</a><br/>
        {/if}

        {if $setup.top_change}
            <a href="{$smarty.const.DIRECTORY}top">{$language.top20|replace:'%files%':$setup.top_num}</a><br/>
        {/if}
    </div>
{/if}


{* загрузки *}
{include file='sys/_files.tpl'}


{* пагинация *}
{paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}{$id}"}


{* нижнее меню *}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}stat/{$id}">{$language.statistics}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}table/{$id}">{$language.orders}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}exchanger/{$id}">{$language.add_file}</a></li>
    <li><a href="{$setup.site_url}">{$language.home}</a></li>
</ul>

{* баннеры *}
{include file='sys/banner.tpl'}

{if $setup.online}
    Online: <strong>{$online}</strong><br/>
{/if}
{* футер *}
{include file='footer.tpl'}