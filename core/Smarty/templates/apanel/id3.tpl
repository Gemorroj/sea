{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Модуль задаст всем MP3 файлам Idv1/Idv2 теги. Если поле пустое, то тег изменяться не будет</h3>

<form action="apanel.php?action=id3" method="post">
    <div data-role="fieldcontain">
        <label for="name">Название:</label>
        <input name="name" id="name" type="text" />
    </div>
    <div data-role="fieldcontain">
        <label for="artists">Артист:</label>
        <input name="artists" id="artists" type="text" />
    </div>
    <div data-role="fieldcontain">
        <label for="album">Альбом:</label>
        <input name="album" id="album" type="text" />
    </div>
    <div data-role="fieldcontain">
        <label for="year">Год:</label>
        <input name="year" id="year" type="number" />
    </div>
    <div data-role="fieldcontain">
        <label for="track">Трек:</label>
        <input name="track" id="track" type="number" />
    </div>
    <div data-role="fieldcontain">
        <label for="genre">Жанр:</label>
        {html_options id='genre' name='genre' values=$genres output=$genres}
    </div>
    <div data-role="fieldcontain">
        <label for="comment">Комментарии:</label>
        <textarea id="comment" name="comment" rows="2" cols="32"></textarea>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}