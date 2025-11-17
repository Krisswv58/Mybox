<?php
	//Inicio la sesión
	session_start();

	//Utiliza los datos de sesion comprueba que el usuario este autenticado
	if($_SESSION["autenticado"] != "SI") {
		header("Location: index.php");
		exit(); //fin del scrip
	}

	// Sistema de navegación entre carpetas
	$base_ruta = "d:\\mybox".'\\'.$_SESSION["usuario"];
	$carpeta_actual = isset($_GET['carpeta']) ? $_GET['carpeta'] : '';

	// Prevenir ataques de directory traversal
	$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);

	// Construir ruta completa
	if($carpeta_actual != '') {
		$ruta = $base_ruta . '\\' . $carpeta_actual;
	} else {
		$ruta = $base_ruta;
	}

	$datos = explode('\\',"d:\\mybox");

	// Función para obtener el icono según la extensión
	function obtenerIcono($nombre_archivo, $es_directorio) {
		if($es_directorio) {
			return '📁'; // Icono de carpeta
		}

		$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

		switch($extension) {
			case 'pdf':
				return '📄';
			case 'doc':
			case 'docx':
				return '📝';
			case 'xls':
			case 'xlsx':
				return '📊';
			case 'ppt':
			case 'pptx':
				return '📽️';
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'gif':
			case 'bmp':
				return '🖼️';
			case 'mp3':
			case 'wav':
			case 'ogg':
				return '🎵';
			case 'mp4':
			case 'avi':
			case 'mkv':
				return '🎬';
			case 'zip':
			case 'rar':
			case '7z':
				return '📦';
			case 'txt':
				return '📃';
			default:
				return '📎';
		}
	}

	// Función para convertir bytes a MB
	function bytesToMB($bytes) {
		return round($bytes / (1024 * 1024), 2);
	}
?>
<!doctype html>
<html>
<head>
	<?php include_once('partes/encabe.inc'); ?>
    <title>Ingreso al Sitio</title>
</head>
<body class="container cuerpo">
	<header class="row">
        <div class="row">
        	<div class="col-lg-6 col-sm-6">
        		<img  src="imagenes/encabe.png" alt="logo institucional" width="100%">
            </div>
        </div>
        <div class="row">
            <?php include_once('partes/menu.inc'); ?>
        </div>
        <br />
    </header>

    <main class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<strong>Mi Cajón de Archivos - myBox</strong>
			</div>
			<div class="panel-body">
				<?php
					// Mostrar breadcrumb de navegación
					echo '<div style="margin-bottom: 15px;">';
					echo '<strong>Ubicación: </strong>';
					echo '<a href="carpetas.php">🏠 Inicio</a>';

					if($carpeta_actual != '') {
						$partes_ruta = explode('\\', $carpeta_actual);
						$ruta_acumulada = '';
						foreach($partes_ruta as $parte) {
							$ruta_acumulada .= ($ruta_acumulada != '' ? '\\' : '') . $parte;
							echo ' / <a href="carpetas.php?carpeta=' . urlencode($ruta_acumulada) . '">' . htmlspecialchars($parte) . '</a>';
						}
					}
					echo '</div>';

					// Botones de acción
					echo '<div style="margin-bottom: 15px;">';
					echo '<a href="agrearchi.php?carpeta=' . urlencode($carpeta_actual) . '" class="btn btn-primary">📤 Subir Archivo</a> ';
					echo '<a href="codigos/crearcarpeta.php?carpeta=' . urlencode($carpeta_actual) . '" class="btn btn-success">📁 Nueva Carpeta</a> ';
					echo '<a href="compartir.php?carpeta=' . urlencode($carpeta_actual) . '" class="btn btn-info">🔗 Compartir</a>';
					echo '</div>';

					$conta = 0;
					if(file_exists($ruta)) {
						$directorio = opendir($ruta);
						echo '<table class="table table-striped table-hover">';
							echo '<thead>';
							echo '<tr>';
								echo '<th width="5%">Tipo</th>';
								echo '<th width="35%">Nombre</th>';
								echo '<th width="15%">Tamaño (MB)</th>';
								echo '<th width="20%">Último acceso</th>';
								echo '<th width="15%">Acciones</th>';
							echo '</tr>';
							echo '</thead>';
							echo '<tbody>';

							// Primero mostrar las carpetas
							$archivos = array();
							$carpetas = array();

							while($elem = readdir($directorio)){
								if(($elem!='.') and ($elem!='..')){
									if(is_dir($ruta.'/'.$elem)) {
										$carpetas[] = $elem;
									} else {
										$archivos[] = $elem;
									}
								}
							}

							// Ordenar carpetas y archivos alfabéticamente
							sort($carpetas);
							sort($archivos);

							// Mostrar carpetas primero
							foreach($carpetas as $carpeta) {
								$ruta_completa = $ruta.'/'.$carpeta;
								$nueva_carpeta = ($carpeta_actual != '' ? $carpeta_actual . '\\' : '') . $carpeta;

								echo '<tr>';
								echo '<td style="font-size: 24px;">' . obtenerIcono($carpeta, true) . '</td>';
								echo '<td><a href="carpetas.php?carpeta=' . urlencode($nueva_carpeta) . '"><strong>' . htmlspecialchars($carpeta) . '</strong></a></td>';
								echo '<td>--</td>';
								echo '<td>'.date("d/m/Y H:i:s", fileatime($ruta_completa)).'</td>';
								echo '<td>';
								echo '<a href="codigos/borrar.php?elemento=' . urlencode($carpeta) . '&carpeta=' . urlencode($carpeta_actual) . '&tipo=carpeta" onclick="return confirmarBorrado(\'' . htmlspecialchars($carpeta) . '\', true);" class="btn btn-danger btn-sm">🗑️ Borrar</a>';
								echo '</td>';
								echo '</tr>';
								$conta++;
							}

							// Luego mostrar archivos
							foreach($archivos as $archivo) {
								$ruta_completa = $ruta.'/'.$archivo;
								$tamano_mb = bytesToMB(filesize($ruta_completa));

								echo '<tr>';
								echo '<td style="font-size: 24px;">' . obtenerIcono($archivo, false) . '</td>';
								echo '<td><a href="abrArchi.php?arch=' . urlencode($archivo) . '&carpeta=' . urlencode($carpeta_actual) . '">' . htmlspecialchars($archivo) . '</a></td>';
								echo '<td>' . $tamano_mb . ' MB</td>';
								echo '<td>'.date("d/m/Y H:i:s", fileatime($ruta_completa)).'</td>';
								echo '<td>';
								echo '<a href="codigos/borrar.php?elemento=' . urlencode($archivo) . '&carpeta=' . urlencode($carpeta_actual) . '&tipo=archivo" onclick="return confirmarBorrado(\'' . htmlspecialchars($archivo) . '\', false);" class="btn btn-danger btn-sm">🗑️ Borrar</a>';
								echo '</td>';
								echo '</tr>';
								$conta++;
							}

						echo '</tbody>';
						echo '</table>';
						closedir($directorio);

						if($conta == 0)
							echo '<div class="alert alert-info">La carpeta está vacía</div>';
					} else {
						echo '<div class="alert alert-warning">La carpeta no existe o no se pudo acceder</div>';
					}
				?>
			</div>
		</div>

		<script>
		function confirmarBorrado(nombre, esCarpeta) {
			var tipo = esCarpeta ? 'carpeta' : 'archivo';
			return confirm('¿Está seguro que desea eliminar ' + (esCarpeta ? 'la carpeta' : 'el archivo') + ' "' + nombre + '"?');
		}
		</script>
    </main>

    <footer class="row">

    </footer>
	<?php include_once('partes/final.inc'); ?>
</body>
</html>
