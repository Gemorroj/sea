{extends file='sys/layout.tpl'}

{block content}
    {* загрузки *}
    {include file='sys/_files.tpl'}

    {* пагинация *}
    {paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}new"}
{/block}

{block footer}
    {* нижнее меню *}
    <ul class="iblock">
        <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}