<?php

/**
 * @author Gemorroj
 * @copyright 2009
 */


require 'moduls/config.php';
require 'moduls/header.php';


if (!$setup['service_change_advanced']) {
	error('Not found');
}

if (!isset($_SESSION['id']) && !isset($_GET['act'])) {
    echo '<form action="' . DIRECTORY . 'service.php" method="get">
<div class="row">
<input type="hidden" name="act" value="enter"/>
ID:<br/>
<input class="enter" type="text" name="id"/><br/>
' . $language['pass'] . '<br/>
<input class="enter" type="password" name="pass"/><br/>
<input type="submit" value="' . $language['go'] . '" class="buttom"/>
</div>
</form>
<div class="row"><a href="' . DIRECTORY . 'service.php?act=registration">' . $language['registration'] . '</a><br/></div>
<form action="' . DIRECTORY . 'service.php?act=pass" method="post">
<div class="row">
' . $language['lost password'] . '<br/>
ID:<input class="enter" type="text" name="id"/><input type="submit" value="' . $language['go'] . '" class="buttom"/>
</div>
</form>';
} else if (isset($_GET['act']) && $_GET['act'] == 'enter' && isset($_GET['id']) && isset($_GET['pass'])) {
	$q = mysql_query('
    SELECT *
	FROM `users_profiles`
	WHERE `id` = ' . intval($_GET['id']) . '
	AND `pass` = "' . md5($_GET['pass']) . '"'
	, $mysql);

	if (mysql_num_rows($q)) {
		$assoc = mysql_fetch_assoc($q);
		$_SESSION['id'] = $assoc['id'];
		$_SESSION['name'] = $assoc['name'];
		$_SESSION['url'] = $assoc['url'];
		$_SESSION['mail'] = $assoc['mail'];
		$_SESSION['style'] = $assoc['style'];
		echo '<div class="row"><a href="' . DIRECTORY . 'service.php">' . $language['go'] . '</a><br/></div>';
	} else {
		error($language['user not found']);
	}
} else if (isset($_GET['act']) && $_GET['act'] == 'registration') {
	if ($_POST) {
		$error = '';
		if (!isset($_SESSION['captcha_keystring']) || $_SESSION['captcha_keystring'] != $_POST['keystring']) {
	    	$error .= $language['not a valid code'] . '<br/>';
		}
		unset($_SESSION['captcha_keystring']);

		if (strlen($_POST['pass']) < 4) {
			$error .= $language['short password'] . '<br/>';
		}

		if (strlen($_POST['url']) < 4 || !strpos($_POST['url'], '.')) {
			$error .= $language['not a valid url'] . '<br/>';
		}

		if (strlen($_POST['mail']) < 4 || !strpos($_POST['mail'], '@')) {
			$error .= $language['not a valid mail'] . '<br/>';
		}

		if ($error) {
			error($error);
		}

		$url = mysql_real_escape_string($_POST['url'], $mysql);
		$pass = md5($_POST['pass']);
		$mail = mysql_real_escape_string($_POST['mail'], $mysql);
		$name = mysql_real_escape_string($_POST['name'], $mysql);
		$style = mysql_real_escape_string($_POST['style'], $mysql);

		if (mysql_num_rows(mysql_query('
            SELECT 1
		    FROM `users_profiles`
		    WHERE `url` = "' . $url . '"'
		, $mysql))) {
			// Такой URL уже есть
			error($language['duplicate url']);
		} else if (mysql_query('INSERT INTO `users_profiles` SET `name` = "' . $name . '", `url` = "' . $url . '", `pass` = "' . $pass . '", `mail` = "' . $mail . '", `style` = "' . $style . '"', $mysql)) {
			$_SESSION['id'] = mysql_insert_id($mysql);
			$_SESSION['name'] = $_POST['name'];
			$_SESSION['url'] = $_POST['url'];
			$_SESSION['mail'] = $_POST['mail'];
			$_SESSION['style'] = $_POST['style'];
			mail($mail, '=?utf-8?B?' . base64_encode('Registration in ' . $_SERVER['HTTP_HOST'] . DIRECTORY) . '?=', 'Your password: ' . $_POST['pass'] . "\r\n" . 'ID: ' . $_SESSION['id'], 'From: robot@' . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8");
			echo '<div class="row">' . $language['registered'] . '<br/></div><div class="row"><a href="' . DIRECTORY . 'service.php">' . $language['go'] . '</a><br/></div>';
		} else {
			error($language['error']);
		}
	} else {
        echo '<form action="' . DIRECTORY . 'service.php?act=registration" method="post">
<div class="row">
<table><tr>
<th>' . $language['your site'] . '</th><th>' . $language['name'] . '</th>
</tr><tr><td>http://<input class="enter" type="text" name="url"/></td><td><input class="enter" type="text" name="name" style="width:96%;"/></td></tr>
<tr><td>' . $language['style'] . ':</td><td>http://<input class="enter" type="text" name="style"/></td></tr>
<tr><td>Email:</td><td><input class="enter" type="text" name="mail" style="width:96%;"/></td></tr>
<tr><td>' . $language['pass'] . '</td><td><input class="enter" type="password" name="pass" style="width:96%;"/></td></tr>
<tr><th><img alt="" src="' . DIRECTORY . 'moduls/kcaptcha/index.php?' . session_name() . '=' . session_id() . '" /></th><td><input class="enter" type="text" name="keystring" maxlength="4" style="width:96%;"/></td></tr>
<tr><th colspan="2"><input type="submit" value="' . $language['go'] . '" class="buttom"/></th></tr>
</table>
</div>
</form>';
	}
} else if (isset($_GET['act']) && $_GET['act'] == 'pass') {
	$id = intval($_POST['id']);
	$q = mysql_query('SELECT `mail` FROM `users_profiles` WHERE `id` = ' . $id, $mysql);
	if (mysql_num_rows($q) && $mail = mysql_result($q, 0)) {
		$pass = pass();
		mysql_query('UPDATE `users_profiles` SET `pass` = "' . md5($pass) . '" WHERE `id` = ' . $id, $mysql);
		mail($mail, '=?utf-8?B?' . base64_encode('Change Password ' . $_SERVER['HTTP_HOST'].DIRECTORY) . '?=', 'Your new password: ' . $pass . "\r\n" . 'ID: ' . $id, 'From: robot@' . $_SERVER['HTTP_HOST'] . "\r\nContent-type: text/plain; charset=UTF-8");
	} else {
		error($language['email not found']);
	}
} else {
	// если пользователь вошел в кабинет
	$act = isset($_GET['act']) ? $_GET['act'] : '';

	switch($act) {
		default:
            echo '<form action="' . DIRECTORY . 'service.php?act=save" method="post"><div class="row"><table><tr><th>N</th><th>' . $language['name'] . '</th><th>' . $language['link'] . '</th></tr>';

            if ($setup['service_head']) {
        	   echo '<tr><th colspan="3">' . $language['head'] . '</th></tr>';
        	   $q = mysql_query('SELECT `name`, `value` FROM `users_settings` WHERE `parent_id` = ' . $_SESSION['id'] . ' AND `position` = "0"', $mysql);
        	   for ($i = 1; $i <= $setup['service_head']; ++$i) {
                    $assoc = mysql_fetch_assoc($q);
                    echo '<tr><td>' . $i . '</td><td><input class="enter" type="text" name="head[name][]" value="' . htmlspecialchars($assoc['name']) . '"/></td><td><input class="enter" type="text" name="head[value][]" value="' . htmlspecialchars($assoc['value']) . '"/></td></tr>';
        	   }
            }
            if ($setup['service_foot']) {
            	echo '<tr><th colspan="3">' . $language['foot'] . '</th></tr>';
            	$q = mysql_query('SELECT `name`, `value` FROM `users_settings` WHERE `parent_id` = ' . $_SESSION['id'] . ' AND `position` = "1"', $mysql);
            	for ($i = 1; $i <= $setup['service_foot']; ++$i) {
            		$assoc = mysql_fetch_assoc($q);
            		echo '<tr><td>' . $i . '</td><td><input class="enter" type="text" name="foot[name][]" value="' . htmlspecialchars($assoc['name']) . '"/></td><td><input class="enter" type="text" name="foot[value][]" value="' . htmlspecialchars($assoc['value']) . '"/></td></tr>';
            	}
            }
            echo '<tr><th colspan="3">URL</th></tr>
<tr><td>&#187;</td><td><input class="enter" type="text" name="name" value="' . htmlspecialchars($_SESSION['name']) . '"/></td><td><input class="enter" type="text" name="url" value="' . htmlspecialchars($_SESSION['url']) . '"/></td></tr>
<tr><td>Email</td><td colspan="2"><input class="enter" type="text" name="mail" value="' . htmlspecialchars($_SESSION['mail']) . '" style="width:98%;"/></td></tr>
<tr><td>' . $language['style'] . '</td><td colspan="2"><input class="enter" type="text" name="style" value="' . htmlspecialchars($style) . '" style="width:98%;"/></td></tr>
<tr><th colspan="3"><input type="submit" value="' . $language['go'] . '" class="buttom"/></th></tr>
</table>
</div></form>
<div class="row"><form action=""><div>' . $language['service'] . '<br/><input class="enter" type="text" value="http://' . $_SERVER['HTTP_HOST'] . DIRECTORY . '?user=' . $_SESSION['id'] . '"/></div></form></div>';

            break;

		case 'save':
			$_SESSION['url'] = $_POST['url'];
			$_SESSION['name'] = $_POST['name'];
			$_SESSION['mail'] = $_POST['mail'];

			$key = 0;

			mysql_query(
			'UPDATE `users_profiles`
			SET
			`name` = "' . mysql_real_escape_string($_POST['name'], $mysql) . '",
			`url` = "' . mysql_real_escape_string($_POST['url'], $mysql) . '",
			`mail` = "' . mysql_real_escape_string($_POST['mail'], $mysql) . '",
			`style` = "' . mysql_real_escape_string($_POST['style'], $mysql) . '",
			WHERE `id` = ' . $_SESSION['id']
			, $mysql);
			mysql_query('DELETE FROM `users_settings` WHERE `parent_id` = ' . $_SESSION['id'], $mysql);
			$sql = 'INSERT INTO `users_settings` (`parent_id`, `position`, `name`, `value`) VALUES';

			$all = sizeof($_POST['head']['name']);
			$all = $all < $setup['service_head'] ? $all : $setup['service_head'];
			for ($i = 0; $i < $all; ++$i) {
				if ($_POST['head']['name'][$i] && $_POST['head']['value'][$i]) {
					$sql .= '('.$_SESSION['id'].', "0", "' . mysql_real_escape_string($_POST['head']['name'][$i], $mysql) . '", "' . mysql_real_escape_string($_POST['head']['value'][$i], $mysql) . '"),';
					$key++;
				}
			}

			$all = sizeof($_POST['foot']['name']);
			$all = $all < $setup['service_foot'] ? $all : $setup['service_foot'];
			for ($i = 0; $i < $all; ++$i) {
				if ($_POST['foot']['name'][$i] && $_POST['foot']['value'][$i]) {
					$sql .= '(' . $_SESSION['id'] . ', "1", "' . mysql_real_escape_string($_POST['foot']['name'][$i], $mysql) . '", "' . mysql_real_escape_string($_POST['foot']['value'][$i], $mysql) . '"),';
					$key++;
				}
			}

			if ($key) {
				$r = mysql_query(rtrim($sql, ','), $mysql);
			} else {
				$r = true;
			}

			mysql_query('OPTIMIZE TABLE `users_profiles`, `users_settings`', $mysql);
			mysql_query('ANALYZE TABLE `users_profiles`, `users_settings`', $mysql);

			if ($r) {
				echo '<div class="row">' . $language['settings saved'] . '<br/></div>';
			} else {
				error($language['error']);
			}
            break;


		case 'exit':
			session_destroy();
			error($language['signed out']);
            break;
	}
	
	echo '<div class="iblock">- <a href="' . DIRECTORY . 'service.php?act=exit">' . $language['exit'] . '</a><br/></div>';
}





echo '<div class="iblock">
- <a href="' . DIRECTORY . '">' . $language['downloads'] . '</a><br/>
- <a href="' . $setup['site_url'] . '">' . $language['home'] . '</a></div>';

require 'moduls/foot.php';

?>
