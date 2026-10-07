<?php
// Lo que hace cualquier usuario con sesion: crear, consultar, modificar y cancelar sus reservas.
class ReservaControlador extends Controlador {
	// main.php
	public function inicio(){
		$this->pagina('reservas/inicio', array('logoGrande' => true));
	}

	// reserva2.php: pantalla para elegir la fecha; el formulario llega despues por AJAX (formulario())
	public function nueva(){
		$this->pagina('reservas/nueva', array(
			'tipo' => Entrada::codigo(Entrada::get('Tipo')),
			'NumReserva' => 0,
			'NReserva' => Sesion::dato('nombre'),
			'Perfil' => Sesion::dato('Perfil'),
			'Idliving' => Sesion::dato('IdLiving'),
			'Fecha' => date('Y-m-d'),
		));
	}

	// guard=ResForm: formulario de reserva segun la fecha (dia de servicio, evento, festivo)
	public function formulario(){
		$reservas = new Reserva();
		$clientesVipModelo = new ClienteVip();
		$catalogo = new Catalogo();
		$eventos = new Evento();

		$fecha = Entrada::post('Fecha');
		// perfil e identidad salen de la sesion, no del formulario
		$Perfil = Sesion::dato('Perfil', 0);
		$IdLiving = Sesion::esAdmin() ? Entrada::postEntero('IdLiving') : Sesion::idLiving();
		$diasem = Fechas::diaSemana($fecha);

		$NombreBoton = "Reservar";
		$NombreReserva = "";
		$TipoCele = 5;
		if (Entrada::post('tipo') != "I") {
			$NumReserva = Entrada::postEntero('Rnum');
			if (!$this->reservaPermitida($NumReserva)) {
				echo '<p class="perror">No tiene permisos sobre esta reserva</p>';
				return;
			}
			$Result = $reservas->porId($NumReserva);
			$NombreBoton = "Actualizar reserva";
			$NombreReserva = $Result[3];
			$TipoCele = $Result[5];
		}

		// Que se le muestra al usuario para esa fecha:
		//   'pasada' la fecha ya paso; 'sin_servicio' domingo que no es festivo;
		//   'evento' hay un evento que no maneja reservas; 'formulario' se puede reservar.
		$Fecha1 = Entrada::fechaDMYaYMD($fecha);
		$evento = $eventos->enFecha($Fecha1);
		if (Fechas::diferenciaDias($fecha, date('d/m/Y')) < 0) {
			$estado = 'pasada';
		} elseif ($evento && $evento['reservas'] != "S") {
			$estado = 'evento';
		} elseif ($diasem == 0 && !$eventos->esFestivo($Fecha1)) {
			$estado = 'sin_servicio';
		} else {
			$estado = 'formulario';
		}

		$clientesVip = $clientesVipModelo->deUsuario($IdLiving);
		$this->vista('reservas/formulario', array(
			'estado' => $estado,
			'fecha' => htmlspecialchars($fecha),
			'fechaTexto' => Fechas::textoLargo($fecha),
			'nombreEvento' => $evento ? $evento['nombre'] : "",
			'aficheFrente' => $evento ? EventoControlador::rutaImagen($evento['frente']) : "",
			'afichePrecios' => $evento ? EventoControlador::rutaImagen($evento['precios']) : "",
			'esAdmin' => $Perfil == 1,
			'NombreReserva' => $NombreReserva,
			'NombreBoton' => $NombreBoton,
			'TipoCele' => $TipoCele,
			// al administrador no se le marca por defecto el envio de la confirmacion
			'recibeCorreo' => $Perfil != 1,
			'clientesVip' => $clientesVip ? $clientesVip->todas() : array(),
			'tiposCelebracion' => $catalogo->tiposCelebracion()->todas(),
		));
	}

	// guard=RR. Respuestas: "EI1" ya existe una igual, "IC1;<numero>" creada, "False" error
	public function crear(){
		$reservas = new Reserva();
		$IdLiving = Entrada::postEntero('id');
		$ClienteVip = Entrada::postEntero("ClienteVIP");
		if (!Sesion::esAdmin()) {
			// un cliente solo reserva a su nombre y sin cliente VIP
			$IdLiving = Sesion::idLiving();
			$ClienteVip = 0;
		}
		$NombreRes = Entrada::post("NombreRese");
		$NombreHomena = Entrada::post("NHomena");
		$TipoReserva = Entrada::postEntero("TipoR");
		$FechaReserva = Entrada::fechaDMYaYMD(Entrada::post("Fecha"));

		if ($reservas->existeIgual($IdLiving, $FechaReserva, $NombreRes, $NombreHomena, $TipoReserva)) {
			echo "EI1";
			return;
		}
		$creada = $reservas->crear($FechaReserva, $IdLiving, $NombreRes, Entrada::postEntero("Numper"), $TipoReserva,
		                           $NombreHomena, Entrada::post("Observa"), date("Y-m-d H:i:s"), 1, '', $ClienteVip);
		echo $creada ? "IC1;".$reservas->ultimaDe($IdLiving, $FechaReserva) : "False";
	}

	// guard=RU. Respuestas: "UC1;<numero>" actualizada, "False" error o sin permiso
	public function actualizar(){
		$reservas = new Reserva();
		$Reserva = Entrada::postEntero('NumReserva');
		if (!$this->reservaPermitida($Reserva)) {
			echo "False";
			return;
		}
		$actualizada = $reservas->actualizar($Reserva, Entrada::post("NombreRese"), Entrada::postEntero("Numper"),
		                                     Entrada::postEntero("TipoR"), Entrada::post("NHomena"), Entrada::post("Observa"));
		echo $actualizada ? "UC1;".$Reserva : "False";
	}

	// guard=E (enlace GET)
	public function cancelar(){
		$reservas = new Reserva();
		$Reserva = Entrada::getEntero('Reserva');
		$listado = Sesion::esAdmin() ? "ListarReservas.php?tipo=1" : "consultaReservas.php";
		if (!$this->reservaPermitida($Reserva)) {
			$this->vista('reservas/aviso', array('mensaje' => "No tiene permisos sobre esta reserva!", 'destino' => "consultaReservas.php"));
		} elseif ($reservas->cancelar($Reserva)) {
			$this->vista('reservas/aviso', array('mensaje' => "Reserva Cancelada Satisfactoriamente!", 'destino' => $listado));
		} else {
			$this->vista('reservas/aviso', array('mensaje' => "Ha Ocurrido un Error!", 'destino' => $listado));
		}
	}

	// consultaReservas.php
	public function consultar(){
		$reservas = new Reserva();
		$query = $reservas->vigentesDe(Sesion::idLiving());
		$this->pagina('reservas/consulta', array(
			'Perfil' => Sesion::dato('Perfil'),
			'query' => $query,
			'n' => $query->num_rows,
		));
	}

	// reserva.php: formulario de modificacion
	public function editar(){
		$reservas = new Reserva();
		$clientesVipModelo = new ClienteVip();
		$catalogo = new Catalogo();

		$NumReserva = Entrada::getEntero('Rnum');
		if (!$this->reservaPermitida($NumReserva)) {
			echo "<script>window.location='consultaReservas.php';</script>";
			return;
		}
		$Result = $reservas->porId($NumReserva);
		$esAdmin = Sesion::esAdmin();
		$clientesVip = $clientesVipModelo->deUsuario(Sesion::idLiving());
		$this->pagina('reservas/editar', array(
			'IdLiving' => Sesion::dato('IdLiving'),
			'NReserva' => Sesion::dato('nombre'),
			'Perfil' => Sesion::dato('Perfil'),
			'tipo' => Entrada::codigo(Entrada::get('Tipo')),
			'NumReserva' => $NumReserva,
			// la fecha se toma de la reserva guardada (YYYY-MM-DD), no de la URL
			'fechaTexto' => Fechas::textoLargo(substr($Result[1], 8, 2)."/".substr($Result[1], 5, 2)."/".substr($Result[1], 0, 4)),
			'mesas' => $Result[11],
			'NombreReserva' => $Result[3],
			'NumPer' => (int)$Result[4],
			'TipoCele' => $Result[5],
			'NomH' => $Result[6],
			'RObservac' => $Result[7],
			'esAdmin' => $esAdmin,
			// con mesa ya asignada no se cambia el cliente VIP
			'vipBloqueado' => Entrada::codigo(Entrada::get('MA')) != "",
			// al administrador no se le marca por defecto el envio de la confirmacion
			'recibeCorreo' => !$esAdmin,
			'clientesVip' => $clientesVip ? $clientesVip->todas() : array(),
			'tiposCelebracion' => $catalogo->tiposCelebracion()->todas(),
		));
	}

	// correoReserva.php: muestra y envia la confirmacion de la reserva, y vuelve a la pantalla de reservas
	public function correo(){
		$reservas = new Reserva();
		$Reserva = Entrada::getEntero('reserva');
		if (!$this->reservaPermitida($Reserva)) {
			$this->redirigir("main.php");
		}
		$resultado = $reservas->datosParaCorreo($Reserva);
		$html = $this->capturar('correos/reserva', array(
			'Reserva' => $Reserva,
			'NombreUser' => $resultado[1],
			'NombreReserva' => $resultado[2],
			'Personas' => $resultado[3],
			'Motivo' => $resultado[4],
			'Homenajeados' => $resultado[5],
			'FechaReserva' => $resultado[6],
			'Observaciones' => $resultado[8],
		));
		echo $html;
		$enviado = Correo::enviar($resultado[0], 'Confirmacion de Reserva para el '.$resultado[6], $html,
		                          'info@livingnight.com.co', 'santop_3@hotmail.com');
		if ($enviado) {
			echo '<script>location.href="reserva2.php?Tipo=I";</script>Email enviado correctamente'.$resultado[0];
		} else {
			echo 'Error en el envío del email';
		}
		header('Location: reserva2.php?Tipo=I');
	}
}
