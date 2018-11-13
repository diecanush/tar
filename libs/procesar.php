<?php

$ruta = '../Lotes/';

require "cabecera.php";

//ESTABLECER TIEMPO MAXIMO DE EJECUCION DEL SCRIPT EN 600 SEGUNDOS
ini_set('max_execution_time', 600);


//FUNCION PARA ELIMINAR DIRECTORIOS QUE TENGAN SUBDIRECTORIOS
function eliminar_directorio($dir) {
	$result = false;
	if ($handle = opendir("$dir")) {
		$result = true;
		while ((($file = readdir($handle)) !== false) && ($result)) {
			if ($file != '.' && $file != '..') {
				if (is_dir("$dir/$file")) {
					$result = eliminar_directorio("$dir/$file");
				} else {
					$result = unlink("$dir/$file");
				}
			}
		}
		closedir($handle);
		if ($result) {
			$result = rmdir($dir);
		}
	}
	return $result;
}

//RECIBIR LA CARPETA DEL FORMULARIO QUE LLAMO AL SCRIPT
$carpeta = 'Lote'.$_GET['lote'];

//CREAR UNA CARPETA TEMPORAL
$carpetaTemp = $ruta."temp";

if (!mkdir($carpetaTemp)) {
	echo "al crear carpeta temp: ".error_get_last()['message'];
}

//CREAR UN ARCHIVO LOG PARA REGISTRAR EL PROCESO
$nombreLog = $carpeta."_".date('Ymd_His').".log";
$log       = fopen($ruta.$nombreLog, w);

//HALLAR EL ARCHIVO CSV DEL LOTE
$encontrado = glob($ruta.$carpeta."/*.csv")[0];

//REGISTRAR EL NOMBRE DEL CSV EN EL LOG
fwrite($log, "csv: ".$encontrado.PHP_EOL);

//EXTRAER EL NOMBRE DEL ARCHIVO CSV DE LA RUTA
$nombreArchivoCsv = substr($encontrado, -28);

echo "<body>";

//ENCABEZADO DE LA PAGINA DE RESULTADOS
echo "
		<header id='titulo'>
        	<h3>$nombreArchivoCsv</h3>
        	<a href='../' class='btn btn-success'>volver</a>
    	</header>
";

//COPIAR EL CSV A LA CARPETA TEMPORAL
if (!copy($encontrado, $ruta."temp/".$nombreArchivoCsv)) {

	echo "al copiar csv".error_get_last()['message'];
}

//CREAR LA CARPETA DEL LOTE EN LA TEMPORAL JUNTO AL CSV
if (!mkdir($ruta."temp/".$carpeta)) {
	echo "al crear subcarptea en temp".error_get_last()['message'];
}

//ABRIR EL CSV
if (($archivo = fopen($encontrado, "r")) !== FALSE) {

	//TABLA PARA MOSTRAR LOS RESULTADOS EN PANTALLA

	//SCRIPT DE LA PLANTILLA DATATABLES
	echo "
		<script type='text/javascript' language='javascript'>
			$(document).ready(function(){
   $('#tabla_proceso').dataTable( {
        'sPaginationType': 'full_numbers'
    } );
})
		</script>

		<!--ENCABEZADO DE LA TABLA-->
		<div id='contenido'>
		<table id='tabla_proceso' class='display table-striped table-bordered'>
			<thead>
				<th>pdf</th>
				<th>esta</th>
				<th>copia</th>
				<th>imagen1</th>
				<th>esta</th>
				<th>copia</th>
				<th>imagen2</th>
				<th>esta</th>
				<th>copia</th>
				<th>imagen3</th>
				<th>esta</th>
				<th>copia</th>
				<th>recorte</th>
				<th>esta</th>
				<th>copia</th>
			</thead>";

	//CONTADOR DE REGISTROS
	$cont = 0;

	//CICLO DE RECORRIDO DEL CSV DESARMANDO CADA LINEA
	while (($datos = fgetcsv($archivo, 10000, ";")) !== FALSE) {

		//COMENZAR LA LINEA PARA REGISTRAR EN EL LOG
		$linea = $cont.";";

		//OBTENER LAS COLUMNAS IMPORTANTES
		$pdf = $datos[0];

		//RUTA DEL PDF
		$nombreArchivoPdf = $carpeta."/".$pdf.".pdf";

		//IMAGEN 1
		$imagen1 = $datos[89];

		//RUTA DE LA IMAGEN 1
		$nombreImagen1 = $carpeta."/".$imagen1;

		//NOMBRE DE LA IMAGEN 2
		$imagen2 = $datos[90];

		//SI HAY IMAGEN 2 ESTABLECER LA RUTA
		if ($imagen2 !== '') {
			$nombreImagen2 = $carpeta."/".$imagen2;
		}

		//NOMBRE DE LA IMAGEN 3
		$imagen3 = $datos[91];

		//SI HAY IMAGEN 3 ESTABLECER LA RUTA
		if ($imagen3 !== '') {
			$nombreImagen3 = $carpeta."/".$imagen3;
		}

		//RECORTE
		$recorte = $datos[92];

		//RUTA DEL RECORTE
		$nombreRecorte = $carpeta."/".$recorte;

		//FILA DE LA TABLA EN PANTALLA
		echo "<tr>
				<td><a href=$ruta$carpeta/$nombreArchivoPdf target=blank>$pdf</a></td>";

		//AGREGAR EL NUMERO DE INFRACCION EN LA LINEA DEL LOG
		$linea .= "Infraccion: ".$pdf.";";

		//COPIAR EL PDF AL TEMPORAL, MOSTRAR EN PANTALLA Y AGREGAR A LA LINEA DEL LOG
		if (file_exists($ruta.$carpeta."/".$nombreArchivoPdf)) {

			echo "<td>si</td>";
			$linea .= $nombreArchivoPdf.";";
			if (copy($ruta.$carpeta."/".$nombreArchivoPdf, $carpetaTemp."/".$nombreArchivoPdf)) {
				echo "<td>ok</td>";
				$linea .= "copiado;";
			} else {
				echo "<td>".error_get_last()['message']."</td>";
				$linea .= "no copiado;";
			}
		} else {
			echo "<td>no</td>
					<td></td>";

			$linea .= "no esiste el pdf;";
		}

		//COPIAR LA IMAGEN 1 AL TEMPORAL, MOSTRAR EN PANTALLA Y AGREGAR A LA LINEA DEL LOG

		echo "<td>$imagen1</td>";

		if (file_exists($ruta.$carpeta."/".$nombreImagen1)) {
			echo "<td>si</td>";
			$linea .= $nombreImagen1.";";
			if (copy($ruta.$carpeta."/".$nombreImagen1, $carpetaTemp."/".$nombreImagen1)) {
				echo "<td>ok</td>";
				$linea .= "copiada;";
			} else {
				echo "<td>".error_get_last()['message']."</td>";
				$linea .= "no copiada";
			}
		} else {
			echo "<td>no</td><td></td>";
			$linea .= "sin imagen 1;";
		}

		//IMAGEN 2

		echo "<td>";

		if ($imagen2 !== '') {
			echo $imagen2;
			$linea .= $imagen2.";";
		} else {
			echo "sin imagen";
			$linea .= "sin imagen 2;";
		}

		echo "</td><td></td><td></td>";

		//IMAGEN 3

		echo "<td>";
		if ($imagen3 !== '') {
			echo $imagen3;
			$linea .= $imagen3.";";
		} else {
			echo "sin imagen";
			$linea .= "sin imagen 3;";
		}

		echo "</td>
				<td></td>
				<td></td>";

		//COPIAR EL RECORTE AL TEMPORAL, MOSTRAR EN PANTALLA Y AGREGAR A LA LINEA DEL LOG

		echo "<td>$recorte</td>
			<td>";

		if (file_exists($ruta.$carpeta."/".$nombreRecorte)) {
			echo "si";
			$linea .= $nombreRecorte.";";
			if (copy($ruta.$carpeta."/".$nombreRecorte, $carpetaTemp."/".$nombreRecorte)) {
				echo "<td>ok</td>";
				$linea .= "copiada;";
			} else {
				echo "<td>".error_get_last()['message']."</td>";
				$linea .= "no copiada;";
			}
		} else {
			echo "no<td></td>";
			$linea .= "sin recorte;";
		}
		echo "</td></tr>";
		$cont++;
		$linea .= PHP_EOL;
		fwrite($log, $linea);
	}

	echo "</table></div>";

	echo "</body>";

	fclose($log);

	copy($ruta.$nombreLog, $carpetaTemp."/".$nombreLog);

	//SI EL LOTE TIENE ARCHIVOS ASOCIADOS SE COPIA
	if (glob($carpetaTemp."/".$carpeta."/*")) {
		$destino = new PharData($ruta.$carpeta.".tar");
		$destino->buildFromDirectory($carpetaTemp);
		echo "<script language='javascript'>
				alert('SE COPIARON $cont REGISTROS')
				</script>";
	} else {
		echo "<script language='javascript'>
				alert('NO SE ENCONTRARON ARCHIVOS EN EL LOTE')
				</script>";
	}

	eliminar_directorio($carpetaTemp);

	fclose($archivo);

}
?>