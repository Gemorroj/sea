{* бредкрамбсы *}

{if $setup.ignore_index_breadcrumbs && $smarty.const.SEA_IS_INDEX === true}
    {* игнорируем бредкрамбсы на главной *}
{else}
    <div class="iblock">
        <img src="{$smarty.const.SEA_PUBLIC_DIRECTORY}style/img/load.png" alt=""/><a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}">{$language.downloads}</a> &#187;
        {foreach Breadcrumbs::getBreadcrumbs() as $k => $v}
            {if $v@last}
                {$v}
            {else}
                <a href="{$smarty.const.SEA_PUBLIC_DIRECTORY}{$k}">{$v}</a> &#187;
            {/if}
        {/foreach}
    </div>
{/if}