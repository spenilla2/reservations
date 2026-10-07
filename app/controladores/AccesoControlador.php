<?php
// Registro, activacion, inicio y cierre de sesion, y recuperacion de contrasena.
class AccesoControlador extends Controlador {
	const URL_SITIO = "http://reservas.livingnightclub.com.co";

	// registro.php
	public function formularioRegistro(){
		$catalogo = new Catalogo();
		$this->vista('acceso/registro', array(
			'ocupaciones' => $catalogo->ocupaciones(),
			'tiposRumba' => $catalogo->tiposRumba(),
		));
	}

	// guard=R
	public function registrar(){
		$usuarios = new Usuario();
		$Nombre = Entrada::post("name");
		$Apellido = Entrada::post("apell");
		$Correo = strtolower(Entrada::post("mail"));
		$creado = $usuarios->crear(array(
			'nombre' => $Nombre,
			'apellido' => $Apellido,
			'genero' => Entrada::post("genero"),
			'fechaNacimiento' => date('Y-m-d', strtotime(Entrada::post("fechanac"))),
			'correo' => $Correo,
			'ocupacion' => Entrada::postEntero("Ocupacion"),
			'telefono' => Entrada::post("telefono"),
			'tipoRumba' => Entrada::postEntero("trumba"),
			'clave' => Entrada::post("pass"),
			'noticias' => Entrada::post("news") == "S" ? "T" : "F",
			'fechaIngreso' => date("Y-m-d"),
		));
		if ($creado == 1) {
			$idUsuario = $usuarios->idPorCorreo($Correo);
			$datos = compact('Nombre', 'Apellido', 'Correo', 'idUsuario');
			Correo::enviar($Correo, 'Creacion de Cuenta Living Night Club', $this->capturar('correos/registro', $datos));
			$this->vista('acceso/registro_resultado', $datos);
		}
	}

	// active.php
	public function activar(){
		$usuarios = new Usuario();
		$id = Entrada::getEntero('id');
		if ($usuarios->estaActivo($id)) {
			$estado = 'activa';
		} else {
			$estado = $usuarios->activar($id, date("Y-m-d H:i:s")) ? 'activada' : 'error';
		}
		$this->vista('acceso/activar', array('estado' => $estado));
		$this->vista('plantilla/pie');
	}

	// guard=M: cuantas cuentas usan ese correo
	public function existeCorreo(){
		$usuarios = new Usuario();
		echo $usuarios->cuantosConCorreo(Entrada::post('correo'));
	}

	// guard=P: valida la clave y deja el login listo para bienvenida()
	public function login(){
		$usuarios = new Usuario();
		$user = strtolower(Entrada::post('user'));
		$passc = Entrada::post('passC');
		$credenciales = $usuarios->credenciales($user);
		if (!$credenciales) {
			echo "E1";
			return;
		}
		list($idUsuario, $passGuardado, $activo) = $credenciales;
		if ($passc == "" || !$usuarios->verificarClave($passc, $passGuardado)) {
			echo "E2";
			return;
		}
		// las claves en formato anterior pasan a hash en el primer ingreso correcto
		if (!$usuarios->claveEsHash($passGuardado) && $usuarios->columnaAdmiteHash()) {
			$usuarios->actualizarClave($idUsuario, $passc);
		}
		if ($activo == 1) {
			Sesion::marcarLoginValidado($user);
			echo "UO";
		} else {
			echo "E3";
		}
	}

	// welcome.php: crea la sesion solo si login() acaba de validar al usuario; nunca desde la URL
	public function bienvenida(){
		date_default_timezone_set('America/Bogota');
		$user = Sesion::consumirLoginValidado();
		if ($user == '') {
			$this->redirigir("index.html");
		}
		$usuarios = new Usuario();
		Sesion::abrir($user, $usuarios->conPerfil($user));
		$usuarios->registrarIngreso($user, date("Y-m-d H:i:s"));
		$this->redirigir("main.php");
	}

	// desconexion.php
	public function salir(){
		Sesion::cerrar();
		$this->vista('acceso/salir');
	}

	// rpas.php
	public function formularioRecuperar(){
		$this->vista('acceso/recuperar');
	}

	// guard=RCE: envia un enlace de un solo uso; la respuesta no revela si el correo existe
	public function recuperar(){
		$usuarios = new Usuario();
		$correo = strtolower(Entrada::post('mail'));
		$resultado = $usuarios->porCorreo($correo);
		if (!$resultado) {
			$enviado = 1;
		} else {
			$enlaces = new RecuperaClave();
			$token = $enlaces->crear($resultado[0]);
			if ($token == '') {
				$enviado = 0;
			} else {
				$html = $this->capturar('correos/recuperar', array(
					'Nombre' => $resultado[1],
					'Apellido' => $resultado[2],
					'correo' => $correo,
					'enlace' => self::URL_SITIO."/restablecer.php?token=".$token,
				));
				$enviado = Correo::enviar($correo, 'Recuperacion de Cuenta en Living Night Club', $html);
			}
		}
		$this->vista('acceso/recuperar_resultado', compact('enviado', 'correo'));
	}

	// restablecer.php
	public function formularioRestablecer(){
		$enlaces = new RecuperaClave();
		$token = (string)Entrada::get('token');
		$this->vista('acceso/restablecer', array(
			'token' => $token,
			'vigente' => $enlaces->usuarioDe($token) > 0,
		));
	}

	// guard=RST
	public function restablecer(){
		$enlaces = new RecuperaClave();
		$usuarios = new Usuario();
		$nuevaClave = (string)Entrada::post('pass');
		$idUsuario = $enlaces->usuarioDe(Entrada::post('token'));
		if ($idUsuario > 0 && $nuevaClave != "" && $usuarios->actualizarClave($idUsuario, $nuevaClave)) {
			// el enlace deja de servir, junto con cualquier otro pendiente del mismo usuario
			$enlaces->borrarDe($idUsuario);
			echo "OK";
		} else {
			echo "E1";
		}
	}

	// hdata.php
	public function condiciones(){
		$this->vista('acceso/condiciones');
	}
}
