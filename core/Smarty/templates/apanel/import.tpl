{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Импорт</h3>

<form action="apanel.php?action=import" method="post">
    <div data-role="fieldcontain">
        <label for="topath">Сохранить в:</label>
        {html_options id='topath' name='topath' options=$dirs}
    </div>

    <div data-role="fieldcontain">
        <label for="files">Импортируемый файл # с каким именем сохранить:</label>
        <textarea required="required" cols="70" rows="10" name="files" id="files"></textarea>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}