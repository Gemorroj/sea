{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Лог авторизаций</h3>

<ul data-role="listview" data-filter="true" data-inset="true">
    <li data-role="list-divider" role="heading">Лог последних 50 посещений админки ([User-Agent] [IP] [Time]):</li>
    {foreach $logs as $log}
        <li>[{$log.ua}] [{$log.ip}] [{$log.time|dateFormatExtended}]</li>
    {/foreach}
</ul>
{/block}