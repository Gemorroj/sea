{extends file='sys/layout.tpl'}


{* сообщение *}
{block content}
    <div class="row">
        <span class="no">{$message}</span>
    </div>
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="javascript:history.back();">{$language.back}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}