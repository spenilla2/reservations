<?php
// Tablero y mapa del salon: ocupacion de mesas por dia y operaciones sobre una mesa. Solo administradores.
class SalonControlador extends Controlador {
	// GestionSalon.php: pantalla para elegir la fecha del tablero
	public function paginaGestion(){
		$this->pagina('salon/gestion', array('Fecha' => date('Y-m-d')));
	}

	// Salon.php: plano estatico
	public function plano(){
		$this->vista('salon/plano');
	}

	// dashboard.php: tablero del dia con las reservas y el plano de mesas (la vista se recarga sola)
	public function dashboard(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$fecha = $this->fechaDeUrl();
		$this->vista('salon/dashboard', array(
			'fecha' => $fecha,
			'query' => $reservas->listadoDelDia($fecha),
			'mesasLibres' => $mesas->libres($fecha, true),
		));
	}

	// reservoir.php: mapa del salon con colores por tipo de asignacion; se recarga solo
	public function mapa(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$catalogo = new Catalogo();
		$fecha = $this->fechaDeUrl();
		$orden = Entrada::getEntero('orden');
		header("refresh:70;url=reservoir.php?fecha=".$fecha."&orden=".$orden);
		$consulta = $mesas->ocupacionConColor($fecha);
		$consulta1 = $catalogo->tiposAsignacionMapa();
		$this->vista('salon/mapa', array(
			'fecha' => $fecha,
			'consulta' => $consulta,
			'nfilas' => $consulta->num_rows,
			'consulta1' => $consulta1,
			'nfilas1' => $consulta1->num_rows,
			'resulres' => $reservas->activasConMesa($fecha),
			'reservasPorMesa' => $mesas->reservasActivasPorMesa($fecha, $orden),
		));
	}

	// GestionMesa.php: estado de una mesa en una fecha y lo que se puede hacer con ella
	public function mesa(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Mesa = $this->codigoMesa(Entrada::codigo(Entrada::get('mesa')));
		$Fecha = Entrada::fechaSegura(Entrada::get('fecha'));
		$asignacion = $mesas->asignacionDe($Mesa, $Fecha);
		$resultado = null;
		if ($asignacion->num_rows > 0) {
			$res = $asignacion->fetch_array();
			$resultado = $reservas->detalleEnMesa($res[2], $Mesa);
		}
		$queryMlibre = $mesas->libres($Fecha);
		$this->vista('salon/mesa', array(
			'Mesa' => $Mesa,
			'Fecha' => $Fecha,
			'IdLiving' => Sesion::dato('IdLiving'),
			'infoMesa' => $mesas->datos($Mesa),
			'nfilasMreservada' => $asignacion->num_rows,
			'resultado' => $resultado,
			'queryMlibre' => $queryMlibre,
			'nMesasLibres' => $queryMlibre->num_rows,
		));
	}

	// GestionMesaA.php: asignar mesas a una reserva desde el tablero
	public function reserva(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Reserva = Entrada::getEntero("reserva");
		$resultado = $reservas->detalle($Reserva)->fetch_array();
		$this->vista('salon/reserva', array(
			'resultado' => $resultado,
			'Mesa' => "",
			'Fecha' => "",
			'mesasLibres' => $mesas->libres($resultado[1]),
		));
	}

	// Listados.php: 'K' mesas levantadas, 'Cu' cumpleanos, o un tipo de asignacion
	public function listado(){
		$mesas = new MesaReserva();
		$fecha = Entrada::fechaSegura(Entrada::get('fecha'));
		$Consulta = $mesas->listado($fecha, Entrada::codigo(Entrada::get('tipo')));
		$this->vista('salon/listado', array('fecha' => $fecha, 'Consulta' => $Consulta, 'nfilas' => $Consulta->num_rows));
	}

	// ListaCumple.php
	public function cumpleanos(){
		$this->listadoPorMotivo(1, 'Cumplea&ntilde;os');
	}

	// ListaGrado.php
	public function grados(){
		$this->listadoPorMotivo(3, 'Grados');
	}

	private function listadoPorMotivo($motivo, $titulo){
		$reservas = new Reserva();
		$fecha = Entrada::fechaSegura(Entrada::get('fecha'));
		$query = $reservas->delDiaPorMotivo($fecha, $motivo);
		$this->vista('salon/listado_motivo', array('fecha' => $fecha, 'titulo' => $titulo, 'reservas' => $query->todas()));
	}

	// guard=TMCD: "<n>|0;<mesa>|<color>;..." para pintar el tablero. Las barras de un digito van como B01
	public function colores(){
		$mesas = new MesaReserva();
		$query = $mesas->coloresDelDia(Entrada::post('fecha'));
		$Cadena = $query->num_rows."|0;";
		foreach ($query->todas() as $res) {
			if (substr($res[0],0,1)=='B' && strlen($res[0])==2) {
				$res[0] = substr($res[0],0,1)."0".substr($res[0],1,1);
			}
			$Cadena .= $res[0]."|".$res[1].";";
		}
		echo $Cadena;
	}

	// guard=BMOD: "<n>;<mesa>;..." con las mesas ocupadas del dia
	public function ocupadas(){
		$mesas = new MesaReserva();
		echo $this->listaDeMesas($mesas->ocupadas(Entrada::post('fecha')));
	}

	// guard=STRE: mesas que quedan fuera del filtro elegido en el tablero
	public function filtro(){
		$mesas = new MesaReserva();
		echo $this->listaDeMesas($mesas->fueraDelFiltro(Entrada::post('fecha'), Entrada::postEntero('tipo')));
	}

	private function listaDeMesas($filas){
		if (!$filas) {
			return ";";
		}
		$Cadena = $filas->num_rows.";";
		foreach ($filas->todas() as $res) {
			$Cadena .= $res[0].";";
		}
		return $Cadena;
	}

	// guard=CCliVip: formulario segun a quien se le va a dar la mesa (1 VIP, 2 foraneo, 3 bloqueo, 4 compartida)
	public function opcionesAsignacion(){
		$clientes = new ClienteVip();
		$catalogo = new Catalogo();
		$Codigo = Entrada::postEntero("Cod");
		$this->vista('salon/opciones_asignacion', array(
			'Codigo' => $Codigo,
			'Fecha' => htmlspecialchars(Entrada::post("fecha")),
			'clientesVip' => $Codigo == 1 ? $clientes->todos() : null,
			'tiposCelebracion' => ($Codigo == 1 || $Codigo == 2) ? $catalogo->tiposCelebracion() : null,
		));
	}

	// guard=IRCVIP: crea una reserva ya efectiva directamente sobre una mesa libre
	public function reservaEnMesa(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Mesa = Entrada::post("Mesa");
		$FechaAsigna = Entrada::post("FechaRes");
		if (substr($FechaAsigna, 4,1) != '-') {
			$FechaAsigna = Entrada::fechaDMYaYMD($FechaAsigna);
		}
		$IdLiving = Entrada::postEntero("IdLiving");
		$TipoCelebracion = Entrada::postEntero("TipoCelebra");
		$ahora = date("Y-m-d H:i:s");

		if ($mesas->asignacionDe($Mesa, $FechaAsigna)->num_rows > 0) {
			$resultado = 'ocupada';
		} elseif (!$reservas->crear($FechaAsigna, $IdLiving, Entrada::post("Nombre"), Entrada::postEntero("NumPer"), $TipoCelebracion,
		                            Entrada::post("NombreH"), Entrada::post("Observa"), $ahora, 3, $Mesa, Entrada::postEntero("IdCliVIP"))) {
			$resultado = 'error';
		} else {
			// los cumpleanos siempre se marcan con su propio tipo de asignacion
			$TipoAsigna = $TipoCelebracion == 1 ? 'T' : Entrada::post("TipoAsigna");
			$asignada = $mesas->asignar($FechaAsigna, $reservas->ultimaDe($IdLiving), $Mesa, $ahora, $TipoAsigna);
			$resultado = $asignada ? 'asignada' : 'error';
		}
		$this->vista('salon/reserva_en_mesa', array('resultado' => $resultado, 'Mesa' => htmlspecialchars($Mesa)));
	}

	// guard=ARMN: asigna una mesa a una reserva desde el tablero y la deja efectiva
	public function asignarDesdeTablero(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$mesa = Entrada::post('mesa');
		$Reserva = Entrada::postEntero('reserva');
		$res = $reservas->fechaYMotivo($Reserva);
		$fechaReserva = $res[0];
		if ($mesas->asignacionDe($mesa, $fechaReserva)->num_rows > 0) {
			echo "La Mesa (".htmlspecialchars($mesa).") ya se encuentra asignada";
			return;
		}
		$FechaReporta = date("Y-m-d H:i:s");
		$mesas->asignar($fechaReserva, $Reserva, $mesa, $FechaReporta, $res[1] == 1 ? 'T' : 'C');
		$reservas->marcarEfectiva($Reserva, $FechaReporta, $mesa);
		echo "Reserva (".$Reserva.") Asignada satisfactoriamente en la mesa ".htmlspecialchars($mesa);
	}

	// guard=LMM: libera una mesa. centi=1 la reserva termina; si no, solo suelta esa mesa y conserva las demas
	public function liberar(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Reserva = Entrada::postEntero("Reserva");
		if (Entrada::post("centi") == 1) {
			$liberada = $mesas->liberarDeReserva($Reserva);
			$reservas->finalizar($Reserva);
		} else {
			$liberada = $mesas->liberarMesa(Entrada::post("Fecha"), Entrada::post("Mesa"));
			$reservas->guardarMesas($Reserva, Entrada::post("MesaN"));
		}
		echo $liberada;
	}

	// guard=AMCP: actualiza las personas de una mesa compartida; con 10 o mas se marca como llena
	public function personasCompartida(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Reserva = Entrada::postEntero("Reserva");
		$CantPer = Entrada::postEntero("CantPer");
		if ($CantPer >= 10) {
			$mesas->cambiarTipo($Reserva, 'I');
		}
		echo $reservas->cambiarPersonas($Reserva, $CantPer);
	}

	// guard=TMS: pasa una reserva de una mesa a otra
	public function trasladar(){
		$reservas = new Reserva();
		$mesas = new MesaReserva();
		$Reserva = Entrada::postEntero("Reserva");
		$MesaD = Entrada::post("MesaD");
		$mesas->cambiarMesa(Entrada::postEntero("MRConse"), $MesaD);
		$nuevas = str_replace(Entrada::post("MesaO"), $MesaD, $reservas->mesasAsignadas($Reserva));
		echo $reservas->guardarMesas($Reserva, $nuevas);
	}

	// guard=INMM: alterna la marca de mesa llena (segunda letra "I" en el tipo de asignacion)
	public function alternarLlena(){
		$mesas = new MesaReserva();
		$TipoA = Entrada::post('tipof');
		$TipoN = strlen($TipoA) > 1 ? substr($TipoA,0,1) : $TipoA."I";
		echo $mesas->cambiarTipo(Entrada::postEntero('reserva'), $TipoN);
	}

	// La fecha llega como YYYY-MM-DD o DD/MM/YYYY y se usa siempre como YYYY-MM-DD.
	private function fechaDeUrl(){
		$fecha = Entrada::fechaSegura(Entrada::get('fecha'));
		if (substr($fecha,2,1)=='/') {
			$fecha = Entrada::fechaDMYaYMD($fecha);
		}
		return $fecha;
	}

	// El mapa identifica barras, palcos y auxiliares con numeros; aqui se pasan a su codigo de mesa.
	private function codigoMesa($Mesa){
		switch($Mesa){
		  case 97:
		      $Mesa="A1";
		  break;
		  case 98:
		      $Mesa="A2";
		  break;
		  case 101:
		      $Mesa="B1";
		  break;
		  case 102:
		      $Mesa="B2";
		  break;
		  case 103:
		      $Mesa="B3";
		  break;
		  case 104:
		      $Mesa="B4";
		  break;
		  case 105:
		      $Mesa="B5";
		  break;
		  case 106:
		      $Mesa="B6";
		  break;
		  case 107:
		      $Mesa="B7";
		  break;
		  case 108:
		      $Mesa="B8";
		  break;
		  case 109:
		      $Mesa="B9";
		  break;
		  case 110:
		      $Mesa="B10";
		  break;
		  case 111:
		      $Mesa="B11";
		  break;
		  case 112:
		      $Mesa="B12";
		  break;
		  case 113:
		      $Mesa="B13";
		  break;
		  case 114:
		      $Mesa="B14";
		  break;
		  case 115:
		      $Mesa="B15";
		  break;
		  case 116:
		      $Mesa="B16";
		  break;
		  case 117:
		      $Mesa="B17";
		  break;
		  case 118:
		      $Mesa="B18";
		  break;
		  case 119:
		      $Mesa="B19";
		  break;
		  case 120:
		      $Mesa="B20";
		  break;
		  case 201:
		      $Mesa="P1";
		  break;
		  case 202:
		      $Mesa="P2";
		  break;
		  case 203:
		      $Mesa="P3";
		  break;
		  case 204:
		      $Mesa="P4";
		  break;
		  case 205:
		      $Mesa="P5";
		  break;
		  case 206:
		      $Mesa="P6";
		  break;
		  case 207:
		      $Mesa="P7";
		  break;

		}
		return $Mesa;
	}
}
