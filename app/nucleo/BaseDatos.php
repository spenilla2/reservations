<?php
// Conexion unica a MySQL. Las credenciales se leen de sirr-config.php, un nivel arriba de la carpeta publica.
class BaseDatos {
	private static $instancia = null;
	private $conexion;

	private function __construct(){
		$rutaConfig = dirname(SIRR_RAIZ)."/sirr-config.php";
		if (!is_file($rutaConfig)) {
			error_log("SIRR: no se encuentra ".$rutaConfig);
			die("Error de configuraci&oacute;n");
		}
		$config = require($rutaConfig);
		$this->conexion = new mysqli($config['servername'], $config['user'], $config['password'], $config['database']);
		if ($this->conexion->connect_errno) {
			error_log("SIRR: conexion fallida: ".$this->conexion->connect_error);
			die("Conexi&oacute;n Fallida");
		}
		$this->conexion->set_charset("utf8");
	}

	public static function obtener(){
		if (self::$instancia === null) {
			self::$instancia = new BaseDatos();
		}
		return self::$instancia;
	}

	// SELECT devuelve Filas; INSERT/UPDATE/DELETE devuelve true; un fallo devuelve false y queda en el log.
	public function consulta($sql){
		$resultado = $this->conexion->query($sql);
		if ($resultado === false) {
			error_log("SIRR SQL: ".$this->conexion->error);
			return false;
		}
		if ($resultado === true) {
			return true;
		}
		$filas = array();
		while ($fila = $resultado->fetch_array()) {
			$filas[] = $fila;
		}
		$resultado->free();
		return new Filas($filas);
	}

	// Igual que consulta(), pero un fallo detiene la peticion.
	public function exigir($sql){
		$resultado = $this->consulta($sql);
		if ($resultado === false) {
			throw new ErrorBD("Error en la base de datos");
		}
		return $resultado;
	}

	public function preparar($sql){
		$stmt = $this->conexion->prepare($sql);
		if (!$stmt) {
			error_log("SIRR SQL: ".$this->conexion->error);
		}
		return $stmt;
	}

	// Texto que va entre comillas en una consulta. Aplicar al final, despues de strtoupper/htmlspecialchars.
	public function texto($valor){
		return $this->conexion->real_escape_string((string)$valor);
	}
}
