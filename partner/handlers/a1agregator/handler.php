<?php
/**
 * Copyright (c) 2012, Gemorroj
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 *
 * @author Sea, Gemorroj
 */
/**
 * Sea Downloads
 *
 * @author Sea, Gemorroj
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */


require '../../../moduls/config.php';


$password = mt_rand(1000, mt_getrandmax());
$answer = str_replace('{password}', $password, ANSWER);
$result = $num = false;

$result1 = mysql_query("
    INSERT INTO `inbox`
	VALUES (
	0,
	'" . mysql_real_escape_string($_GET['date'], $mysql) . "',
	'" . mysql_real_escape_string($_GET['msg'], $mysql) . "',
    '" . mysql_real_escape_string($_GET['msg_trans'], $mysql) ."',
    " . intval($_GET['operator_id']) . ",
    " . floatval($_GET['user_id']) . ",
    " . intval($_GET['smsid']) . ",
    " . floatval($_GET['cost_rur']) . ",
    " . floatval($_GET['cost']) . ",
    " . intval($_GET['test']) . ",
    " . intval($_GET['num']) . ",
    '" . mysql_real_escape_string($_GET['skey'], $mysql) . "',
    '" . mysql_real_escape_string($_GET['sign'], $mysql) . "',
    " . intval($_GET['ran'], $mysql) . ",
    '" . mysql_real_escape_string($answer, $mysql) . "'
	)
	", $mysql);

if ($result1) {
	foreach ($partner[$_GET['operator_id']] as $var) {
		if ($var[0] == $_GET['num']) {
			$num = $var[1];
			break;
		}
	}

	if (!$num) {
		$num = UNKNOWN_NUMBER;
	}

	if ($num) {
        $result2 = mysql_query("
            INSERT INTO `passwords`
            VALUES (
            0,
            " . $password . ",
            NOW(),
            NOW() + INTERVAL " . $num . "
            )
        ", $mysql);
    } else {
        $result2 = false;
    }

    if (!$result2) {
    	mysql_query('DELETE FROM `inbox` WHERE `id` = ' . mysql_insert_id($mysql), $mysql);
   	} else {
   		$result = true;
    }
}

header('Content-type: text/plain; charset=UTF-8');
if ($result) {
    echo 'smsid: ' . $_GET['smsid'] . "\nstatus: reply\n\n" . $answer;
} else {
	echo 'smsid: ' . $_GET['smsid'] . "\nstatus: ignore\n\nError";
}

?>
