{extends file='../sys/apanel/layout.tpl'}


{block content}
    <h3>Очистка данных</h3>

    <div data-role="controlgroup">
        <a href="apanel.php?action=cleantrash" data-role="button" data-icon="delete">Очистка БД от мусора</a>
        <a href="apanel.php?action=cleannews" data-role="button" data-icon="delete">Очистка новостей</a>
        <a href="apanel.php?action=cleancomm_news" data-role="button" data-icon="delete">Очистка комментариев к новостям</a>
        <a href="apanel.php?action=cleancomm" data-role="button" data-icon="delete">Очистка комментариев к файлам</a>
        <a href="apanel.php?action=cleandb" data-role="button" data-icon="delete">Полная очистка БД</a>
    </div>
{/block}