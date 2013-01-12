{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Импорт файлов из директории {$setup.importpath}</h3>
<em>
    В директорию {$setup.importpath}/files добавляется файл, например, {$setup.importpath}/files/MyVideo/video.mp4<br/>
    В директорию {$setup.importpath}/about добавляется описание к файлу, например, {$setup.importpath}/about/MyVideo/video.mp4.txt<br/>
    В директорию {$setup.importpath}/screen добавляется скриншот к файлу, например, {$setup.importpath}/screen/MyVideo/video.mp4.gif<br/>
    В директорию {$setup.importpath}/attach добавляются вложения к файлу, например, {$setup.importpath}/attach/MyVideo/video.mp4_video.3gp<br/>
    <br/>
    Названия файлов-вложений должны начинаться так же как и название главного файла (включая расширение) + подчеркивание + название вложения<br/>
    Названия файлов-скриншотов и файлов-описаний должны начинаться так же как и название главного файла (включая расширение) + расширение (txt - для описаний, gif, png или jpg для скриншотов)
</em>

<a href="apanel.php?action=import" data-role="button" data-icon="refresh">Импортировать</a>
{/block}