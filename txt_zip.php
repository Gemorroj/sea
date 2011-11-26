<?php
// mod Gemorroj

require 'moduls/config.php';
define('DIRECTORY', str_replace(array('\\', '//'), '/', dirname($_SERVER['PHP_SELF']) . '/'));

// Проверка переменных
$id = intval($_GET['id']);
// Получаем инфу о файле
$d = mysql_fetch_row(mysql_query('SELECT `path` FROM `files` WHERE `id` = ' . $id, $mysql));


if (file_exists($d[0])) {
    mysql_query('UPDATE `files` SET `loads` = `loads` + 1, `timeload` = ' . $_SERVER['REQUEST_TIME'] . ' WHERE `id` = ' . $id, $mysql);

    $tmp = $setup['zpath'] . '/' . str_replace('/', '--', iconv_substr(strstr($d[0], '/'), 1)) . '.zip';

    if (!file_exists($tmp)) {
        include 'moduls/PEAR/pclzip.lib.php';

        $zip = new PclZip($tmp);

        function cb ($p_event, &$p_header)
        {
            $p_header['stored_filename'] = basename($p_header['filename']);
            return 1;
        }
        $zip->create($d[0], PCLZIP_CB_PRE_ADD, 'cb');
        chmod($tmp, 0644);
    }

    header('Location: http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . str_replace('%2F', '/', rawurlencode($tmp)), true, 301);
} else {
    echo $setup['hackmess'];
}

?>
