{extends file='sys/layout.tpl'}


{* сервис *}
{block content}
    {assign var=act value=$smarty.get.act|default:''}

    {if !isset($smarty.session.id)}
        {if $act == 'registration'}
            {include file='service/registration.tpl'}
        {else}
            {include file='service/index.tpl'}
        {/if}
    {else}
        {include file='service/settings.tpl'}
    {/if}
{/block}



{block footer}
    <ul class="iblock">
        {if isset($smarty.session.id)}
            <li><a href="{$smarty.const.DIRECTORY}service?act=exit">{$language.exit}</a></li>
        {/if}
        <li><a href="{$smarty.const.DIRECTORY}{Http_Request::get('id')}">{$language.back}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}