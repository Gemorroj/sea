<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" type="text/css" href="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css"/>
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <title>Admin panel</title>
    <style type="text/css">
        .content-primary {
            width: 74%;
            float: right;
            margin: 0;
            padding: 0;
        }
        .content-secondary {
            width: 25%;
            float: left;
            position: relative;
            margin: 0;
            padding: 0;
            text-align: left;
        }
        .content-secondary .ui-listview {
            margin: 0;
        }
    </style>
</head>
<body>
<div data-role="page">

    <div data-role="content">
        <div class="content-primary">
            {if isset($error) && $error}
                <div class="ui-bar ui-bar-b">
                    <h3>Warning!</h3>
                    <p>{$error|escape|nl2br nofilter}</p>
                </div><br/>
            {/if}
            {if isset($message) && $message}
                <div class="ui-bar ui-bar-a">
                    <h3>Success!</h3>
                    <p>{$message|escape|nl2br nofilter}</p>
                </div><br/>
            {/if}

            {block content}{/block}
        </div>

        <div class="content-secondary">
            {block header}
                <ul data-role="listview" data-inset="true">
                    <li data-role="list-divider">Admin Panel</li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'add_news'}data-theme="b"{/if}><a href="apanel.php?action=add_news">Создать новость</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'add_dir'}data-theme="b"{/if}><a href="apanel.php?action=add_dir">Создать директорию</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'upload'}data-theme="b"{/if}><a href="apanel.php?action=upload">Загрузка файлов</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'import'}data-theme="b"{/if}><a href="apanel.php?action=import">Импорт файлов</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'setting'}data-theme="b"{/if}><a href="apanel.php?action=setting">Настройки</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'modules'}data-theme="b"{/if}><a href="apanel.php?action=modules">Модули</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'service'}data-theme="b"{/if}><a href="apanel.php?action=service">Сервис</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'exchanger'}data-theme="b"{/if}><a href="apanel.php?action=exchanger">Обменник</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'lib'}data-theme="b"{/if}><a href="apanel.php?action=lib">Библиотека</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'sec'}data-theme="b"{/if}><a href="apanel.php?action=sec">Безопасность</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'log'}data-theme="b"{/if}><a href="apanel.php?action=log">Лог авторизаций</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'buy'}data-theme="b"{/if}><a href="apanel.php?action=buy">Реклама</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'id3'}data-theme="b"{/if}><a href="apanel.php?action=id3">MP3 теги</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'mark'}data-theme="b"{/if}><a href="apanel.php?action=mark">Маркер картинок</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'optim'}data-theme="b"{/if}><a href="apanel.php?action=optim">Оптимизация БД</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action == 'scan'}data-theme="b"{/if}><a href="apanel.php?action=scan">Полное обновление БД</a></li>
                    <li {if isset($smarty.get.action) && $smarty.get.action|truncate:5:'' == 'clean'}data-theme="b"{/if}><a href="apanel.php?action=clean">Очистка</a></li>
                </ul>
            {/block}
        </div>
    </div>

</div>
</body>
</html>