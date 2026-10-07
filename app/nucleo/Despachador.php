<?php
// Une cada archivo de entrada (o cada accion de Querys.php) con su controlador segun app/rutas.php,
// y aplica el nivel de acceso de la ruta antes de ejecutarlo.
class Despachador {
	// Paginas: el nombre del archivo de entrada decide la ruta.
	public static function pagina($archivo){
		$rutas = require(SIRR_APP."/rutas.php");
		$ruta = $rutas['paginas'][basename($archivo)];
		Sesion::iniciar();
		if ($ruta[2] == 'admin') {
			self::exigirSesionPagina();
			if (!Sesion::esAdmin()) {
				self::salir("main.php");
			}
		} elseif ($ruta[2] == 'sesion') {
			self::exigirSesionPagina();
		}
		self::ejecutar($ruta);
	}

	// Acciones AJAX y formularios: el parametro guard decide la ruta.
	public static function accion(){
		$rutas = require(SIRR_APP."/rutas.php");
		$guard = Entrada::post('guard');
		if ($guard == "") {
			$guard = Entrada::get('guard');
		}
		$ruta = isset($rutas['acciones'][$guard]) ? $rutas['acciones'][$guard] : null;
		// una accion desconocida se trata como de gestion: nunca queda abierta
		$nivel = $ruta ? $ruta[2] : 'admin';
		Sesion::iniciar();
		if ($nivel != 'publico') {
			if (!Sesion::vigente()) {
				http_response_code(401);
				echo "<div class='alert alert-danger' role='alert'>Su sesi&oacute;n ha expirado. <a href='index.html'>Iniciar Sesi&oacute;n</a></div>";
				exit();
			}
			if ($nivel == 'admin' && !Sesion::esAdmin()) {
				http_response_code(403);
				echo "<div class='alert alert-danger' role='alert'>No tiene permisos para realizar esta acci&oacute;n.</div>";
				exit();
			}
		}
		if ($ruta) {
			self::ejecutar($ruta);
		}
	}

	private static function ejecutar($ruta){
		$controlador = new $ruta[0]();
		try {
			$controlador->{$ruta[1]}();
		} catch (ErrorBD $e) {
			die($e->getMessage());
		}
	}

	private static function exigirSesionPagina(){
		if (!Sesion::vigente()) {
			self::salir("index.html");
		}
	}

	private static function salir($destino){
		if (!headers_sent()) {
			header("location: ".$destino);
		}
		echo "<script>window.location='".$destino."';</script>";
		exit();
	}
}
