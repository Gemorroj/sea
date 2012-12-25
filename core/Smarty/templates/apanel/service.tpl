{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Сервис</h3>
<p>Всего пользователей: {$users}</p>

<div class="ui-body ui-body-c">
<form action="apanel.php?action=service" method="post">
    <div data-role="fieldcontain">
        <label for="service_head">Ссылок вверху:</label>
        <input required="required" id="service_head" name="service_head" type="number" value="{$setup.service_head}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="service_foot">Ссылок внизу:</label>
        <input required="required" id="service_foot" name="service_foot" type="number" value="{$setup.service_foot}"/>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
</div>

<p></p>

<div class="ui-body ui-body-c">
<form action="apanel.php?action=service&amp;mode=del" method="post">
    <div data-role="fieldcontain">
        <label for="user">ID:</label>
        <input required="required" id="user" name="user" type="number" />
    </div>

    <input type="submit" value="Удалить"/>
</form>
    </div>
{/block}