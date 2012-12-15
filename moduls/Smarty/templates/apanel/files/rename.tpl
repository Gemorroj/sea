{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Переименование</h3>

<form action="apanel.php?action=rename&amp;id={$smarty.get.id}" method="post">
    {foreach $langpacks as $langpack}
        <div data-role="fieldcontain">
            <label for="new_{$langpack}">{$langpack}:</label>
            <input type="text" required="required" name="new[{$langpack}]" id="new_{$langpack}" value="{if $langpack == 'english'}{$info.name}{else}{$info.{$langpack|truncate:3:''|cat:'_name'}}{/if}" />
        </div>
    {/foreach}

    <input type="submit" value="Сохранить"/>
</form>
{/block}