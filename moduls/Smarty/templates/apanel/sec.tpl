{extends file='../sys/apanel/layout.tpl'}

{* TODO:доделать формы везде по аналогии с доками jquery modile (data-role="fieldcontain" и др) *}
{block content}
    <form action="apanel.php?action=sec" method="post">
        <div data-role="fieldcontain">
            <label>
                Пароль (если не хотите менять оставляем пустым):
                <input name="password" type="password" value=""/>
            </label>
        </div>

        <div data-role="fieldcontain">
            <label>
                Число неверных попыток ввода пароля до блокировки:
                <input name="countban" type="number" value="{$setup.countban}"/>
            </label>
        </div>

        <div data-role="fieldcontain">
            <label>
                Время блокировки (сек.):
                <input name="timeban" type="number" value="{$setup.timeban}"/>
            </label>
        </div>

        <div data-role="fieldcontain">
            <label><input type="checkbox" name="autologin" value="1" /> Автологин</label>
        </div>

        <label><input type="checkbox" name="delete_file" value="1" /> Функция удаления файлов</label>

        <label><input type="checkbox" name="delete_dir" value="1" /> Функция удаления каталогов</label>

        <div data-role="fieldcontain">
            <label for="pwd">
                Введите текущий пароль для подтверждения:
            </label>
            <input id="pwd" name="pwd" type="password" value=""/>
        </div>

        <input type="submit" value="Сохранить"/>
    </form>
{/block}