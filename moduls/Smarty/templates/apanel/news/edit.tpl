{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Изменение новости</h3>

<form action="apanel.php?action=edit_news&amp;news={$smarty.get.news}" method="post">
    {foreach $langpacks as $langpack}
        <div data-role="fieldcontain">
            <label for="new_{$langpack}">{$langpack}:</label>
            <textarea required="required" cols="70" rows="10" name="new[{$langpack}]" id="new_{$langpack}">{if $langpack == 'english'}{$news.news}{else}{$news.{$langpack|truncate:3:''|cat:'_news'}}{/if}</textarea>
        </div>
    {/foreach}

    <input type="submit" value="Сохранить"/>
</form>
{/block}