<?php
	//Inicio la sesión
	session_start();

	//Utiliza los datos de sesion comprueba que el usuario este autenticado
	if ($_SESSION["autenticado"] != "SI") {
		header("Location: index.php");
		exit(); //fin del script
	}

	$archivo = isset($_GET['arch']) ? $_GET['arch'] : '';
	$carpeta_actual = isset($_GET['carpeta']) ? $_GET['carpeta'] : '';

	// Prevenir directory traversal
	$archivo = str_replace(['..', '/', '\\\\'], '', $archivo);
	$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);

	//Declara ruta carpeta del usuario
	$base_ruta = "d:\\mybox";
	$ruta = $base_ruta.'/'.$_SESSION["usuario"];

	if($carpeta_actual != '') {
		$ruta .= '/' . $carpeta_actual;
	}

	$ruta = $ruta.'/'.$archivo;

	// Verificar que el archivo existe
	if(!file_exists($ruta)) {
		die("El archivo no existe.");
	}

	$mime = mime_content_type($ruta);
	$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

	// Lista de extensiones que se mostrarán en el navegador: pdf, jpg, png
	$extensiones_visualizar = array('pdf', 'jpg', 'jpeg', 'png');

	// Si es PDF, JPG o PNG, mostrar en el navegador
	if(in_array($extension, $extensiones_visualizar)) {
		header("Content-type: ". $mime);
		header("Content-Disposition: inline; filename=".$archivo);
		readfile($ruta);
	} else {
		// Para otras extensiones, forzar descarga
		header("Content-Disposition: attachment; filename=".$archivo);
		header("Content-type: ". $mime);
		header("Content-length: ".filesize($ruta));
		readfile($ruta);
	}
?>
