<?php

$xml = simplexml_load_file('id_operators.xls');
//print_r($xml);


$array = array();
$all = sizeof($xml->Worksheet->Table->Row);
for ($i = 1; $i < $all; ++$i) {
	$array[(string)$xml->Worksheet->Table->Row[$i]->Cell[0]->Data] = array(
		array(
			'NUMBER',
			'5 Day',
			(string)$xml->Worksheet->Table->Row[$i]->Cell[1]->Data,
			(string)$xml->Worksheet->Table->Row[$i]->Cell[2]->Data,
			'PAYMENT'
		)
	);
}

ksort($array);
var_export($array, false);

?>
