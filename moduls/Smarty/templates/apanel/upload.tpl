{extends file='../sys/apanel/layout.tpl'}


{block content}
<form action="apanel.php?action=upload" method="post" enctype="multipart/form-data">
    <div data-role="fieldcontain">
        <label for="topath">Сохранить в:</label>
        {html_options id='topath' name='topath' options=$dirs}
    </div>

    <div data-role="fieldcontain">
        <label for="userfile">Upload файлов (max {ini_get('upload_max_filesize')})</label>
        <input id="userfile" name="userfile[]" type="file" multiple="multiple" />
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}