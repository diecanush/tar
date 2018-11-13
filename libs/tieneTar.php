<?php
require "cabecera.php";
$lote = $_GET['lote'];
$ruta = $_GET['ruta'];

//RECIBIR LA CARPETA DEL FORMULARIO QUE LLAMO AL SCRIPT
$carpeta = 'Lote'.$lote;

//SI EXISTE EL TAR DEL LOTE
if (file_exists($ruta.$carpeta.".tar")) {

	//INFORMAR Y OFRECER REPROCESAR O CANCELAR
	echo "
		<header id='titulo'>
        	<h3>el lote $lote ya fue procesado</h3>

			<form action='tieneLote.php?lote=" .$lote."&ruta=".$ruta."' method='post'>
				<input type='submit' value='reprocesar' class='btn btn-danger'>
				<input type='button' class='btn btn-warning' onclick='history.back()' value='cancelar'>
			</form></center>
		</header>
";
} else {

	//REDIRIGIR AL SCRIPT QUE VERIFICA QUE EL LOTE TIENE LOS ARCHIVOS
	header('location: tieneLote.php?lote='.$lote.'&ruta='.$ruta);
}
?>