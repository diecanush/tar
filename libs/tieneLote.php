<?php
require "cabecera.php";
$lote      = $_GET['lote'];
$ruta      = $_GET['ruta'];
$carpeta   = $ruta.'Lote'.$lote;
$contenido = glob($carpeta."/*$lote*");
if (array_filter($contenido)) {
	header('location: procesar.php?lote='.$lote.'&ruta='.$ruta);
} else {
	echo "<header class='contenido'>
			<h3>la carpeta no tiene el lote $lote</h3>
			<input type='button' class='btn btn-warning' onclick='history.back()' value='volver'>
		</header>
";
}

?>