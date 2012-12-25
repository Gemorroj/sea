{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Добавление скриншота</h3>

<form action="apanel.php?action=add_screen&amp;id={$smarty.get.id}" method="post" enctype="multipart/form-data" data-ajax="false">
    <div data-role="fieldcontain">
        <label for="screen">Скриншот:</label>
        <input accept="image/jpeg,image/png,image/gif" name="screen" type="file" required="required" id="screen" />
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}