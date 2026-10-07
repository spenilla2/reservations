<?php
// Mesas del salon (tblmesas) y su asignacion a reservas por dia (tblmesareserva).
// STRESTADORESERVA: 1 mesa ocupada, 0 liberada. STRTIPO: letra de tbltipoasigna, con una "I" adicional si esta llena.
class MesaReserva extends Modelo {
	// Mesas sin asignacion vigente en la fecha. $ordenNumerico las ordena por numero de mesa.
	public function libres($fecha, $ordenNumerico = false){
		return $this->bd->exigir("SELECT * 
		   FROM tblmesas M 
		  WHERE M.STRMESA NOT IN(
						SELECT MR.STRMESA 
						  FROM tblmesareserva MR 
						 WHERE MR.DTMFECHARESERVA = '".$this->texto($fecha)."'
						   AND MR.STRESTADORESERVA = '1'
						 )".($ordenNumerico ? " ORDER BY CAST(M.STRMESA as UNSIGNED) ASC" : ""));
	}

	// Fila de tblmesas (codigo, capacidad, tipo) o null si la mesa no existe.
	public function datos($mesa){
		$filas = $this->bd->consulta("SELECT * FROM tblmesas WHERE STRMESA = '".$this->texto($mesa)."'");
		return $filas ? $filas->fetch_array() : null;
	}

	// Asignacion vigente de una mesa en la fecha (Filas vacio si esta libre).
	public function asignacionDe($mesa, $fecha){
		return $this->bd->consulta("SELECT * 
				  FROM tblmesareserva mr 
				 WHERE mr.STRMESA='".$this->texto($mesa)."' 
				   AND mr.strestadoreserva='1'
				   AND mr.dtmfechareserva='".$this->texto($fecha)."'");
	}

	public function asignar($fecha, $numReserva, $mesa, $fechaAsigna, $tipo){
		return $this->bd->consulta("INSERT INTO tblmesareserva VALUES(NULL,'".$this->texto($fecha).
			                                             "',".(int)$numReserva.
			                                             ",'".$this->texto($mesa).
			                                             "','".$this->texto($fechaAsigna).
			                                             "','".$this->texto($tipo).
			                                             "','1')");
	}

	public function marcarConfirmadas($numReserva){
		return $this->bd->consulta("UPDATE tblmesareserva r 
		             SET r.STRTIPO = 'C' 
		           WHERE r.NUMRESERVA = ".(int)$numReserva);
	}

	public function cambiarTipo($numReserva, $tipo){
		return $this->bd->consulta("UPDATE tblmesareserva m 
			       SET m.STRTIPO='".$this->texto($tipo)."'
			     WHERE m.NUMRESERVA=".(int)$numReserva);
	}

	public function liberarDeReserva($numReserva){
		return $this->bd->consulta("UPDATE tblmesareserva m 
					   SET m.strestadoreserva=0
					 WHERE m.numreserva=".(int)$numReserva);
	}

	public function liberarMesa($fecha, $mesa){
		return $this->bd->consulta("UPDATE tblmesareserva m 
					   SET m.strestadoreserva=0
					 WHERE m.dtmfechareserva='".$this->texto($fecha)."'
					   AND m.strmesa='".$this->texto($mesa)."'");
	}

	// Libera las mesas de las reservas que quedaron perdidas ese dia.
	public function liberarPerdidas($fecha){
		$fecha = $this->texto($fecha);
		return $this->bd->consulta("UPDATE tblmesareserva mr 
				   SET mr.strestadoreserva=0 
				 WHERE mr.dtmfechareserva='".$fecha."'
				   AND mr.numreserva IN(
				   		SELECT r.numreserva
				   		  FROM tblreservas r
				   		 WHERE r.dtmfechareserva='".$fecha."'
				   		   AND r.numestado='2')");
	}

	public function cambiarMesa($consecutivo, $mesaDestino){
		return $this->bd->consulta("UPDATE tblmesareserva mr 
		            SET mr.strmesa='".$this->texto($mesaDestino)."'
		          WHERE mr.numconsecutivo=".(int)$consecutivo);
	}

	// Mesas ocupadas del dia con el color de su tipo de asignacion (rojo si estan llenas).
	public function coloresDelDia($fecha){
		return $this->bd->consulta("SELECT DISTINCT  M.STRMESA,
								CASE 
									WHEN LENGTH(M.STRTIPO)>1 THEN '#FF0000'
									ELSE A.STRCOLOR
								END 
                  FROM tblmesareserva M, 
                  	     tbltipoasigna A 
                 WHERE M.DTMFECHARESERVA = '" .$this->texto($fecha)."'
                   AND A.STRTIPOASIGNA = substring(M.STRTIPO,1,1)
                   AND M.STRESTADORESERVA in(1,3)
               	ORDER BY M.STRMESA ASC");
	}

	public function ocupadas($fecha){
		return $this->bd->consulta($this->sqlMesasSegunOcupacion($fecha, "", "IS NOT NULL"));
	}

	// Mesas que NO cumplen el filtro del tablero: 1 especiales, 2 confirmadas, 3 normales o especiales,
	// 4 compartidas, 5 llenas, 6 cumpleanos. Devuelve null si el filtro no existe.
	public function fueraDelFiltro($fecha, $filtro){
		$condiciones = array(
			1 => "AND substring(M.STRTIPO,1,1) ='E'",
			2 => "AND substring(M.STRTIPO,1,1) ='C'",
			3 => "AND substring(M.STRTIPO,1,1) in('N','E')",
			4 => "AND substring(M.STRTIPO,1,1) in('K')",
			5 => "AND substring(M.STRTIPO,2,1) in('I')",
		);
		if ($filtro == 6) {
			return $this->bd->consulta("SELECT A.STRMESA 
				    FROM tblmesas A LEFT JOIN(
										SELECT DISTINCT
											   M.STRMESA 
										  FROM tblmesareserva M,
										  	   tblreservas r
										 WHERE M.DTMFECHARESERVA = '".$this->texto($fecha)."'
										   AND r.NUMRESERVA=M.NUMRESERVA 
										   AND M.STRESTADORESERVA = 1
										   AND r.NUMMOTIVO=1
									  ORDER BY M.STRMESA ASC
                                    ) P ON P.STRMESA= A.STRMESA
				   WHERE P.STRMESA IS NULL");
		}
		if (!isset($condiciones[$filtro])) {
			return null;
		}
		return $this->bd->consulta($this->sqlMesasSegunOcupacion($fecha, $condiciones[$filtro], "IS NULL"));
	}

	private function sqlMesasSegunOcupacion($fecha, $condicion, $nulo){
		return "SELECT A.STRMESA 
				    FROM tblmesas A LEFT JOIN(
										SELECT DISTINCT
											   M.STRMESA 
										  FROM tblmesareserva M
										 WHERE M.DTMFECHARESERVA = '".$this->texto($fecha)."' 
										   AND M.STRESTADORESERVA = 1
										   ".$condicion."
									  ORDER BY M.STRMESA ASC
                                    ) P ON P.STRMESA= A.STRMESA
				   WHERE P.STRMESA ".$nulo;
	}

	// Asignaciones vigentes del dia con el color de su tipo, para pintar el mapa del salon.
	public function ocupacionConColor($fecha){
		return $this->bd->exigir("SELECT m.*,a.strcolor 
                  FROM tblmesareserva m, tbltipoasigna a 
                 WHERE m.dtmfechareserva = '" .$this->texto($fecha)."'
                   AND a.strtipoasigna = m.strtipo
                   AND m.strestadoreserva = 1");
	}

	// Mesas con reserva activa del dia. $orden: 1 por mesa, 2 por nombre de reserva.
	public function reservasActivasPorMesa($fecha, $orden){
		return $this->bd->exigir("SELECT distinct(m.STRMESA), 
                   						   r.STRNOMBRERESERVA,
                   						   r.STRNOMBREHOMENAJE,
                   						   r.STROBSERVACIONES
                  					  FROM tblmesareserva m, 
                  					       tbltipoasigna a, 
                  					       tblreservas r 
                 					 WHERE m.dtmfechareserva = '" .$this->texto($fecha)."'
                   					   AND a.strtipoasigna = m.strtipo
                   					   AND m.strestadoreserva = 1
                   					   AND r.numestado=1
                   					   AND r.NUMRESERVA=m.NUMRESERVA
                   					   AND m.STRTIPO not in('B','K')
                   					   AND r.STRNOMBRERESERVA NOT IN('MESA BLOQUEADA','Mesa Compartida')
                   					   AND r.STRNOMBRERESERVA NOT LIKE '%Grupo%'
                   							ORDER BY ".(int)$orden." asc");
	}

	// Listados del mapa: 'K' mesas levantadas, 'Cu' cumpleanos, o las de un tipo de asignacion.
	public function listado($fecha, $tipo){
		if ($tipo == 'K') {
			$filtro = "AND mr.STRESTADORESERVA =0";
			$orden = 2;
		} elseif ($tipo == 'Cu') {
			$filtro = "AND mr.STRESTADORESERVA =1
			   AND r.NUMMOTIVO =1
			   AND r.NUMESTADO not in(2,0)";
			$orden = 1;
		} else {
			$filtro = "AND mr.STRESTADORESERVA =1
			   AND mr.STRTIPO='".$this->texto($tipo)."'";
			$orden = 2;
		}
		return $this->bd->exigir("SELECT mr.strmesa,
			   r.strnombrereserva,
			   r.STRNOMBREHOMENAJE,
			   tc.strdescripcion, 
			   ta.strdescripcion, 
			   e.strdescripcion,
			   CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
			   r.strobservaciones,
			   r.NUMRESERVA
			 FROM tblreservas r, 
			      tblmesareserva mr, 
			      tbltipocelebra tc, 
			      tbltipoasigna ta, 
			      tblestados e,
			      tblusuarios u
			WHERE r.dtmfechareserva = '".$this->texto($fecha)."'
			   AND r.NUMRESERVA = mr.NUMRESERVA
			   ".$filtro."
			   AND tc.nummotivo = r.nummotivo
			   AND ta.STRTIPOASIGNA = mr.STRTIPO
			   AND r.NUMESTADO = e.numestado
			   AND r.NUMIDENTLIV = u.NUMIDENTLIV
			ORDER BY ".$orden." asc");
	}
}
