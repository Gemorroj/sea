{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>БД обновлена</h3>
<ul data-role="listview" data-inset="true">
    <li>Просканировано директорий <span class="ui-li-count">{$data.folders}</span></li>
    <li>Просканировано файлов <span class="ui-li-count">{$data.files}</span></li>
</ul>
{/block}