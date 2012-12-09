{extends file='../sys/apanel/layout.tpl'}


{block content}
<form action="apanel.php?action=import" method="post">
    <div data-role="fieldcontain">
        <label for="topath">Сохранить в:</label>
        {html_options id='topath' name='topath' options=$dirs}
    </div>

    <div data-role="fieldcontain">
        <label for="files">Импортируемый файл # с каким именем сохранить:</label>
        <textarea cols="70" rows="10" name="files" id="files"></textarea>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}