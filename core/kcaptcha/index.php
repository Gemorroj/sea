<?php
error_reporting(0);

require './kcaptcha.php';

session_save_path(dirname(__FILE__) . '/../tmp');
session_name('sea');
session_start();

$captcha = new KCAPTCHA();

if ($_REQUEST[session_name()]) {
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
}
