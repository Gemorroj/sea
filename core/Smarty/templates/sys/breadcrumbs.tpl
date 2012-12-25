<div class="iblock">
    <img src="{$smarty.const.DIRECTORY}style/img/load.png" alt=""/><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a> &#187;
    {foreach $breadcrumbs as $k => $v}
        {if $v@last}
            {$v}
        {else}
            <a href="{$smarty.const.DIRECTORY}{$k}">{$v}</a> &#187;
        {/if}
    {/foreach}
</div>