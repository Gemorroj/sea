{extends file='sys/layout.tpl'}


{* новости *}
{block content}
    {if $paginatorConf.items < 1}
        <strong>[{$language.empty}]</strong>
    {else}
        {foreach $news as $v}
            <div class="{cycle values="row,row2"}">

                {if $smarty.const.SEA_IS_ADMIN}
                    <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}apanel/apanel.php?news={$v.id}&amp;action=del_news" title="Удалить" class="no" onclick="return window.confirm('Удалить новость?');">[X]</a>
                    <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}apanel/apanel.php?news={$v.id}&amp;action=edit_news" title="Изменить" class="yes">[E]</a>
                {/if}

                {$v.time|dateFormatExtended}<br/>
                <span class="comment">{$v.news|bbcode nofilter}</span><br/>
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}news_comments/{$v.id}">{$language.comments}</a> [{$v.count}]
            </div>
        {/foreach}
    {/if}

    {* пагинация *}
    {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.SEA_PUBLIC_DIRECTORY}news"}
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}settings/{Http_Request::get('id')}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}