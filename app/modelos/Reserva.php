<?php
// Reservas (tblreservas). Estados: 1 activa, 3 efectiva, 0 cancelada, 2 perdida, 5 finalizada.
class Reserva extends Modelo {
	// Filtro comun: deja fuera las mesas bloqueadas, compartidas y los grupos sin reserva.
	const SOLO_RESERVAS_REALES = "AND r.STRNOMBRERESERVA NOT IN('MESA BLOQUEADA','Mesa Compartida')
              AND r.STRNOMBRERESERVA NOT LIKE '%Grupo %'";

	public function porId($numReserva){
		$filas = $this->bd->consulta("SELECT * 
		          FROM tblreservas 
				 WHERE NUMRESERVA = ".(int)$numReserva."");
		return $filas ? $filas->fetch_array() : null;
	}

	public function perteneceA($numReserva, $idLiving){
		$numReserva = (int)$numReserva;
		$idLiving = (int)$idLiving;
		$stmt = $this->bd->preparar("SELECT 1 FROM tblreservas WHERE NUMRESERVA = ? AND NUMIDENTLIV = ?");
		if (!$stmt) {
			return false;
		}
		$stmt->bind_param("ii", $numReserva, $idLiving);
		$stmt->execute();
		$stmt->store_result();
		$pertenece = $stmt->num_rows > 0;
		$stmt->close();
		return $pertenece;
	}

	// Reservas vigentes (activas o efectivas, de hoy en adelante) de un usuario.
	public function vigentesDe($idLiving){
		return $this->bd->consulta("SELECT r.*,
                  t.STRDESCRIPCION, 
                  e.STRDESCRIPCION 
             FROM tblreservas r, 
                  tbltipocelebra t,
                  tblestados e 
            WHERE r.NUMIDENTLIV=".(int)$idLiving." 
              AND r.NUMMOTIVO=t.NUMMOTIVO 
              AND r.NUMESTADO=e.NUMESTADO
              AND r.NUMESTADO in(1,3)
              ".self::SOLO_RESERVAS_REALES."
              AND r.DTMFECHARESERVA >='".date('Y-m-d')."' 
         ORDER BY 1 DESC");
	}

	// Forma en que se guarda el nombre de una reserva nueva; existeIgual() compara contra esto mismo.
	private function nombreGuardado($nombre){
		return htmlentities(htmlspecialchars(strtoupper(rtrim(ltrim($nombre)))));
	}

	public function existeIgual($idLiving, $fecha, $nombre, $homenajeado, $motivo){
		$filas = $this->bd->consulta("SELECT * 
					FROM tblreservas r 
				   WHERE r.NUMIDENTLIV=".(int)$idLiving." 
				     AND r.DTMFECHARESERVA='".$this->texto($fecha)."'
				     AND r.STRNOMBRERESERVA='".$this->texto($this->nombreGuardado($nombre))."'
				     AND r.STRNOMBREHOMENAJE='".$this->texto(htmlspecialchars(strtoupper($homenajeado)))."' 
				     AND r.NUMMOTIVO=".(int)$motivo);
		return $filas && $filas->num_rows >= 1;
	}

	// $estado 1 = activa (reserva normal), 3 = efectiva (creada directamente sobre una mesa).
	public function crear($fecha, $idLiving, $nombre, $personas, $motivo, $homenajeado, $observaciones, $fechaRegistro, $estado, $mesa, $idClienteVip){
		return $this->bd->consulta("INSERT INTO tblreservas VALUES(null,'".$this->texto($fecha)."',"
			                                              .(int)$idLiving.",'"
			                                              .$this->texto($this->nombreGuardado($nombre))."',"
			                                              .(int)$personas.","
			                                              .(int)$motivo.",'"
			                                              .$this->texto(htmlspecialchars(strtoupper($homenajeado)))."','"
			                                              .$this->texto(htmlspecialchars(strtoupper($observaciones)))."','"
			                                              .$this->texto($fechaRegistro)."',".(int)$estado.",'"
			                                              .$this->texto($fechaRegistro)."','".$this->texto($mesa)."',"
			                                              .(int)$idClienteVip.",'N')");
	}

	public function ultimaDe($idLiving, $fecha = null){
		$sql = "SELECT max(numreserva) 
		    	        FROM tblreservas 
		    	       WHERE NUMIDENTLIV = ".(int)$idLiving;
		if ($fecha !== null) {
			$sql .= " AND DTMFECHARESERVA = '".$this->texto($fecha)."'";
		}
		$fila = $this->bd->consulta($sql)->fetch_array();
		return $fila[0];
	}

	public function actualizar($numReserva, $nombre, $personas, $motivo, $homenajeado, $observaciones){
		return $this->bd->consulta("UPDATE tblreservas
		           SET STRNOMBRERESERVA = '".$this->texto(htmlspecialchars(strtoupper($nombre))).
		           "', NUMPERSONAS = ".(int)$personas.
		            ", NUMMOTIVO = ".(int)$motivo.
		            ", STRNOMBREHOMENAJE = '".$this->texto(htmlspecialchars(strtoupper($homenajeado))).
		           "', STROBSERVACIONES = '".$this->texto($observaciones).
		            "' WHERE NUMRESERVA = ".(int)$numReserva);
	}

	public function cancelar($numReserva){
		return $this->bd->consulta("UPDATE tblreservas
		           SET NUMESTADO = 0
		         WHERE NUMRESERVA = ".(int)$numReserva);
	}

	public function datosParaCorreo($numReserva){
		$filas = $this->bd->exigir("select u.STRMAIL, 
                 CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as Nombre,
                 r.STRNOMBRERESERVA,
                 r.NUMPERSONAS,
                 c.STRDESCRIPCION,
                 r.STRNOMBREHOMENAJE,
                 r.DTMFECHARESERVA,
                 r.DTMFECHAREALIZARES,
                 r.STROBSERVACIONES
            from tblreservas r,
                 tblusuarios u,
                 tbltipocelebra c
           where r.NUMIDENTLIV=u.NUMIDENTLIV
             and r.NUMMOTIVO=c.NUMMOTIVO 
             and r.NUMRESERVA=".(int)$numReserva);
		return $filas->fetch_array();
	}

	// Detalle con usuario, motivo y estado, para confirmar la llegada o gestionar la reserva.
	public function detalle($numReserva){
		return $this->bd->exigir("SELECT r.NUMRESERVA,
						 r.DTMFECHARESERVA,
						 CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
						 r.STRNOMBRERESERVA,
						 r.NUMPERSONAS,
						 t.STRDESCRIPCION,
						 r.STRNOMBREHOMENAJE,
						 r.STROBSERVACIONES,
						 e.STRDESCRIPCION,
						 r.DTMFECHAREALIZARES,
						 r.STRMESAASIGNA,
						 r.NUMMOTIVO,
						 r.STRCUMPLERESERVA,
						 r.DTMFECHAREALIZARES,
						 r.NUMESTADO,
						 r.NUMMOTIVO,
						 e.NUMESTADO	
				    FROM tblreservas r,
				         tblusuarios u,
				         tbltipocelebra t,
				         tblestados e 
				   WHERE r.NUMRESERVA = ".(int)$numReserva." 
				     AND r.NUMIDENTLIV = u.NUMIDENTLIV
				     AND r.NUMMOTIVO = t.NUMMOTIVO
				     AND r.NUMESTADO = e.NUMESTADO");
	}

	// Detalle de la reserva que ocupa una mesa, con el tipo de asignacion de esa mesa.
	public function detalleEnMesa($numReserva, $mesa){
		$filas = $this->bd->consulta("SELECT r.NUMRESERVA,
               r.DTMFECHARESERVA,
               CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
               r.STRNOMBRERESERVA,
               r.NUMPERSONAS,
               t.STRDESCRIPCION,
               r.STRNOMBREHOMENAJE,
               r.STROBSERVACIONES,
               e.STRDESCRIPCION,
               r.DTMFECHAREALIZARES,
               r.STRMESAASIGNA,
               ta.STRDESCRIPCION,
               mr.STRTIPO,
               mr.NUMCONSECUTIVO,
               r.NUMMOTIVO,
               r.STRCUMPLERESERVA,
               r.DTMFECHAREALIZARES,
               r.NUMESTADO,
               r.NUMMOTIVO  
          FROM tblreservas r,
               tblusuarios u,
               tbltipocelebra t,
               tblestados e,
               tblmesareserva mr,
               tbltipoasigna ta 
         WHERE r.NUMRESERVA = ".(int)$numReserva." 
           AND r.NUMIDENTLIV = u.NUMIDENTLIV
           AND r.NUMMOTIVO = t.NUMMOTIVO
           AND r.NUMESTADO = e.NUMESTADO
           AND r.NUMRESERVA = mr.NUMRESERVA
           AND SUBSTRING(mr.STRTIPO,1,1) = ta.STRTIPOASIGNA
           AND mr.STRMESA='".$this->texto($mesa)."'");
		return $filas ? $filas->fetch_array() : null;
	}

	public function marcarEfectiva($numReserva, $fechaEstado, $mesa = null){
		$sql = "UPDATE tblreservas r 
		           SET r.NUMESTADO = 3, 
		               r.DTMFECHAESTADO = '".$this->texto($fechaEstado)."'";
		if ($mesa !== null) {
			$sql .= ", r.STRMESAASIGNA = '".$this->texto($mesa)."'";
		}
		return $this->bd->consulta($sql." WHERE r.NUMRESERVA = ".(int)$numReserva);
	}

	// Reservas activas de un dia (YYYY-MM-DD) que todavia no tienen mesa.
	public function pendientesDeMesa($fecha){
		$fecha = $this->texto($fecha);
		return $this->bd->exigir("SELECT * 
		   FROM tblreservas R 
		  WHERE R.DTMFECHARESERVA='".$fecha."' 
		    AND R.NUMESTADO=1 
		    AND R.NUMRESERVA NOT IN(
						SELECT M.NUMRESERVA 
						  FROM tblmesareserva M 
						 WHERE M.DTMFECHARESERVA = '".$fecha."'
						   AND M.STRESTADORESERVA = '1')
			ORDER BY R.STRNOMBRERESERVA");
	}

	public function guardarMesas($numReserva, $mesas){
		return $this->bd->consulta("UPDATE tblreservas R 
		           SET R.STRMESAASIGNA = '".$this->texto($mesas)."' 
		         WHERE R.NUMRESERVA = ".(int)$numReserva);
	}

	public function mesasAsignadas($numReserva){
		$fila = $this->bd->consulta("SELECT r.STRMESAASIGNA 
        		   FROM tblreservas r 
		          WHERE r.NumReserva=".(int)$numReserva."")->fetch_array();
		return $fila[0];
	}

	// La reserva deja la mesa y queda finalizada.
	public function finalizar($numReserva){
		return $this->bd->consulta("UPDATE tblreservas r 
					   SET r.STRMESAASIGNA='',
					       r.numestado=5
					 WHERE r.numreserva=".(int)$numReserva);
	}

	public function cambiarPersonas($numReserva, $personas){
		return $this->bd->consulta("UPDATE tblreservas r
					 SET r.numpersonas=".(int)$personas."
				   WHERE r.numreserva=".(int)$numReserva);
	}

	public function marcarCortesia($numReserva, $valor){
		return $this->bd->exigir("update tblreservas r 
				   SET r.STRCUMPLERESERVA='".$this->texto($valor)."' 
				 WHERE r.NUMRESERVA=".(int)$numReserva."");
	}

	// Devuelve array(fecha, motivo) de la reserva.
	public function fechaYMotivo($numReserva){
		return $this->bd->consulta("SELECT r.DTMFECHARESERVA,
		               r.NUMMOTIVO
		          FROM tblreservas r  
				WHERE NUMRESERVA = ".(int)$numReserva)->fetch_array();
	}

	// Reservas activas del dia con mesa asignada que no son especiales: las que se pueden levantar.
	public function paraLevantar($fecha){
		return $this->bd->exigir("      SELECT r.*
						  FROM tblreservas r, tblmesareserva mr
						 WHERE r.numestado = 1
						   AND r.dtmfechareserva='".$this->texto($fecha)."'
						   AND mr.numreserva=r.numreserva
						   AND mr.strtipo<>'E'");
	}

	// Marca como perdidas las reservas activas del dia, salvo las especiales y las de $mantener (lista "1,2,3").
	public function levantar($fecha, $mantener){
		$fecha = $this->texto($fecha);
		return $this->bd->consulta("UPDATE tblreservas r 
				   SET r.numestado=2 
				 WHERE r.dtmfechareserva='".$fecha."'
				   AND r.numestado=1
				   AND r.numreserva NOT IN(".Entrada::listaEnteros($mantener).")
				   AND r.numreserva NOT IN(
				   		SELECT mr.numreserva
				   		  FROM tblmesareserva mr
				   		 WHERE mr.dtmfechareserva='".$fecha."'
				   		   AND mr.strtipo='E')");
	}

	// Listado del dia con motivo, estado y correo de quien reservo. $soloCortesia limita a las que aplican cortesia.
	public function listadoDelDia($fecha, $soloCortesia = false){
		return $this->bd->consulta("SELECT r.*,
                  t.STRDESCRIPCION, 
                  e.STRDESCRIPCION,
                  u.STRMAIL
             FROM tblreservas r, 
                  tbltipocelebra t,
                  tblestados e, 
                  tblusuarios u 
            WHERE r.DTMFECHARESERVA = '".$this->texto($fecha)."' 
              AND r.NUMMOTIVO=t.NUMMOTIVO 
              AND r.NUMESTADO=e.NUMESTADO
              AND r.NUMESTADO in(1,3)
              AND r.NUMIDENTLIV = u.NUMIDENTLIV
              ".self::SOLO_RESERVAS_REALES."
              ".($soloCortesia ? " AND r.STRCUMPLERESERVA='S'" : "")." 
         ORDER BY 4 ASC");
	}

	// Devuelve array(total de reservas, personas esperadas) del dia.
	public function totalesDelDia($fecha, $soloCortesia = false){
		return $this->bd->consulta("SELECT 
		 		  COUNT(*) as Total,
		 		  SUM(NUMPERSONAS) as PersonEsp
             FROM tblreservas r 
            WHERE r.DTMFECHARESERVA = '".$this->texto($fecha)."' 
              AND r.NUMESTADO in(1,3)
              ".self::SOLO_RESERVAS_REALES."
              ".($soloCortesia ? " AND r.STRCUMPLERESERVA='S'" : "")."")->fetch_array();
	}

	public function buscar($numReserva){
		return $this->bd->exigir("SELECT r.*,
                  t.STRDESCRIPCION, 
                  e.STRDESCRIPCION 
             FROM tblreservas r, 
                  tbltipocelebra t,
                  tblestados e 
            WHERE r.numreserva=".(int)$numReserva." 
              AND r.NUMMOTIVO=t.NUMMOTIVO 
              AND r.NUMESTADO=e.NUMESTADO
              AND r.NUMESTADO in(1,3) 
         ORDER BY 1 DESC");
	}

	// Reservas del dia por motivo (1 cumpleanos, 3 grado), activas o efectivas, tengan o no mesa.
	// Columnas: nombre, homenajeado, mesas, estado, personas.
	public function delDiaPorMotivo($fecha, $motivo){
		return $this->bd->consulta("SELECT r.STRNOMBRERESERVA,
               r.STRNOMBREHOMENAJE,
               r.STRMESAASIGNA,
               e.STRDESCRIPCION,
               r.NUMPERSONAS
          FROM tblreservas r,
               tblestados e
         WHERE r.DTMFECHARESERVA = '".$this->texto($fecha)."'
           AND r.NUMMOTIVO = ".(int)$motivo."
           AND r.NUMESTADO in(1,3)
           AND r.NUMESTADO = e.NUMESTADO
      ORDER BY 1 ASC");
	}

	// Cuantas reservas activas del dia ya tienen mesa.
	public function activasConMesa($fecha){
		$fila = $this->bd->exigir("SELECT COUNT(*)
             FROM tblreservas R,
                  tblmesareserva mr
            WHERE R.DTMFECHARESERVA = '".$this->texto($fecha)."'
              AND R.NUMESTADO = 1
              AND R.STRMESAASIGNA <>''
              AND R.NUMRESERVA=mr.NUMRESERVA
              AND mr.STRESTADORESERVA=1")->fetch_array();
		return $fila;
	}
}
