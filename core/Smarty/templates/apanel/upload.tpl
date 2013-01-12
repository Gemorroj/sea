{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Загрузка файлов</h3>

<div class="ui-body ui-body-c">
    <form action="apanel.php?action=upload&amp;type=file" method="post" enctype="multipart/form-data" data-ajax="false">
        <div data-role="fieldcontain">
            <label for="topath">Сохранить в:</label>
            {html_options id='topath' name='topath' options=$dirs}
        </div>

        <div data-role="fieldcontain">
            <label for="userfile">Загрузка файлов (max {ini_get('upload_max_filesize')})</label>
            <input required="required" id="userfile" name="userfile[]" type="file" multiple="multiple" />
        </div>

        <input type="submit" value="Сохранить"/>
    </form>
</div>

<p></p>

<div class="ui-body ui-body-c">
    <form action="apanel.php?action=upload&amp;type=url" method="post">
        <div data-role="fieldcontain">
            <label for="topath">Сохранить в:</label>
            {html_options id='topath' name='topath' options=$dirs}
        </div>

        <div data-role="fieldcontain">
            <label for="files">Импортируемый файл # с каким именем сохранить (<em>можно указывать несколько файлов, каждый с новой строки</em>):</label>
            <textarea required="required" cols="70" rows="10" name="files" id="files" placeholder="http://example.com/file.gif # myfile.gif"></textarea>
        </div>

        <input type="submit" value="Сохранить"/>
    </form>
</div>
{/block}