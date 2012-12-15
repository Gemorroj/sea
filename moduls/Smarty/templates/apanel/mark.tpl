{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Маркер картинок</h3>

<div class="ui-body ui-body-c">
    <form action="apanel.php?action=mark" method="post">
        <fieldset data-role="controlgroup" data-type="horizontal">
            {html_radios name="marker" options=[1=>'Вкл.',0=>'Выкл.',2=>'Только в общем просмотре'] selected=$setup.marker}
        </fieldset>

        <div class="containing-element">
            <label for="marker_where">Расположение:</label>
            <select name="marker_where" id="marker_where" data-role="slider">
                <option value="top" {if $setup.marker_where == 'top'}selected="selected"{/if}>вверху</option>
                <option value="foot" {if $setup.marker_where == 'foot'}selected="selected"{/if}>внизу</option>
            </select>
        </div>

        <input class="buttom" type="submit" value="Сохранить"/>
    </form>
</div>

<p></p>

<div class="ui-body ui-body-c">
    <form action="apanel.php?action=mark" method="post">
        <div data-role="fieldcontain">
            <label for="text">На картинки будет нанесена указанная надпись, удалить ее будет невозможно:</label>
            <input name="text" id="text" type="text"/>
        </div>

        <div class="containing-element">
            <label for="y">Расположение:</label>
            <select name="y" id="y" data-role="slider">
                <option value="top">вверху</option>
                <option value="foot">внизу</option>
            </select>
        </div>

        <div data-role="fieldcontain">
            <label for="size">Размер шрифта:</label>
            <input name="size" id="size" type="number" value="12"/>
        </div>

        <div data-role="fieldcontain">
            <label for="color">Цвет:</label>
            <input name="color" id="color" type="color" maxlength="7" value="#cccccc"/>
        </div>

        <input type="submit" value="Сохранить"/>
    </form>
</div>
{/block}