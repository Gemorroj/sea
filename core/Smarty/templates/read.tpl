{extends file='sys/layout.tpl'}


{* библиотека *}
{block content}
    {if $setup.lib_str}
        <pre>{$content|wordwrap:$setup.lib_str:"\n":false}</pre>
    {else}
        <pre>{$content}</pre>
    {/if}

    {* пагинация *}
    {paginationExtended page=$paginatorConf.page pages=$paginatorConf.pages url="{$smarty.const.DIRECTORY}read/{$id}"}
{/block}


{block footer}
    <ul class="iblock">
        <li><a href="{$smarty.const.DIRECTORY}view/{$id}">{$file.name}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}{$directory.id}">{$language.go_to_the_category}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}settings/{$id}">{$language.settings}</a></li>
        <li><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a></li>
        <li><a href="http://{$setup.site_url}">{$language.home}</a></li>
    </ul>
{/block}