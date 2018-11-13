<?php
$ruta  = "../Lotes/";
$lista = array();

//CREAR UNA LISTA CON LAS CARPETAS DE LA RUTA
if (is_dir($ruta)) {
	if ($dh = opendir($ruta)) {
		while (($carpeta = readdir($dh)) !== false) {
			if (is_dir($ruta.$carpeta) && $carpeta !== '.' && $carpeta !== '..') {
				array_push($lista, substr($carpeta, 4));
			}
		}
		closedir($dh);
	}
}

//ORDENAR LA LISTA
asort($lista);
?>
<script type="text/javascript" language="javascript" src="js/jslistadolotes.js"></script>
<table id="tabla_lista_lotes" class="display table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>LOTE</th>
            <th>CSV</th>
            <th>PROCESADO</th>
            <th>ACCIONES</th>
        </tr>
    </thead>
	<tbody>
<?php foreach ($lista as $lote) {?>
	<tr>
		<td>
	<?php echo $lote;?>
	</td>
		<td>
	<?php if ($csv = glob($ruta."Lote".$lote."/*.csv")[0]) {
		echo substr($csv, -28);
	} else {
		echo "no tiene";
	}
	?>
	</td>
		<td>
	<?php
	$tar = $ruta."Lote".$lote.".tar";
	if ($procesado = file_exists($tar)) {
		echo date("Y/m/d H:i:s.", filemtime($tar));
	} else {
		echo "no";
	}
	?>
					</td>
					<td>
						<a class='btn btn-primary' href="libs/tieneTar.php?lote=<?php echo $lote;?>&ruta=<?php echo $ruta;?>" >PROCESAR</a>
	<?php if ($procesado) {
		$log = end(glob($ruta."*$lote*.log"));
		echo "<a class='btn btn-success' target='blank' href='libs/verLog.php?log=".$log."''> VER LOG</a>";
	}?>
	</td>
	</tr>
	<?php }?>
</tbody>
</table>

