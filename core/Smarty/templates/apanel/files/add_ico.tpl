{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Добавление иконки</h3>

<form action="apanel.php?action=add_ico&amp;id={$smarty.get.id}" method="post" enctype="multipart/form-data" data-ajax="false">
    <div data-role="fieldcontain">
        <label for="ico">Иконка:</label>
        <input accept="image/png" name="ico" type="file" required="required" id="ico" />
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}