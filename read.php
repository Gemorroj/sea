<?php
// mod Gemorroj


require 'moduls/config.php';
require 'moduls/header.php';

if (!$setup['lib_change']) {
	error('Not found');
}
	
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
	$page = 1;
}

// Получаем инфу о файле
$v = mysql_fetch_assoc(mysql_query('SELECT * FROM `files` WHERE `id` = ' . $id, $mysql));
$pathinfo = pathinfo($v['path']);

if (!is_file($v['path']) || strtolower($pathinfo['extension']) != 'txt') {
	error('File not found');
}

$back = mysql_fetch_assoc(mysql_query("SELECT `id` FROM `files` WHERE `path` = '" . mysql_real_escape_string($pathinfo['dirname'] . '/', $mysql) . "'", $mysql));

$filename = $v['name'];
if ($_SESSION['langpack'] == 'russian') {
    $filename = $v['rus_name'];
}

$title .= $_SESSION['language']['read'] . ' - ' . $filename . ' / ' . $page;

if (isset($_SESSION['lib'])) {
	$setup['lib'] = $_SESSION['lib'];
}


// UTF-8
$fp = fopen($v['path'], 'rb');
if ($page > 1) {
    fseek($fp, $page * $setup['lib'] - $setup['lib']);
}
$content = fread($fp, $setup['lib']) . fgets($fp, 1024);
fclose($fp);

if ($page > 1) {
	$i = 0;
	foreach (str_split($content, 1) as $f) {
		if ($f == ' ' || $f == "\n" || $f == "\r" || $f == "\t") {
			break;
		}
		$i++;
	}
	$content = substr($content, $i);
}

$pages = ceil(filesize($v['path']) / $setup['lib']);


if ($setup['lib_str']) {
    echo '<pre class="ik">' . wordwrap(htmlspecialchars(str_to_utf8($content), ENT_NOQUOTES), $setup['lib_str'], "\n", false) . '</pre>' . go($page, $pages, DIRECTORY . 'read/' . $id);
} else {
    echo '<pre class="ik">' . htmlspecialchars(str_to_utf8($content), ENT_NOQUOTES) . '</pre>' . go($page, $pages, DIRECTORY . 'read/' . $id);
}

if ($back['id']) {
	$str = '- <a href="' . DIRECTORY . $back['id'] . '">' . $_SESSION['language']['go to the category'] . '</a><br/>';
} else {
    $str = '';
}
echo '<div class="iblock">- <a href="' . DIRECTORY . 'view/' . $id . '">' . $_SESSION['language']['go to the description of the file'] . '</a><br/>' . $str . '- <a href="' . DIRECTORY . '">' . $_SESSION['language']['downloads'] . '</a><br/>- <a href="' . $setup['site_url'] . '">' . $_SESSION['language']['home'] . '</a><br/></div>';

require 'moduls/foot.php';

?>
