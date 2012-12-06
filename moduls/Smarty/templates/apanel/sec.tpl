{extends file='../sys/apanel/layout.tpl'}


{block content}
    <form action="apanel.php?action=sec" method="post">
        <div data-role="fieldcontain">
            <label for="password">Пароль (если не хотите менять оставляем пустым):</label>
            <input id="password" name="password" type="password" value=""/>
        </div>

        <div data-role="fieldcontain">
            <label for="countban"> Число неверных попыток ввода пароля до блокировки:</label>
            <input id="countban" name="countban" type="number" value="{$setup.countban}"/>
        </div>

        <div data-role="fieldcontain">
            <label for="timeban">Время блокировки (сек.):</label>
            <input id="timeban" name="timeban" type="number" value="{$setup.timeban}"/>
        </div>

        <div data-role="fieldcontain">
            <label for="autologin">Автологин</label>
            <input type="checkbox" id="autologin" name="autologin" value="1" />
        </div>

        <div data-role="fieldcontain">
            <label for="delete_file">Функция удаления файлов</label>
            <input type="checkbox" id="delete_file" name="delete_file" value="1" />
        </div>

        <div data-role="fieldcontain">
            <label for="delete_dir">Функция удаления каталогов</label>
            <input type="checkbox" id="delete_dir" name="delete_dir" value="1" />
        </div>

        <div data-role="fieldcontain">
            <label for="pwd">Введите текущий пароль для подтверждения:</label>
            <input id="pwd" name="pwd" type="password" value=""/>
        </div>

        <input type="submit" value="Сохранить"/>
    </form>
{/block}