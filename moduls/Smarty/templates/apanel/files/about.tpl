{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Описание</h3>

<form action="apanel.php?action=about&amp;id={$smarty.get.id}" method="post">
    <div data-role="fieldcontain">
        <label for="about">Описание:</label>
        <textarea cols="70" rows="10" name="about" id="about">{$about}</textarea>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}