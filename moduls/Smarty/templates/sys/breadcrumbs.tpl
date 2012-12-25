<div class="iblock">
    <img src="{$smarty.const.DIRECTORY}style/img/load.png" alt=""/><a href="{$smarty.const.DIRECTORY}">{$language.downloads}</a> &#187;
    {foreach from=$breadcrumbs key=k item=v name=loop_breadcrumbs}
        {if $smarty.foreach.loop_breadcrumbs.last}
            {$v}
        {else}
            <a href="{$smarty.const.DIRECTORY}{$k}">{$v}</a> &#187;
        {/if}
    {/foreach}
</div>