{extends file='sys/layout.tpl'}


{* новости *}
{block content}
    {if $paginatorConf.items < 1}
        <strong>[{$language.empty}]</strong>
    {else}
        {foreach $news as $v}
            <div class="{cycle values="row,row2"}">

                {if $smarty.const.IS_ADMIN}
                    <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?news={$v.id}&amp;action=del_news" title="Удалить" class="no" onclick="return window.confirm('Удалить новость?');">[X]</a>
                    <a href="{$smarty.const.DIRECTORY}apanel/apanel.php?news={$v.id}&amp;action=edit_news" title="Изменить" class="yes">[E]</a>
                {/if}

                {$v.time|dateFormatExtended}<br/>
                <span style="font-size:9px;">{$v.news|bbcode nofilter}</span><br/>
                <a href="{$smarty.const.DIRECTORY}news_comments/{$v.id}">{$language.comments}</a> [{$v.count}]
            </div>
        {/foreach}
    {/if}

    {* пагинация *}
    {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.DIRECTORY}news"}
{/block}


{block footer}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
</ul>
{/block}