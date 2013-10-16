{extends file='sys/layout.tpl'}


{* библиотека *}
{block content}
    {if $setup.lib_str}
        <pre>{$content|wordwrap:$setup.lib_str:"\n":false}</pre>
    {else}
        <pre>{$content}</pre>
    {/if}

    {* пагинация *}
    {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.SEA_PUBLIC_DIRECTORY}read/{Http_Request::get('id')}"}
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}view/{Http_Request::get('id')}">{$file.name}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}{$directory.id}">{$language.go_to_the_category}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}settings/{Http_Request::get('id')}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}