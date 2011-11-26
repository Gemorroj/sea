<?php
// mod Gemorroj

require 'moduls/config.php';

$id = intval($_GET['id']);

if (file_exists('moduls/cache/' . $id . '.dat')) {
    $data = unserialize(file_get_contents('moduls/cache/' . $id . '.dat'));
    if ($data && @$data['comments']['APIC']) {
        $im = @imagecreatefromstring(substr($data['comments']['APIC'], 12));

        if ($im) {
            header('Content-Type: image/png');
            header('Cache: public');
            header('Cache-control: max-age=2592000');
            header('Expires: ' . date('r', $_SERVER['REQUEST_TIME'] + 2592000));
            imagepng(simple_resize($im), '', 9);
            imagedestroy($im);
        }
    }
}

?>
