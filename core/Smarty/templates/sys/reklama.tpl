{* реклама *}

{if $setup.buy_change && $buy}
    <div class="iblock">
        {foreach $buy as $v}
            {$v nofilter}<br/>
        {/foreach}
    </div>
{/if}
{if $setup.service_change_advanced && $serviceBuy}
    <div class="iblock">
        {foreach $serviceBuy as $k => $v}
            <a href="http://{$k}">{$v}</a><br/>
        {/foreach}
    </div>
{/if}