{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Библиотека</h3>

<form action="apanel.php?action=lib" method="post">
    <div data-role="fieldcontain">
        <label for="lib">Максимальное число символов на страницу:</label>
        <input required="required" id="lib" name="lib" type="number" value="{$setup.lib}" />
    </div>

    <div data-role="fieldcontain">
        <label for="lib_str">Максимальное число символов на одну строку:</label>
        <input required="required" id="lib_str" name="lib_str" type="number" value="{$setup.lib_str}" />
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}