{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}



{* загрузки *}
{include file='sys/_files.tpl'}


{* пагинация *}
{paginationExtended page=$page pages=$pages url="{$smarty.const.DIRECTORY}top"}


{* нижнее меню *}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="{$setup.site_url}">{$language.home}</a></li>
</ul>

{* футер *}
{include file='footer.tpl'}