<?php
    //Inicio la sesión
    session_start();

    //Utiliza los datos de sesion comprueba que el usuario este autenticado
    if ($_SESSION["autenticado"] != "SI") {
       header("Location: index.php");
        exit(); //fin del scrip
    }

	// Obtener carpeta actual
	$carpeta_actual = isset($_GET['carpeta']) ? $_GET['carpeta'] : '';
	$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);

	//declara ruta carpeta del usuario
	$base_ruta = "d:\\mybox";
    $ruta = $base_ruta.'/'.$_SESSION["usuario"];

	if($carpeta_actual != '') {
		$ruta .= '/' . $carpeta_actual;
	}

	$error = '';
	$Accion_Formulario = $_SERVER['PHP_SELF'];
    if ((isset($_POST["OC_Aceptar"])) && ($_POST["OC_Aceptar"] == "frmArchi")) {
		// Verificar que se haya subido un archivo
		if(isset($_FILES['txtArchi']) && $_FILES['txtArchi']['error'] == 0) {
			$Sali = $_FILES['txtArchi']['name'];
			$tamano_archivo = $_FILES['txtArchi']['size'];

			// VALIDACIÓN: Máximo 20MB (20 * 1024 * 1024 bytes)
			$tamano_maximo = 20 * 1024 * 1024; // 20 MB en bytes

			if($tamano_archivo > $tamano_maximo) {
				$tamano_mb = round($tamano_archivo / (1024 * 1024), 2);
				$error = "El archivo es demasiado grande ($tamano_mb MB). El tamaño máximo permitido es 20 MB.";
			} else {
				$Sali = str_replace(' ','_',$Sali);

				$ruta_destino = $ruta . '/' . $Sali;

				if(move_uploaded_file($_FILES['txtArchi']['tmp_name'], $ruta_destino)) {
					if(chmod($ruta_destino, 0644)){
						header("Location: carpetas.php?carpeta=" . urlencode($carpeta_actual));
						exit();
					} else {
						$error = 'No se pudo cambiar los permisos, consulte a su administrador';
					}
				} else {
					$error = 'No se pudo subir el archivo. Verifique los permisos de la carpeta.';
				}
			}
		} else {
			$error = 'Error al subir el archivo. Por favor intente nuevamente.';
		}
   }
?>
<!doctype html>
<html>
<head>
	<?php include_once('partes/encabe.inc'); ?>
    <title>Agregar archivos.</title>
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
		<div class="panel panel-primary datos1">
			<div class="panel-heading">
				<strong>Subir Archivo</strong>
			</div>
			<div class="panel-body">
				<?php if($error != '') { ?>
					<div class="alert alert-danger">
						<strong>Error:</strong> <?php echo $error; ?>
					</div>
				<?php } ?>

				<div class="alert alert-info">
					<strong>Límite de tamaño:</strong> El archivo no debe exceder los 20 MB.
				</div>

				<form action="<?php echo $Accion_Formulario . '?carpeta=' . urlencode($carpeta_actual); ?>" method="post" enctype="multipart/form-data" name="frmArchi" onsubmit="return validarArchivo();">
					<fieldset>
						<div class="form-group">
           					<label><strong>Seleccionar Archivo (máximo 20 MB):</strong></label>
							<input name="txtArchi" type="file" id="txtArchi" class="form-control" required />
							<small class="form-text text-muted">Tamaño máximo permitido: 20 MB</small>
						</div>
						<div class="form-group">
           					<input type="submit" name="Submit" value="Cargar Archivo" class="btn btn-primary" />
							<a href="carpetas.php?carpeta=<?php echo urlencode($carpeta_actual); ?>" class="btn btn-default">Cancelar</a>
						</div>
         			</fieldset>
         			<input type="hidden" name="OC_Aceptar" value="frmArchi" />
      			</form>
			</div>
		</div>

		<script>
		function validarArchivo() {
			var archivo = document.getElementById('txtArchi');
			if(archivo.files.length > 0) {
				var tamano = archivo.files[0].size;
				var tamanoMB = tamano / (1024 * 1024);
				if(tamanoMB > 20) {
					alert('El archivo es demasiado grande (' + tamanoMB.toFixed(2) + ' MB). El tamaño máximo permitido es 20 MB.');
					return false;
				}
			}
			return true;
		}
		</script>
    </main>

    <footer class="row">

    </footer>
	<?php include_once('partes/final.inc'); ?>
</body>
</html>
