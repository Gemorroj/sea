{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}


{* статистика *}
<div class="row">
    {$language.all_files}: <strong>{$stat.all_files}</strong><br/>
    {$language.total_new_files}: <strong>{$stat.total_new_files}</strong><br/>
    {$language.total_volume}: <strong>{$stat.total_volume|sizeFormatExtended}</strong><br/>
    {$language.total_downloads}: <strong>{$stat.total_downloads}</strong><br/>
    {$language.maximum_online}: <strong>{$setup.online_max}</strong> ({$setup.online_max_time|strtotime|dateFormatExtended})<br/>
</div>


{* нижнее меню *}
<div class="iblock">
    - <a href="{$smarty.const.DIRECTORY}{$id}">{$language.back}</a><br/>
    - <a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a><br/>
    - <a href="{$setup.site_url}">{$language.home}</a><br/>
</div>


{* футер *}
{include file='footer.tpl'}