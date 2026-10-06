<?php
// Control de acceso comun: sesion, inactividad y perfil.
// No imprime nada al incluirse; cada pagina o endpoint decide que exigir.
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
if (!defined('SIRR_INACTIVIDAD')) {
	define('SIRR_INACTIVIDAD', 2400);
}

function sesionVigente(){
	if (empty($_SESSION['loggedin']) || !isset($_SESSION['inicia'])) {
		return false;
	}
	if ((time() - $_SESSION['inicia']) > SIRR_INACTIVIDAD) {
		session_unset();
		session_destroy();
		return false;
	}
	$_SESSION['inicia'] = time();
	return true;
}

function esAdmin(){
	return isset($_SESSION['Perfil']) && $_SESSION['Perfil'] == 1;
}

function idLivingSesion(){
	return isset($_SESSION['IdLiving']) ? (int)$_SESSION['IdLiving'] : 0;
}

// Para paginas: redirige al login si no hay sesion.
function exigirSesionPagina(){
	if (!sesionVigente()) {
		if (!headers_sent()) {
			header("location: index.html");
		}
		echo "<script>window.location='index.html';</script>";
		exit();
	}
}

// Para paginas de gestion: ademas exige perfil administrador.
function exigirAdminPagina(){
	exigirSesionPagina();
	if (!esAdmin()) {
		if (!headers_sent()) {
			header("location: main.php");
		}
		echo "<script>window.location='main.php';</script>";
		exit();
	}
}

// Para llamadas AJAX: responde con codigo de error en lugar de redirigir.
function exigirSesionAjax(){
	if (!sesionVigente()) {
		http_response_code(401);
		echo "<div class='alert alert-danger' role='alert'>Su sesi&oacute;n ha expirado. <a href='index.html'>Iniciar Sesi&oacute;n</a></div>";
		exit();
	}
}

function exigirAdminAjax(){
	exigirSesionAjax();
	if (!esAdmin()) {
		http_response_code(403);
		echo "<div class='alert alert-danger' role='alert'>No tiene permisos para realizar esta acci&oacute;n.</div>";
		exit();
	}
}

// --- Contrasenas ---
// El navegador envia la clave en Base64 (scripts.js); ese valor es el que se compara y se hashea.
// Solo se guarda con hash cuando la columna STRPASSWORD ya fue ampliada (sql/2026-10-05_claves.sql).
function columnaClaveAdmiteHash($conexion){
	static $admite = null;
	if ($admite === null) {
		$admite = false;
		$res = $conexion->query("SELECT CHARACTER_MAXIMUM_LENGTH
		                           FROM information_schema.COLUMNS
		                          WHERE TABLE_SCHEMA = DATABASE()
		                            AND TABLE_NAME = 'tblusuarios'
		                            AND COLUMN_NAME = 'STRPASSWORD'");
		if ($res && ($fila = $res->fetch_row())) {
			$admite = ((int)$fila[0] >= 60);
		}
	}
	return $admite;
}

function claveEsHash($guardada){
	$info = password_get_info((string)$guardada);
	return !empty($info['algo']);
}

function verificarClave($clave, $guardada){
	if (claveEsHash($guardada)) {
		return password_verify((string)$clave, (string)$guardada);
	}
	return hash_equals((string)$guardada, (string)$clave);
}

function claveParaGuardar($conexion, $clave){
	if (columnaClaveAdmiteHash($conexion)) {
		return password_hash((string)$clave, PASSWORD_DEFAULT);
	}
	return (string)$clave;
}

function actualizarClave($conexion, $idLiving, $clave){
	$nueva = claveParaGuardar($conexion, $clave);
	$idLiving = (int)$idLiving;
	$stmt = $conexion->prepare("UPDATE tblusuarios SET STRPASSWORD = ? WHERE NUMIDENTLIV = ?");
	if (!$stmt) {
		return false;
	}
	$stmt->bind_param("si", $nueva, $idLiving);
	$ok = $stmt->execute();
	$stmt->close();
	return $ok;
}

// Un cliente solo puede tocar sus propias reservas; el administrador, cualquiera.
function reservaPermitida($conexion, $numReserva){
	if (esAdmin()) {
		return true;
	}
	$numReserva = (int)$numReserva;
	$idLiving = idLivingSesion();
	$stmt = $conexion->prepare("SELECT 1 FROM tblreservas WHERE NUMRESERVA = ? AND NUMIDENTLIV = ?");
	if (!$stmt) {
		return false;
	}
	$stmt->bind_param("ii", $numReserva, $idLiving);
	$stmt->execute();
	$stmt->store_result();
	$permitida = $stmt->num_rows > 0;
	$stmt->close();
	return $permitida;
}
?>
