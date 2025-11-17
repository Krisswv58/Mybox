<?php
	//Inicio la sesión
	session_start();

	//Utiliza los datos de sesion comprueba que el usuario este autenticado
	if($_SESSION["autenticado"] != "SI") {
		header("Location: index.php");
		exit();
	}

	$carpeta_actual = isset($_GET['carpeta']) ? $_GET['carpeta'] : '';
	$carpeta_actual = str_replace(['..', '/', '\\\\'], '', $carpeta_actual);

	$base_ruta = "d:\\mybox";
	$mi_carpeta = $base_ruta . '\\' . $_SESSION["usuario"];

	if($carpeta_actual != '') {
		$ruta_completa = $mi_carpeta . '\\' . $carpeta_actual;
	} else {
		$ruta_completa = $mi_carpeta;
	}

	// Directorio donde se guardarán los archivos compartidos
	$archivo_compartidos = $base_ruta . '\\compartidos.json';

	// Cargar compartidos existentes
	$compartidos = array();
	if(file_exists($archivo_compartidos)) {
		$contenido = file_get_contents($archivo_compartidos);
		$compartidos = json_decode($contenido, true);
		if(!is_array($compartidos)) {
			$compartidos = array();
		}
	}

	$mensaje = '';
	$error = '';

	// Procesar formulario de compartir
	if(isset($_POST['compartir'])) {
		$usuario_destino = trim($_POST['usuario_destino']);
		$elemento = $_POST['elemento'];

		// Validar que el usuario destino existe
		$ruta_destino = $base_ruta . '\\' . $usuario_destino;

		if($usuario_destino == $_SESSION["usuario"]) {
			$error = 'No puedes compartir archivos contigo mismo.';
		} else if(!file_exists($ruta_destino)) {
			$error = 'El usuario destino no existe.';
		} else {
			// Crear registro de compartido
			$compartido_id = uniqid();
			$compartidos[$compartido_id] = array(
				'propietario' => $_SESSION["usuario"],
				'usuario_destino' => $usuario_destino,
				'elemento' => $elemento,
				'carpeta' => $carpeta_actual,
				'fecha' => date('Y-m-d H:i:s'),
				'ruta_completa' => $ruta_completa . '\\' . $elemento
			);

			// Guardar en archivo JSON
			file_put_contents($archivo_compartidos, json_encode($compartidos, JSON_PRETTY_PRINT));
			$mensaje = 'Elemento compartido exitosamente con ' . htmlspecialchars($usuario_destino);
		}
	}

	// Obtener lista de archivos y carpetas en la ubicación actual
	$elementos = array();
	if(file_exists($ruta_completa)) {
		$directorio = opendir($ruta_completa);
		while($elem = readdir($directorio)){
			if(($elem!='.') and ($elem!='..')){
				$elementos[] = $elem;
			}
		}
		closedir($directorio);
		sort($elementos);
	}

	// Obtener archivos compartidos CONMIGO
	$compartidos_conmigo = array();
	foreach($compartidos as $id => $comp) {
		if($comp['usuario_destino'] == $_SESSION["usuario"]) {
			$compartidos_conmigo[$id] = $comp;
		}
	}
?>
<!doctype html>
<html>
<head>
	<?php include_once('partes/encabe.inc'); ?>
	<title>Compartir Archivos</title>
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
				<strong>Compartir Archivos y Carpetas</strong>
			</div>
			<div class="panel-body">
				<?php if($mensaje != '') { ?>
					<div class="alert alert-success"><?php echo $mensaje; ?></div>
				<?php } ?>
				<?php if($error != '') { ?>
					<div class="alert alert-danger"><?php echo $error; ?></div>
				<?php } ?>

				<h4>Compartir desde: <?php echo $carpeta_actual != '' ? htmlspecialchars($carpeta_actual) : 'Raíz'; ?></h4>

				<?php if(count($elementos) > 0) { ?>
					<form method="post" class="form-inline">
						<div class="form-group">
							<label>Elemento a compartir:</label>
							<select name="elemento" class="form-control" required>
								<option value="">-- Seleccione --</option>
								<?php foreach($elementos as $elem) { ?>
									<option value="<?php echo htmlspecialchars($elem); ?>"><?php echo htmlspecialchars($elem); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label>Usuario destino:</label>
							<input type="text" name="usuario_destino" class="form-control" placeholder="nombre_usuario" required />
						</div>
						<button type="submit" name="compartir" class="btn btn-primary">Compartir</button>
						<a href="carpetas.php?carpeta=<?php echo urlencode($carpeta_actual); ?>" class="btn btn-default">Volver</a>
					</form>
				<?php } else { ?>
					<div class="alert alert-warning">No hay elementos para compartir en esta ubicación.</div>
					<a href="carpetas.php?carpeta=<?php echo urlencode($carpeta_actual); ?>" class="btn btn-default">Volver</a>
				<?php } ?>

				<hr>

				<h4>Archivos Compartidos Conmigo</h4>
				<?php if(count($compartidos_conmigo) > 0) { ?>
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Elemento</th>
								<th>Propietario</th>
								<th>Fecha</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($compartidos_conmigo as $id => $comp) { ?>
								<tr>
									<td><?php echo htmlspecialchars($comp['elemento']); ?></td>
									<td><?php echo htmlspecialchars($comp['propietario']); ?></td>
									<td><?php echo htmlspecialchars($comp['fecha']); ?></td>
									<td>
										<a href="ver_compartido.php?id=<?php echo $id; ?>" class="btn btn-sm btn-info">Ver/Descargar</a>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } else { ?>
					<div class="alert alert-info">No tienes archivos compartidos.</div>
				<?php } ?>
			</div>
		</div>
	</main>

	<footer class="row">

	</footer>
	<?php include_once('partes/final.inc'); ?>
</body>
</html>
