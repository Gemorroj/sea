{extends file='../sys/apanel/layout.tpl'}


{block content}
<h3>Модули</h3>

<form action="apanel.php?action=modules" method="post">
    <fieldset data-role="controlgroup">
        {html_checkboxes name="comments_change" selected=$setup.comments_change options=[1=>'Комментарии']}
        {html_checkboxes name="comments_captcha" selected=$setup.comments_captcha options=[1=>'Капча к комментариям']}
        {html_checkboxes name="eval_change" selected=$setup.eval_change options=[1=>'Рейтинг']}
        {html_checkboxes name="jad_change" selected=$setup.jad_change options=[1=>'Генератор Jad']}
        {html_checkboxes name="cut_change" selected=$setup.cut_change options=[1=>'Нарезчик Mp3']}
        {html_checkboxes name="audio_player_change" selected=$setup.audio_player_change options=[1=>'Flash плеер Mp3']}
        {html_checkboxes name="video_player_change" selected=$setup.video_player_change options=[1=>'Flash плеер Flv/Mp4']}
        {html_checkboxes name="zip_change" selected=$setup.zip_change options=[1=>'Просмотр архивов']}
        {html_checkboxes name="zakaz_change" selected=$setup.zakaz_change options=[1=>'Стол заказов']}
        {html_checkboxes name="buy_change" selected=$setup.buy_change options=[1=>'Рекламный блок']}
        {html_checkboxes name="onpage_change" selected=$setup.onpage_change options=[1=>'Форма выбора кол-ва файлов на страницу']}
        {html_checkboxes name="preview_change" selected=$setup.preview_change options=[1=>'Форма выбора предпросмотра']}
        {html_checkboxes name="top_change" selected=$setup.top_change options=[1=>'ТОП']}
        {html_checkboxes name="new_change" selected=$setup.new_change options=[1=>'Новые файлы']}
        {html_checkboxes name="stat_change" selected=$setup.stat_change options=[1=>'Статистика']}
        {html_checkboxes name="pagehand_change" selected=$setup.pagehand_change options=[1=>'Ручной ввод страниц']}
        {html_checkboxes name="search_change" selected=$setup.search_change options=[1=>'Поиск файлов']}
        {html_checkboxes name="lib_change" selected=$setup.lib_change options=[1=>'Библиотека']}
        {html_checkboxes name="screen_change" selected=$setup.screen_change options=[1=>'Уменьшенные скриншоты в общем просмотре']}
        {html_checkboxes name="screen_file_change" selected=$setup.screen_file_change options=[1=>'Уменьшенные скриншоты в просмотре файла']}
        {html_checkboxes name="swf_change" selected=$setup.swf_change options=[1=>'Swf превью в общем просмотре']}
        {html_checkboxes name="swf_file_change" selected=$setup.swf_file_change options=[1=>'Swf превью в просмотре файла']}
        {html_checkboxes name="jar_change" selected=$setup.jar_change options=[1=>'Иконки Jar файлов в общем просмотре']}
        {html_checkboxes name="jar_file_change" selected=$setup.jar_file_change options=[1=>'Иконки Jar файлов в просмотре файла']}
        {html_checkboxes name="anim_change" selected=$setup.anim_change options=[1=>'Поддержка анимации']}
        {html_checkboxes name="prew" selected=$setup.prew options=[1=>'Предпросмотр по умолчанию']}
        {html_checkboxes name="lib_desc" selected=$setup.lib_desc options=[1=>'Брать первую строку из txt файла как описание']}
        {html_checkboxes name="ext" selected=$setup.ext options=[1=>'Показ расширения']}
        {html_checkboxes name="prev_next" selected=$setup.prev_next options=[1=>'Предыдущий/следующий файлы']}
        {html_checkboxes name="style_change" selected=$setup.style_change options=[1=>'Смена стилей']}
        {html_checkboxes name="service_change" selected=$setup.service_change options=[1=>'Сервисное использование']}
        {html_checkboxes name="service_change_advanced" selected=$setup.service_change_advanced options=[1=>'Расширенное сервисное использование']}
        {html_checkboxes name="abuse_change" selected=$setup.abuse_change options=[1=>'Жалобы']}
        {html_checkboxes name="exchanger_change" selected=$setup.exchanger_change options=[1=>'Обменник']}
        {html_checkboxes name="send_email" selected=$setup.send_email options=[1=>'Отправка ссылки на Email']}
    </fieldset>

    <input type="submit" value="Сохранить"/>
</form>
{/block}