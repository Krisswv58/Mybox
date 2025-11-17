<?php
	//Inicio la sesión
	session_start();

	//Utiliza los datos de sesion comprueba que el usuario este autenticado
	if ($_SESSION["autenticado"] != "SI") {
		header("Location: ../index.php");
		exit(); //fin del script
	}

	$carpeta_actual = isset($_GET['carpeta']) ? $_GET['carpeta'] : '';
	$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);

	//declara ruta carpeta del usuario
	$base_ruta = "d:\\mybox";
	$ruta = $base_ruta.'/'.$_SESSION["usuario"];

	if($carpeta_actual != '') {
		$ruta .= '/' . $carpeta_actual;
	}

	$Accion_Formulario = $_SERVER['PHP_SELF'];
	if ((isset($_POST["OC_Aceptar"])) && ($_POST["OC_Aceptar"] == "frmCarpeta")) {
		$nombre_carpeta = trim($_POST['txtNombreCarpeta']);

		// Sanitizar nombre de carpeta
		$nombre_carpeta = str_replace(['..', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '', $nombre_carpeta);
		$nombre_carpeta = str_replace(' ', '_', $nombre_carpeta);

		if($nombre_carpeta != '') {
			$nueva_ruta = $ruta . '/' . $nombre_carpeta;

			if(!file_exists($nueva_ruta)) {
				if(mkdir($nueva_ruta, 0755, true)){
					header("Location: ../carpetas.php?carpeta=" . urlencode($carpeta_actual));
					exit();
				} else {
					$error = 'ERROR: NO se pudo crear la carpeta. Verifique los permisos.';
				}
			} else {
				$error = 'ERROR: Ya existe una carpeta con ese nombre.';
			}
		} else {
			$error = 'ERROR: Debe proporcionar un nombre válido para la carpeta.';
		}
	}
?>
<!doctype html>
<html>
<head>
	<?php include_once('../partes/encabe.inc'); ?>
	<title>Crear Carpeta</title>
</head>
<body class="container cuerpo">
	<header class="row">
		<div class="row">
			<div class="col-lg-6 col-sm-6">
				<img  src="../imagenes/encabe.png" alt="logo institucional" width="100%">
			</div>
		</div>
		<div class="row">
			<?php include_once('../partes/menu.inc'); ?>
		</div>
		<br />
	</header>

	<main class="row">
		<div class="panel panel-primary datos1">
			<div class="panel-heading">
				<strong>Crear Nueva Carpeta</strong>
			</div>
			<div class="panel-body">
				<?php if(isset($error)) { ?>
					<div class="alert alert-danger"><?php echo $error; ?></div>
				<?php } ?>

				<form action="<?php echo $Accion_Formulario . '?carpeta=' . urlencode($carpeta_actual); ?>" method="post" name="frmCarpeta">
					<fieldset>
						<div class="form-group">
							<label><strong>Nombre de la Carpeta:</strong></label>
							<input name="txtNombreCarpeta" type="text" class="form-control" id="txtNombreCarpeta" size="60" placeholder="Ej: Mis_Documentos" required />
						</div>
						<div class="form-group">
							<input type="submit" name="Submit" value="Crear Carpeta" class="btn btn-success" />
							<a href="../carpetas.php?carpeta=<?php echo urlencode($carpeta_actual); ?>" class="btn btn-default">Cancelar</a>
						</div>
					</fieldset>
					<input type="hidden" name="OC_Aceptar" value="frmCarpeta" />
				</form>
			</div>
		</div>
	</main>

	<footer class="row">

	</footer>
	<?php include_once('../partes/final.inc'); ?>
</body>
</html>
