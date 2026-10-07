<?php
// Sesion, inactividad y perfil del usuario conectado.
class Sesion {
	const INACTIVIDAD = 2400;

	public static function iniciar(){
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
	}

	public static function vigente(){
		if (empty($_SESSION['loggedin']) || !isset($_SESSION['inicia'])) {
			return false;
		}
		if ((time() - $_SESSION['inicia']) > self::INACTIVIDAD) {
			session_unset();
			session_destroy();
			return false;
		}
		$_SESSION['inicia'] = time();
		return true;
	}

	public static function esAdmin(){
		return isset($_SESSION['Perfil']) && $_SESSION['Perfil'] == 1;
	}

	public static function idLiving(){
		return isset($_SESSION['IdLiving']) ? (int)$_SESSION['IdLiving'] : 0;
	}

	public static function dato($clave, $defecto = null){
		return isset($_SESSION[$clave]) ? $_SESSION[$clave] : $defecto;
	}

	// El login validado deja el correo aqui; la bienvenida lo consume una sola vez.
	public static function marcarLoginValidado($correo){
		session_regenerate_id(true);
		$_SESSION['login_validado'] = $correo;
	}

	public static function consumirLoginValidado(){
		if (empty($_SESSION['login_validado'])) {
			return '';
		}
		$correo = $_SESSION['login_validado'];
		unset($_SESSION['login_validado']);
		return $correo;
	}

	public static function abrir($correo, $usuario){
		$_SESSION['loggedin'] = true;
		$_SESSION['inicia'] = time();
		$_SESSION['login'] = $correo;
		$_SESSION['manager'] = true;
		$_SESSION['ultimoAcceso'] = date("Y-n-j H:i:s");
		$_SESSION['autenticado'] = "Y";
		$_SESSION['IdLiving'] = $usuario[0];
		$_SESSION['iniciales'] = substr($usuario[1], 0,1)."".substr($usuario[2], 0,1);
		$_SESSION['nombre'] = htmlspecialchars_decode($usuario[1])." ".htmlspecialchars_decode($usuario[2]);
		$_SESSION['usuario'] = "cliente";
		$_SESSION['Perfil'] = $usuario[22];
	}

	public static function cerrar(){
		session_unset();
		session_destroy();
	}
}
