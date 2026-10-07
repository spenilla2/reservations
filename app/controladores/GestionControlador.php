<?php
// Gestion de reservas por parte del administrador: confirmar llegadas, asignar mesas, levantar y listar.
class GestionControlador extends Controlador {
	// ConfReserva.php
	public function paginaConfirmar(){
		$this->pagina('gestion/confirmar');
	}

	// AsignaMesa.php
	public function paginaAsignar(){
		$this->pagina('gestion/asignar', array('Fecha' => date('Y-m-d')));
	}

	// levantaReservas.php
	public function paginaLevantar(){
		$this->pagina('gestion/levantar', array('Fecha' => date('Y-m-d')));
	}

	// ListarReservas.php: tipo 1 todas las reservas, otro valor solo las que aplican cortesia
	public function paginaListar(){
		$Tipo = Entrada::getEntero('tipo');
		$this->pagina('gestion/listar', array(
			'Tipo' => $Tipo,
			'Mensaje' => $Tipo == 1 ? "Consultar Reservas" : "Consultar Reservas Efectivas para Cortesia",
			'Fecha' => date('Y-m-d'),
		));
	}

	// guard=CR: datos de una reserva antes de confirmar la llegada
	public function detalle(){
		$reservas = new Reserva();
		$this->vista('gestion/detalle_reserva', array('Consulta' => $reservas->detalle(Entrada::postEntero("NumReserva"))));
	}

	// guard=CRE: el cliente llego; la reserva pasa a efectiva y sus mesas a confirmadas
	public function confirmar(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Reserva = Entrada::postEntero("NumReserva");
		$queryMR = $mesas->marcarConfirmadas($Reserva);
		$reservas->marcarEfectiva($Reserva, date("Y-m-d H:i:s"));
		$this->vista('gestion/reserva_confirmada', array('queryMR' => $queryMR));
	}

	// guard=CRF: reservas del dia que aun no tienen mesa
	public function pendientesDeMesa(){
		$reservas = new Reserva();
		$Fecha = Entrada::fechaDMYaYMD(Entrada::post("Fecha"));
		$this->vista('gestion/pendientes_mesa', array(
			'Consulta' => $reservas->pendientesDeMesa($Fecha),
			'Fecha' => htmlspecialchars($Fecha),
		));
	}

	// guard=CAM: un selector de mesa libre por cada mesa que necesita la reserva
	public function selectorMesas(){
		$mesas = new MesaReserva();
		$this->vista('gestion/selector_mesas', array(
			'CantMesas' => Entrada::postEntero("cantMesas"),
			'mesasLibres' => $mesas->libres(Entrada::fechaDMYaYMD(Entrada::post("fecha"))),
		));
	}

	// guard=AMR: asigna mesas a una reserva activa (normal o especial)
	public function asignarMesas(){
		$tipo = Entrada::post("REspecial") == "true" ? "E" : "N";
		$this->guardarAsignacion($tipo);
	}

	// guard=AMR1: igual, pero el tipo depende del estado de la reserva (3 = ya efectiva, queda confirmada)
	public function asignarMesasSegunEstado(){
		$tipo = "N";
		$estado = Entrada::post("Estado");
		if ($estado == '1') {
			$tipo = Entrada::post("TRes") == "true" ? "E" : "N";
		} elseif ($estado == '3') {
			$tipo = "C";
		}
		$this->guardarAsignacion($tipo);
	}

	private function guardarAsignacion($tipo){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$FechaReserva = Entrada::fechaDMYaYMD(Entrada::post("Fecha"));
		$Reserva = Entrada::postEntero("Reserva");
		$CMesas = Entrada::postEntero("CMesas");
		$MesasAsigna = explode(",", Entrada::post("AMesas"));
		$FechaRealizaAsigna = date("Y-m-d H:i:s");
		for ($i = 0; $i < $CMesas; $i++) {
			$mesas->asignar($FechaReserva, $Reserva, isset($MesasAsigna[$i]) ? $MesasAsigna[$i] : '', $FechaRealizaAsigna, $tipo);
		}
		$this->vista('gestion/mesas_asignadas', array('query' => $reservas->guardarMesas($Reserva, Entrada::post("AMesas"))));
	}

	// guard=LRes: reservas que no llegaron y se pueden levantar
	public function paraLevantar(){
		$reservas = new Reserva();
		$FechaC = Entrada::fechaDMYaYMD(Entrada::post("Fecha"));
		$Consulta = $reservas->paraLevantar($FechaC);
		$this->vista('gestion/para_levantar', array(
			'Consulta' => $Consulta,
			'nfilas' => $Consulta->num_rows,
			'FechaC' => htmlspecialchars($FechaC),
		));
	}

	// guard=LResScript: marca como perdidas las reservas del dia (menos las que se mantienen) y libera sus mesas
	public function levantar(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Fecha = Entrada::post("Fecha");
		$levantadas = $reservas->levantar($Fecha, Entrada::post("ResMan"));
		$mesasLiberadas = $levantadas ? $mesas->liberarPerdidas($Fecha) : false;
		$this->vista('gestion/levantadas', compact('levantadas', 'mesasLiberadas'));
	}

	// guard=BRF: listado de reservas de un dia
	public function listado(){
		$reservas = new Reserva();
		$Fecha = Entrada::post("fecha");
		$soloCortesia = Entrada::post("tipo") != 1;
		$Consulta1 = $reservas->listadoDelDia($Fecha, $soloCortesia);
		$this->vista('gestion/listado', array(
			'Consulta1' => $Consulta1,
			'n' => $Consulta1->num_rows,
			'Resu3' => $reservas->totalesDelDia($Fecha, $soloCortesia),
			'Fecha' => htmlspecialchars($Fecha),
		));
	}

	// guard=BR: busqueda por numero de reserva
	public function buscar(){
		$reservas = new Reserva();
		$this->vista('gestion/busqueda', array('Consulta1' => $reservas->buscar(Entrada::postEntero("Reserva"))));
	}

	// guard=ConfEntrM: marca si la reserva aplica cortesia
	public function cortesia(){
		$reservas = new Reserva();
		$this->vista('gestion/cortesia', array(
			'Consulta' => $reservas->marcarCortesia(Entrada::postEntero('RNum'), Entrada::post('cumpli')),
		));
	}
}
