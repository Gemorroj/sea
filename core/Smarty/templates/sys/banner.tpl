{if $setup.buy_change && $banner}
    <div class="iblock">
        {foreach from=$banner item=v}
            {$v nofilter}<br/>
        {/foreach}
    </div>
{/if}
{if $setup.service_change_advanced && $serviceBanner}
    <div class="iblock">
        {foreach from=$serviceBanner key=k item=v}
            <a href="http://{$k}">{$v}</a><br/>
        {/foreach}
    </div>
{/if}