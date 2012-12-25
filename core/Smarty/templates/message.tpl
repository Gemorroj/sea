{extends file='sys/layout.tpl'}


{* сообщение *}
{block content}
    <div class="row">
        {foreach $message as $mess}
            <span class="{if $isError}no{else}yes{/if}">{$mess}</span><br/>
        {/foreach}
    </div>
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="javascript:history.back();">{$language.back}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}