{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}


{* статистика *}
<div class="mblock">
    <div class="iblock">
        {$language.all_files}: <strong>{$stat.all_files}</strong><br/>
        {$language.total_new_files}: <strong>{$stat.total_new_files}</strong><br/>
        {$language.total_volume}: <strong>{$stat.total_volume|sizeFormatExtended}</strong><br/>
        {$language.total_downloads}: <strong>{$stat.total_downloads}</strong><br/>
        {$language.maximum_online}: <strong>{$setup.online_max}</strong> ({$setup.online_max_time|strtotime|dateFormatExtended})<br/>
    </div>
</div>


{* нижнее меню *}
<ul class="iblock">
    <li><a href="{$smarty.const.DIRECTORY}{$id}">{$language.back}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="{$setup.site_url}">{$language.home}</a></li>
</ul>


{* футер *}
{include file='footer.tpl'}