<?php
#-----------------------------------------------------#
#     ============ЗАГРУЗ-ЦЕНТР=============           #
#             	 Автор  :  Sea                        #
#               E-mail  :  x-sea-x@ya.ru              #
#                  ICQ  :  355152215                  #
#   Вы не имеете права распространять данный скрипт.  #
#   		По всем вопросам пишите в ICQ.        #
#-----------------------------------------------------#

// mod Gemorroj

$i = intval($_GET['i']);

header('Content-type: image/png');
$im = imagecreate(100, 4);
$c0 = imagecolorallocate($im, 0, 0, 0);
$c1 = imagecolorallocate($im, 255, 128, 0);
$c2 = imagecolorallocate($im, 100, 150, 225);
$c3 = imagecolorallocate($im, 168, 175, 187);
imagefill($im, 100, 0, $c2);
imagefilledrectangle($im, 0, 0, $i, 4, $c1);
imagerectangle($im, 0, 0, 99, 3, $c0);
imagepng($im, '', 9);
imagedestroy($im);

?>
