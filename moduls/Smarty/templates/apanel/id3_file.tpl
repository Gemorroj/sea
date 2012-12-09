{extends file='../sys/apanel/layout.tpl'}


{block content}
<h4>Модуль задаст MP3 файлу указанные теги. Если поле пустое, то тег изменяться не будет</h4>

<form action="apanel.php?action=id3" method="post">
    <div data-role="fieldcontain">
        <label for="name">Название:</label>
        <input name="name" id="name" type="text" value="{$name}" />
    </div>
    <div data-role="fieldcontain">
        <label for="artists">Артист:</label>
        <input name="artists" id="artists" type="text" value="{$artists}" />
    </div>
    <div data-role="fieldcontain">
        <label for="album">Альбом:</label>
        <input name="album" id="album" type="text" value="{$album}" />
    </div>
    <div data-role="fieldcontain">
        <label for="year">Год:</label>
        <input name="year" id="year" type="number" value="{$year}" />
    </div>
    <div data-role="fieldcontain">
        <label for="track">Трек:</label>
        <input name="track" id="track" type="number" value="{$track}" />
    </div>
    <div data-role="fieldcontain">
        <label for="genre">Жанр:</label>
        {html_options id='genre' name='genre' values=$genres output=$genres selected=$genre}
    </div>
    <div data-role="fieldcontain">
        <label for="comment">Комментарии:</label>
        <textarea id="comment" name="comment" rows="2" cols="32">{$comment}</textarea>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}