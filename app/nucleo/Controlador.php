<?php
// Base de los controladores: reciben la peticion, llaman a los modelos y entregan los datos a una vista.
abstract class Controlador {
	// Muestra una vista de app/vistas con las variables indicadas.
	protected function vista($nombre, array $datos = array()){
		extract($datos);
		require(SIRR_APP."/vistas/".$nombre.".php");
	}

	// Vista dentro de la plantilla comun (barra de navegacion y pie).
	protected function pagina($nombre, array $datos = array()){
		$this->vista('plantilla/cabecera', array(
			'Perfil' => Sesion::dato('Perfil'),
			'iniciales' => Sesion::dato('iniciales'),
			'logoGrande' => !empty($datos['logoGrande']),
		));
		$this->vista($nombre, $datos);
		$this->vista('plantilla/pie');
	}

	// Devuelve el HTML de una vista en lugar de imprimirlo (cuerpos de correo).
	protected function capturar($nombre, array $datos = array()){
		ob_start();
		$this->vista($nombre, $datos);
		return ob_get_clean();
	}

	// Un cliente solo puede tocar sus propias reservas; el administrador, cualquiera.
	protected function reservaPermitida($numReserva){
		if (Sesion::esAdmin()) {
			return true;
		}
		$reservas = new Reserva();
		return $reservas->perteneceA($numReserva, Sesion::idLiving());
	}

	protected function redirigir($destino){
		header("location: ".$destino);
		exit();
	}
}
