{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Настройки</h3>

<form action="apanel.php?action=setting" method="post">
    <div data-role="fieldcontain">
        <label for="path">Директория с файлами:</label>
        <input required="required" type="text" id="path" name="path" value="{$setup.path}" />
    </div>

    <div data-role="fieldcontain">
        <label for="importpath">Директория для импорта:</label>
        <input required="required" type="text" id="importpath" name="importpath" value="{$setup.importpath}" />
    </div>

    <div data-role="fieldcontain">
        <label for="opath">Директория с описаниями:</label>
        <input required="required" id="opath" name="opath" type="text" value="{$setup.opath}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="apath">Директория с вложениями:</label>
        <input required="required" id="apath" name="apath" type="text" value="{$setup.apath}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="spath">Директория со скринами:</label>
        <input required="required" id="spath" name="spath" type="text" value="{$setup.spath}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="jpath">Директория c JAVA книгами:</label>
        <input required="required" id="jpath" name="jpath" type="text" value="{$setup.jpath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.jpath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="ipath">Директория c иконками из JAR файлов:</label>
        <input required="required" id="ipath" name="ipath" type="text" value="{$setup.ipath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.ipath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="zppath">Директория c картинками из ZIP архивов:</label>
        <input required="required" id="zppath" name="zppath" type="text" value="{$setup.zppath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.zppath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="zpath">Директория c ZIP книгами:</label>
        <input required="required" id="zpath" name="zpath" type="text" value="{$setup.zpath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.zpath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="tpath">Директория co скриншотами тем:</label>
        <input required="required" id="tpath" name="tpath" type="text" value="{$setup.tpath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.tpath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="ffmpegpath">Директория co скриншотами видео:</label>
        <input required="required" id="ffmpegpath" name="ffmpegpath" type="text" value="{$setup.ffmpegpath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.ffmpegpath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="picpath">Директория c превьюшками картинок:</label>
        <input required="required" id="picpath" name="picpath" type="text" value="{$setup.picpath}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.picpath}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="mp3path">Директория для нарезок:</label>
        <input required="required" id="mp3path" name="mp3path" type="text" value="{$setup.mp3path}"/>
        <a data-icon="delete" data-role="button" data-mini="true" data-inline="true" href="apanel.php?action=clean_cache&amp;dir={$setup.mp3path}">Очистить</a>
    </div>

    <div data-role="fieldcontain">
        <label for="limit">Лимит нарезок (Мб):</label>
        <input required="required" id="limit" name="limit" type="number" value="{$setup.limit}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="comments_view">Количество комментариев в описании файла:</label>
        <input required="required" id="comments_view" name="comments_view" type="number" value="{$setup.comments_view}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="onpage">Файлов на страницу по умолчанию:</label>
        {html_options id='onpage' name='onpage' options=[5=>5,10=>10,15=>15,20=>20,25=>25,30=>30] selected=$setup.onpage}
    </div>

    <div data-role="fieldcontain">
        <label for="css">Стиль по умолчанию:</label>
        {html_options id='css' name='css' values=$styles output=$styles selected=$setup.css}
    </div>

    <div data-role="fieldcontain">
        <label for="langpack">Язык по умолчанию:</label>
        {html_options id='langpack' name='langpack' values=$langpacks output=$langpacks selected=$setup.langpack}
    </div>

    <div data-role="fieldcontain">
        <label for="prev_size">Размер превьюшек (например, 40*40):</label>
        <input required="required" id="prev_size" pattern="[0-9]+\*[0-9]+" name="prev_size" type="text" value="{$setup.prev_size}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="view_size">Размеры картинок (например, 128*128,120*160,132*176,240*320):</label>
        <input required="required" id="view_size" pattern="[0-9\*,]+" name="view_size" type="text" value="{$setup.view_size}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="desc">Число отображаемых символов описания в общем просмотре файлов:</label>
        <input required="required" id="desc" name="desc" type="number" value="{$setup.desc}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="ffmpeg_frame">Номер фрейма для превью видео:</label>
        <input required="required" id="ffmpeg_frame" name="ffmpeg_frame" type="number" value="{$setup.ffmpeg_frame}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="ffmpeg_frames">Номера фреймов для превью видео в просмотре файла (например, 25,120,250):</label>
        <input required="required" id="ffmpeg_frames" pattern="[0-9,]+" name="ffmpeg_frames" type="text" value="{$setup.ffmpeg_frames}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="day_new">Время новых файлов (дней, 0 - выключено):</label>
        <input required="required" id="day_new" name="day_new" type="number" value="{$setup.day_new}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="online_time">Время онлайна (сек.):</label>
        <input required="required" id="online_time" name="online_time" type="number" value="{$setup.online_time}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="pagehand">Число страниц после которого появляется возможность ручного ввода страниц:</label>
        <input required="required" id="pagehand" name="pagehand" type="number" value="{$setup.pagehand}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="top_num">Число ТОП файлов:</label>
        <input required="required" id="top_num" name="top_num" type="number" value="{$setup.top_num}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="sort">Сортировка по умолчанию:</label>
        {html_options id='sort' name='sort' options=['name'=>'Имя','size'=>'Размер','date'=>'Дата','load'=>'Популярность','eval'=>'Рейтинг'] selected=$setup.sort}
    </div>

    <div data-role="fieldcontain">
        <label for="zag">Заголовок:</label>
        <input required="required" id="zag" name="zag" type="text" value="{$setup.zag}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="site_url">Главная сайта:</label>
        <input required="required" id="site_url" name="site_url" type="url" value="http://{$setup.site_url}"/>
    </div>

    <div data-role="fieldcontain">
        <label for="zakaz_email">E-mail админа:</label>
        <input required="required" id="zakaz_email" name="zakaz_email" type="email" value="{$setup.zakaz_email}"/>
    </div>

    <input type="submit" value="Сохранить"/>
</form>
{/block}