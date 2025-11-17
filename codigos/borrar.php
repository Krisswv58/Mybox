<?php
	session_start();

	// Verifica que el usuario esté autenticado
	if($_SESSION["autenticado"] != "SI") {
		header("Location: ../index.php");
		exit();
	}

	$elemento = isset($_GET['elemento']) ? $_GET['elemento'] : '';
	$carpeta_actual = isset($_GET['carpeta']) ? $_GET['carpeta'] : '';
	$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'archivo';

	// Prevenir directory traversal
	$elemento = str_replace(['..', '/', '\\\\'], '', $elemento);
	$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);

	$base_ruta = "d:\\mybox";
	$ruta = $base_ruta . '/' . $_SESSION["usuario"];

	if($carpeta_actual != '') {
		$ruta .= '/' . $carpeta_actual;
	}

	$ruta_completa = $ruta . '/' . $elemento;

	// Función para eliminar directorio recursivamente
	function eliminarDirectorio($dir) {
		if (!file_exists($dir)) {
			return true;
		}

		if (!is_dir($dir)) {
			return unlink($dir);
		}

		foreach (scandir($dir) as $item) {
			if ($item == '.' || $item == '..') {
				continue;
			}

			if (!eliminarDirectorio($dir . DIRECTORY_SEPARATOR . $item)) {
				return false;
			}
		}

		return rmdir($dir);
	}

	echo "<!DOCTYPE html>";
	echo "<html>";
	echo "<head>";
	echo "<meta charset='UTF-8'>";
	echo "<title>Eliminar elemento</title>";
	echo "<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>";
	echo "</head>";
	echo "<body class='container' style='margin-top: 50px;'>";

	if($elemento != '' && file_exists($ruta_completa)) {
		$exito = false;

		if($tipo == 'carpeta' && is_dir($ruta_completa)) {
			$exito = eliminarDirectorio($ruta_completa);
			$mensaje = $exito ? "La carpeta ha sido eliminada exitosamente." : "NO se pudo eliminar la carpeta.";
		} else if($tipo == 'archivo' && is_file($ruta_completa)) {
			$exito = @unlink($ruta_completa);
			$mensaje = $exito ? "El archivo ha sido eliminado exitosamente." : "NO se pudo eliminar el archivo.";
		} else {
			$mensaje = "El elemento no existe o el tipo es incorrecto.";
		}

		if($exito) {
			echo "<div class='alert alert-success'><strong>Éxito:</strong> " . $mensaje . "</div>";
		} else {
			echo "<div class='alert alert-danger'><strong>Error:</strong> " . $mensaje . "</div>";
		}
	} else {
		echo "<div class='alert alert-warning'><strong>Advertencia:</strong> El elemento no existe.</div>";
	}

	// Retornar a carpetas.php
	echo "<script language='JavaScript'>";
	echo "setTimeout(function() { location.href='../carpetas.php?carpeta=" . urlencode($carpeta_actual) . "'; }, 2000);";
	echo "</script>";

	echo "<p><a href='../carpetas.php?carpeta=" . urlencode($carpeta_actual) . "' class='btn btn-primary'>Volver a Mis Archivos</a></p>";
	echo "</body>";
	echo "</html>";
?>
