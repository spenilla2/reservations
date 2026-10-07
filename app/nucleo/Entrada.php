<?php
// Lectura y saneo de lo que llega por POST y GET.
class Entrada {
	public static function post($clave, $defecto = ''){
		return isset($_POST[$clave]) ? $_POST[$clave] : $defecto;
	}

	public static function get($clave, $defecto = ''){
		return isset($_GET[$clave]) ? $_GET[$clave] : $defecto;
	}

	public static function postEntero($clave){
		return (int)self::post($clave, 0);
	}

	public static function getEntero($clave){
		return (int)self::get($clave, 0);
	}

	// Lista de numeros separados por coma, para IN(...).
	public static function listaEnteros($valor){
		return implode(",", array_map('intval', explode(",", (string)$valor)));
	}

	// Fechas recibidas por URL (YYYY-MM-DD o DD/MM/YYYY): solo digitos, guion y barra.
	public static function fechaSegura($valor){
		return preg_match('/^[0-9\/\-]{1,10}$/', (string)$valor) ? (string)$valor : '';
	}

	// Codigos cortos (mesa, tipo): solo letras y numeros.
	public static function codigo($valor){
		return preg_replace('/[^A-Za-z0-9]/', '', (string)$valor);
	}

	// DD/MM/YYYY -> YYYY-MM-DD, tal como lo envian los formularios.
	public static function fechaDMYaYMD($valor){
		$valor = (string)$valor;
		return substr($valor, 6, 4)."-".substr($valor, 3, 2)."-".substr($valor, 0, 2);
	}
}
