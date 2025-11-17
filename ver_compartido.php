<?php
	//Inicio la sesión
	session_start();

	//Utiliza los datos de sesion comprueba que el usuario este autenticado
	if($_SESSION["autenticado"] != "SI") {
		header("Location: index.php");
		exit();
	}

	$compartido_id = isset($_GET['id']) ? $_GET['id'] : '';

	$base_ruta = "d:\\mybox";
	$archivo_compartidos = $base_ruta . '\\compartidos.json';

	// Cargar compartidos
	$compartidos = array();
	if(file_exists($archivo_compartidos)) {
		$contenido = file_get_contents($archivo_compartidos);
		$compartidos = json_decode($contenido, true);
	}

	// Verificar que el compartido existe y es para este usuario
	if(!isset($compartidos[$compartido_id]) || $compartidos[$compartido_id]['usuario_destino'] != $_SESSION["usuario"]) {
		die("No tienes permiso para acceder a este archivo.");
	}

	$compartido = $compartidos[$compartido_id];
	$ruta_archivo = $compartido['ruta_completa'];

	if(!file_exists($ruta_archivo)) {
		die("El archivo compartido ya no existe.");
	}

	$archivo = basename($ruta_archivo);
	$mime = mime_content_type($ruta_archivo);
	$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

	// Lista de extensiones que se mostrarán en el navegador
	$extensiones_visualizar = array('pdf', 'jpg', 'jpeg', 'png');

	// Si es PDF, JPG o PNG, mostrar en el navegador
	if(in_array($extension, $extensiones_visualizar)) {
		header("Content-type: ". $mime);
		header("Content-Disposition: inline; filename=".$archivo);
		readfile($ruta_archivo);
	} else {
		// Para otras extensiones, forzar descarga
		header("Content-Disposition: attachment; filename=".$archivo);
		header("Content-type: ". $mime);
		header("Content-length: ".filesize($ruta_archivo));
		readfile($ruta_archivo);
	}
?>
