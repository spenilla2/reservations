<?php
// Clientes VIP: alta, modificacion y busqueda. Solo administradores.
class ClienteVipControlador extends Controlador {
	// guard=RCV
	public function crear(){
		$clientes = new ClienteVip();
		$confirmado = Entrada::post("Confirma") == "T";
		$name = Entrada::post("name");
		$creado = false;
		if ($confirmado) {
			$FechaCreacion = date("Y-m-d H:i:s");
			$creado = $clientes->crear($name, Entrada::post("genero"), Entrada::postEntero("identLiv"), Entrada::postEntero("trumba"),
			                           Entrada::post("telefono"), $FechaCreacion, "[".$FechaCreacion."]: ".Entrada::post("Observa"));
		}
		$this->vista('vip/creado', array('confirmado' => $confirmado, 'creado' => $creado, 'name' => htmlspecialchars($name)));
	}

	// guard=ACV: cada observacion nueva se agrega al historial con fecha y autor
	public function actualizar(){
		$clientes = new ClienteVip();
		$usuarios = new Usuario();
		$observaciones = Entrada::post("VObserva");
		$nueva = Entrada::post("NObserva");
		if ($nueva != "") {
			$autor = $usuarios->nombreCompleto(Entrada::postEntero("IdLiving"));
			$observaciones .= "<br>"."[<b>".date("Y-m-d H:i:s")." - ".$autor."</b>]: ".$nueva;
		}
		echo $clientes->actualizar(Entrada::postEntero("IdClienteVip"), Entrada::post("Nombre"), Entrada::post("Genero"),
		                           Entrada::postEntero("TRumba"), Entrada::post("Telefono"), $observaciones);
	}

	// guard=BCVIP
	public function buscar(){
		$clientes = new ClienteVip();
		$Consulta = $clientes->buscarPorNombre(Entrada::post("Nom"));
		$this->vista('vip/busqueda', array(
			'Consulta' => $Consulta,
			// el telefono solo se muestra al usuario que registro al cliente; este filtro nunca recibio el usuario
			'IdLiving' => null,
		));
	}
}
