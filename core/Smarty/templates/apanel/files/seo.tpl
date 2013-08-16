{extends file='../../sys/apanel/layout.tpl'}


{block content}
<h3>Поисковая оптимизация - SEO</h3>

<form action="apanel.php?action=seo&amp;id={$smarty.get.id}" method="post">
    <div data-role="fieldcontain">
        <label for="title">Заголовок (title):</label>
        <input name="title" type="text" id="title" value="{Seo::getTitle()}" />
    </div>

    <div data-role="fieldcontain">
        <label for="keywords">Ключевые слова (keywords):</label>
        <input name="keywords" type="text" id="keywords" value="{Seo::getKeywords()}" />
    </div>

    <div data-role="fieldcontain">
        <label for="description">Описание (description):</label>
        <input name="description" type="text" id="description" value="{Seo::getDescription()}" />
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}