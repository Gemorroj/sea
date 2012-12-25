{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Обменник</h3>

<form action="apanel.php?action=exchanger" method="post">
    <div data-role="fieldcontain">
        {html_checkboxes name="exchanger_notice" selected=$setup.exchanger_notice options=[1=>'Отправлять уведомления на Email о новых файлах']}
    </div>

    <div data-role="fieldcontain">
        {html_checkboxes name="exchanger_hidden" selected=$setup.exchanger_hidden options=[1=>'Делать загруженные файлы невидимыми']}
    </div>

    <div data-role="fieldcontain">
        <label for="exchanger_name">Регулярное выражение для проверки имени файла:</label>
        <input id="exchanger_name" name="exchanger_name" type="text" value="{$setup.exchanger_name}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="exchanger_extensions">Расширения файлов, разрешенные для загрузки, перечисленные через запятую:</label>
        <input id="exchanger_extensions" name="exchanger_extensions" type="text" value="{$setup.exchanger_extensions}"/>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}