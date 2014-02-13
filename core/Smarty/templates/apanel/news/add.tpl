{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Создание новости</h3>

<form action="apanel.php?action=add_news" method="post">
    {foreach $langpacks as $langpack}
        <div data-role="fieldcontain">
            <label for="new_{$langpack}">{$langpack}:</label>
            <textarea placeholder="Текст новости" required="required" cols="70" rows="10" name="new[{$langpack}]" id="new_{$langpack}"></textarea>
        </div>
    {/foreach}

    <input type="submit" value="Сохранить"/>
</form>
{/block}