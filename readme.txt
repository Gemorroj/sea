ЗАГРУЗ-ЦЕНТР Sea mod Gemorroj

Требования:
Apache 2, Nginx
PHP 5.2.1 или выше
PHP модули: PDO, Mbstring, CURL, Filter, SimpleXML, GD, FFmpeg (работает и без него, но не будет скриншотов видео)
MySQL 5.0.7 или выше

Установка:
1. Создаем новую базу данных
2. Вписываем конфигурационные данные в файл core/config.php
3. Ставим CHMOD 777 на директории files/ (и всем поддиректориям), cache/ (и всем поддиректориям) и core/cache/, core/tmp/, core/Smarty/templates_c/, core/Smarty/cache/
4. Запускаем файл install.php и вводим пароль для доступа в админку
5. Если установка прошла успешно, то удаляем файлы install.php и update.php
6. Заходим в браузере в админку - apanel/ и настраиваем загруз-центр

Обновление:
1. Заменяем все файлы загруз-центра новыми
2. Запускаем файл update.php
3. Если обновление прошло успешно, то удаляем файлы install.php и update.php
4. Заходим в браузере в админку - apanel/ и настраиваем загруз-центр


Дополнительная информация:
Если Вы добавляете файлы через FTP, то не забудьте вручную проставить всем поддиректориям в директории files/ права 777
Чтобы добавить описание следует в папке about/ создать такую же структуру директорий, как и в директории files/, создать файл с таким же именем, как описываемый + расширение ".txt". Например, есть файл files/xxx/photo.jpg, в таком случае его описание должно находиться в файле cache/about/xxx/photo.jpg.txt
То же самое касается и скриншотов, они должны находиться в папке screen/ с такой же структурой, как и в папке files/. Возможные расширения скриншотов - ".jpg", ".png" или ".gif". Например, есть файл files/xxx/photo.jpg, в таком случае его скриншот должен находиться в файле cache/screen/xxx/photo.jpg.jpg
Текст должен быть в кодировке UTF-8 без BOM сигнатуры. Так же следите за xHTML разметкой, т.к. описание обрабатывается учетом ббкода
Маркер наносимый на картинки находится в файле core/resources/marker.png, замените его на свой
Чтобы ЗЦ был был на русском, английском или других доступных языках, следует перейти по ссылке http://example.com/?langpack=russian или http://example.com/?langpack=english и т.д.
Автологин в админку выглядит так http://example.com/apanel/?p=пароль
Если вы хотите изменить дизайн зц, то добавьте свой css файл со стилем в директорию style и выберите в админке свой дизайн по умолчанию


Доступные ББкоды:
[url][/url]
[url=http://wapinet.ru]сайт[/url]
[br]
[i][/i]
[b][/b]
[u][/u]
[big][/big]
[small][/small]
[code][/code]
[red][/red]
[yellow][/yellow]
[green][/green]
[blue][/blue]
[white][/white]
[color=#000000][/color]
[size=8][/size]
[img]http://wapinet.ru/img.gif[/img]


Чтобы вывести общее количество файлов следует выполнить следующий код

require_once 'path_to_sea/core/classes/Db/Mysql.php';
Db_Mysql::init(array(
    'host' => 'localhost',
    'dbname' => 'sea',
    'username' => 'root',
    'password' => '',
));
$db = Db_Mysql::getInstance();
echo $db->query('SELECT COUNT(1) FROM `files` WHERE `dir` = "0" AND `hidden` = "0"')->fetchColumn();

то же самое но с ограничением на какую-либо папку
echo $db->query('SELECT COUNT(1) FROM `files` WHERE `dir` = "0" AND `hidden` = "0" AND `infolder` LIKE("files/dir/%")')->fetchColumn();
где "files/dir/%" - маска поиска.


По вопросам приобретения зц, обращайтесь на email: wapinet@mail.ru
