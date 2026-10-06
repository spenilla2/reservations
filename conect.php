<?php 
// Las credenciales se leen de sirr-config.php, ubicado un nivel arriba de la carpeta publica.
$rutaConfig = dirname(__DIR__)."/sirr-config.php";
if(!is_file($rutaConfig)){
	error_log("SIRR: no se encuentra ".$rutaConfig);
	die("Error de configuraci&oacute;n");
}
$config = require($rutaConfig);
$conexion = new  mysqli($config['servername'],$config['user'],$config['password'],$config['database']);
if($conexion->connect_errno){
	error_log("SIRR: conexion fallida: ".$conexion->connect_error);
	die("Conexi&oacute;n Fallida");
}else{
  $conexion->set_charset("utf8");
}

// --- Saneo de entradas antes de armar consultas ---
// Numeros: siempre entero.
function entero($valor){
	return (int)$valor;
}
// Texto que va entre comillas en una consulta. Aplicar al final, despues de strtoupper/htmlspecialchars.
function texto($valor){
	global $conexion;
	return $conexion->real_escape_string((string)$valor);
}
// Lista de numeros separados por coma, para IN(...).
function listaEnteros($valor){
	return implode(",", array_map('intval', explode(",", (string)$valor)));
}
// Fechas recibidas por URL (YYYY-MM-DD o DD/MM/YYYY): solo digitos, guion y barra.
function fechaSegura($valor){
	return preg_match('/^[0-9\/\-]{1,10}$/', (string)$valor) ? (string)$valor : '';
}
// Codigos cortos (mesa, tipo): solo letras y numeros.
function codigo($valor){
	return preg_replace('/[^A-Za-z0-9]/', '', (string)$valor);
}
// Registra el error de base de datos sin mostrarlo al usuario.
function errorBD(){
	global $conexion;
	error_log("SIRR SQL: ".$conexion->error);
	return "Error en la base de datos";
}
?>
