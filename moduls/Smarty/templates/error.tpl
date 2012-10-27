{* шапка *}
{include file='header.tpl'}

{* бредкрамбсы *}
{include file='sys/breadcrumbs.tpl'}

{* сообщение *}
<div class="iblock">
    <div class="row">
        <span class="no">{$message}</span>
    </div>
</div>

{* нижнее меню *}
<ul class="iblock">
    <li><a href="javascript:history.back();">{$language.back}</a></li>
    <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
    <li><a href="{$setup.site_url}">{$language.home}</a></li>
</ul>


{* футер *}
{include file='footer.tpl'}