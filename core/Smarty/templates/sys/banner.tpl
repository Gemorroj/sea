{* баннеры *}

{if $setup.buy_change && $banner}
    <div class="iblock">
        {foreach $banner as $v}
            {$v nofilter}<br/>
        {/foreach}
    </div>
{/if}
{if $setup.service_change_advanced && $serviceBanner}
    <div class="iblock">
        {foreach $serviceBanner as $k => $v}
            <a href="http://{$k}">{$v}</a><br/>
        {/foreach}
    </div>
{/if}
