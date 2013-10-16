{extends file='sys/layout.tpl'}

{block content}
    {* загрузки *}
    {include file='sys/_files.tpl'}

    {* пагинация *}
    {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.SEA_PUBLIC_DIRECTORY}new"}
{/block}

{block footer}
    {* нижнее меню *}
    <ul class="iblock">
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}settings/{Http_Request::get('id')}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}