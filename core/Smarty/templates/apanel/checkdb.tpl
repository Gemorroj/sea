{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>БД очищена</h3>

<ul data-role="listview" data-inset="true">
    <li>Удалено неверных записей <span class="ui-li-count">{$count}</span></li>
</ul>
{/block}