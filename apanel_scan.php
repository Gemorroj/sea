<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#             	 Автор  :  Sea                        #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#   		По всем вопросам пишите в ICQ.            #
#-----------------------------------------------------#

// mod Gemorroj

//error_reporting(0);

@set_time_limit(99999);
ini_set('max_execution_time', 99999);
ignore_user_abort(true);
ob_implicit_flush(1);
//clearstatcache();
ini_set('memory_limit', '256M');

require 'moduls/config.php';
require 'moduls/header.php';


$HeadTime = microtime(true);

if ($_SESSION['autorise'] != $setup['password'] || $_SESSION['ipu'] != $_SERVER['REMOTE_ADDR']) {
    error($setup['hackmess']);
}



// скриншоты
chmod($setup['spath'], 0777);
// описания
chmod($setup['opath'], 0777);
// вложения
chmod($setup['apath'], 0777);

$scan = isset($_GET['scan']) ? $_GET['scan'] : $setup['path'];

echo '<div style="font-size: x-small;">';
$data = scaner($scan);
echo '</div>';


if ($data['errors']) {
    echo '<div class="no">' . implode('<br/>', $data['errors']) . '<br/></div>';
}

echo '<div class="mainzag">БД обновлена!<br/></div>
Просканировано директорий: ' . $data['folders'] . '<br/>
Просканировано файлов: ' . $data['files'] . '<br/>
<div class="mainzag" style="color:#b00;">
Внимание! Теперь следует пересчитать количество файлов в папках<br/>
Для продолжения нажмите на <a class="yes" href="apanel_count.php">ЭТУ</a> ссылку
</div>';

require 'moduls/foot.php';

?>
