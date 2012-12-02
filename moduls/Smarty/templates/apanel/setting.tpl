{extends file='../sys/apanel/layout.tpl'}


{block content}
<form action="apanel.php?action=setting" method="post">
    <label>Папка с файлами:
        <input type="text" name="path" value="{$setup.path}" />
    </label>

    <label>
        Папка с описаниями:
        <input name="opath" type="text" value="{$setup.opath}"/>
    </label>
    <label>
        Папка с вложениями:
        <input name="apath" type="text" value="{$setup.apath}"/>
    </label>
    <label>
        Папка со скринами:
        <input name="spath" type="text" value="{$setup.spath}"/>
    </label>

    <label>
        Папка c JAVA книгами: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.jpath}">Очистить</a>
        <input name="jpath" type="text" value="{$setup.jpath}"/>
    </label>

    <label>
        Папка c иконками из JAR файлов: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.ipath}">Очистить</a>
        <input name="ipath" type="text" value="{$setup.ipath}"/>
    </label>

    <label>
        Папка c картинками из ZIP архивов: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.zppath}">Очистить</a>
        <input name="zppath" type="text" value="{$setup.zppath}"/>
    </label>

    <label>
        Папка c ZIP книгами: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.zpath}">Очистить</a>
        <input name="zpath" type="text" value="{$setup.zpath}"/>
    </label>

    <label>
        Папка co скриншотами тем: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.tpath}">Очистить</a>
        <input name="tpath" type="text" value="{$setup.tpath}"/>
    </label>

    <label>
        Папка co скриншотами видео: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.ffmpegpath}">Очистить</a>
        <input name="ffmpegpath" type="text" value="{$setup.ffmpegpath}"/>
    </label>

    <label>
        Папка c превьюшками картинок: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.picpath}">Очистить</a>
        <input name="picpath" type="text" value="{$setup.picpath}"/>
    </label>

    <label>
        Папка для нарезок: <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.mp3path}">Очистить</a>
        <input name="mp3path" type="text" value="{$setup.mp3path}"/>
    </label>

    <label>
        Лимит нарезок (Мб):
        <input name="limit" type="number" value="{$setup.limit}"/>
    </label>

    <label>
        Количество комментариев в описании файла:
        <input name="comments_view" type="number" value="{$setup.comments_view}"/>
    </label>

    <label>
        Файлов на страницу по умолчанию:
        {html_options name=onpage options=[5=>5,10=>10,15=>15,20=>20,25=>25,30=>30] selected=$setup.onpage}
    </label>

    <label>
        Стиль по умолчанию:
        {html_options name=css values=$styles output=$styles selected=$setup.css}
    </label>

    <label>
        Язык по умолчанию:
        {html_options name=langpack values=$langpacks output=$langpacks selected=$setup.langpack}
    </label>

    <label>
        Размер превьюшек (например, 40*40):
        <input pattern="[0-9]+\*[0-9]+" name="prev_size" type="text" value="{$setup.prev_size}"/>
    </label>

    <label>
        Размеры картинок (например, 128*128,120*160,132*176,240*320):
        <input pattern="[0-9\*,]+" name="view_size" type="text" value="{$setup.view_size}"/>
    </label>

    <label>
        Число отображаемых символов описания в общем просмотре файлов:
        <input name="desc" type="number" value="{$setup.desc}"/>
    </label>

    <label>
        Номер фрейма для превью видео:
        <input name="ffmpeg_frame" type="number" value="{$setup.ffmpeg_frame}"/>
    </label>

    <label>
        Номера фреймов для превью видео в просмотре файла (например, 25,120,250):
        <input pattern="[0-9,]+" name="ffmpeg_frames" type="text" value="{$setup.ffmpeg_frames}"/>
    </label>

    <label>
        Время новых файлов (дней, 0 - выключено):
        <input name="day_new" type="number" value="{$setup.day_new}"/>
    </label>

    <label>
        Время онлайна (сек.):
        <input name="online_time" type="number" value="{$setup.online_time}"/>
    </label>

    <label>
        Число страниц после которого появляется возможность ручного ввода страниц:
        <input name="pagehand" type="number" value="{$setup.pagehand}"/>
    </label>

    <label>
        Число ТОП файлов:
        <input name="top_num" type="number" value="{$setup.top_num}"/>
    </label>

    <label>
        Сортировка по умолчанию:
        {html_options name=sort options=['name'=>'Имя','size'=>'Размер','data'=>'Дата','load'=>'Популярность','eval'=>'Рейтинг'] selected=$setup.sort}
    </label>

    <label>
        Заголовок:
        <input name="zag" type="text" value="{$setup.zag}"/>
    </label>

    <label>
        Главная сайта:
        <input name="site_url" type="url" value="http://{$setup.site_url}"/>
    </label>

    <label>
        E-mail админа:
        <input name="zakaz_email" type="email" value="{$setup.zakaz_email}"/>
    </label>

    <input type="submit" value="Сохранить"/>
</form>
{/block}