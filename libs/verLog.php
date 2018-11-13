<?php $log = $_GET['log'];
$archivo   = fopen($log, 'r');
while ($lineas = fgetcsv($archivo, ";")) {
	$numero = count($lineas);
	for ($c = 0; $c < $numero; $c++) {
		echo $lineas[$c]."<br />\n";
	}
}

?>