{if $setup.buy_change && $buy}
    <div class="iblock">
        {foreach from=$buy item=v}
            {$v nofilter}<br/>
        {/foreach}
    </div>
{/if}
{if $setup.service_change_advanced && $serviceBuy}
    <div class="iblock">
        {foreach from=$serviceBuy key=k item=v}
            <a href="http://{$k}">{$v}</a><br/>
        {/foreach}
    </div>
{/if}