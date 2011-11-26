<?php
// mod Gemorroj
require_once dirname(__FILE__) . '/config.php';

if (isset($_REQUEST['langpack'])) {
    $_SESSION['langpack'] = $_REQUEST['langpack'];
    include_once DIR . '/language/' . $_SESSION['langpack'] . '.dat';
    $_SESSION['language'] = & $language;
}

if (!isset($_SESSION['language']) || !file_exists(DIR . '/language/' . $_SESSION['langpack'] . '.dat')) {
    // язык по умолчанию
    $_SESSION['langpack'] = $setup['langpack'];
    include_once DIR . '/language/' . $_SESSION['langpack'] . '.dat';
    $_SESSION['language'] = & $language;
}


function view_languages()
{
    echo $_SESSION['language']['language'] . ':<br/><select class="enter" name="langpack">';

    foreach (glob(DIR . '/language/*.dat') as $v) {
        $v = pathinfo($v, PATHINFO_FILENAME);
        echo '<option value="' . htmlspecialchars($v) . '" ' . sel($v, $_SESSION['langpack']) . '>' . htmlspecialchars($v, ENT_NOQUOTES) . '</option>';
    }

    echo '</select>';
}


function language_dir($english = '', $russian = '')
{
    echo '<input class="enter" name="new[english]" type="text" size="70" value="' . htmlspecialchars($english) . '"/>(english)<br/><input class="enter" name="new[russian]" type="text" size="70" value="' . htmlspecialchars($russian) . '"/>(russian)<br/>';
}

?>
