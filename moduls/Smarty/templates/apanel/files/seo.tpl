{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>SEO</h3>

<form action="apanel.php?action=seo&amp;id={$smarty.get.id}" method="post">
    <div data-role="fieldcontain">
        <label for="title">Title:</label>
        <input name="title" type="text" id="title" value="{$seo.title}" />
    </div>

    <div data-role="fieldcontain">
        <label for="keywords">Keywords:</label>
        <input name="keywords" type="text" id="keywords" value="{$seo.keywords}" />
    </div>

    <div data-role="fieldcontain">
        <label for="description">Description:</label>
        <input name="description" type="text" id="description" value="{$seo.description}" />
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}