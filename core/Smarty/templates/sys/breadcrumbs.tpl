{* бредкрамбсы *}

{if $setup.ignore_index_breadcrumbs && $smarty.const.IS_INDEX === true}
    {* игнорируем бредкрамбсы на главной *}
{else}
    <div class="iblock">
        <img src="{$smarty.const.DIRECTORY}style/img/load.png" alt=""/><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a> &#187;
        {foreach Breadcrumbs::getBreadcrumbs() as $k => $v}
            {if $v@last}
                {$v}
            {else}
                <a href="{$smarty.const.DIRECTORY}{$k}">{$v}</a> &#187;
            {/if}
        {/foreach}
    </div>
{/if}