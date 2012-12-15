{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Безопасность</h3>

<form action="apanel.php?action=sec" method="post">
    <div data-role="fieldcontain">
        <label for="password">Пароль (если не хотите менять оставляем пустым):</label>
        <input required="required" id="password" name="password" type="password" value=""/>
    </div>

    <div data-role="fieldcontain">
        <label for="countban"> Число неверных попыток ввода пароля до блокировки:</label>
        <input required="required" id="countban" name="countban" type="number" value="{$setup.countban}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="timeban">Время блокировки (сек.):</label>
        <input required="required" id="timeban" name="timeban" type="number" value="{$setup.timeban}"/>
    </div>

    <div data-role="fieldcontain">
        {html_checkboxes name="autologin" selected=$setup.autologin options=[1=>'Автологин']}
    </div>

    <div data-role="fieldcontain">
        {html_checkboxes name="delete_file" selected=$setup.delete_file options=[1=>'Функция удаления файлов']}
    </div>

    <div data-role="fieldcontain">
        {html_checkboxes name="delete_dir" selected=$setup.delete_dir options=[1=>'Функция удаления каталогов']}
    </div>

    <div data-role="fieldcontain">
        <label for="pwd">Введите текущий пароль для подтверждения:</label>
        <input required="required" id="pwd" name="pwd" type="password" value=""/>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}