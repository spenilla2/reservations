<?php
// Eventos (tbleventos): conciertos con dos imagenes, y festivos (tblfestivos) que condicionan las reservas de un dia.
// Columnas de tbleventos tal como se usan hoy:
//   nomImagen      nombre del evento
//   nombreArchivo  imagen frontal (archivo en images/Eventos)
//   imagenGrande   imagen con los precios (archivo en images/Eventos)
//   Fecha          dia del evento
//   ManejaReserva  'S' si ese dia se pueden hacer reservas por el sistema, 'N' si no
//   conta, Genero  campos antiguos sin uso; se conservan porque la tabla los exige
class Evento extends Modelo {
	const COLUMNAS = "id, nomImagen AS nombre, Fecha AS fecha, nombreArchivo AS frente, imagenGrande AS precios, ManejaReserva AS reservas";

	// Evento programado en la fecha (YYYY-MM-DD), o null.
	public function enFecha($fecha){
		$filas = $this->bd->consulta("SELECT ".self::COLUMNAS."
					  FROM tbleventos 
					 WHERE Fecha='".$this->texto($fecha)."'
				  ORDER BY id DESC");
		return $filas ? $filas->fetch_array() : null;
	}

	public function porId($id){
		$filas = $this->bd->consulta("SELECT ".self::COLUMNAS." FROM tbleventos WHERE id = ".(int)$id);
		return $filas ? $filas->fetch_array() : null;
	}

	// Eventos de una fecha en adelante, del mas cercano al mas lejano.
	public function proximos($desde){
		$filas = $this->bd->consulta("SELECT ".self::COLUMNAS." FROM tbleventos WHERE Fecha >= '".$this->texto($desde)."' ORDER BY Fecha ASC, id ASC");
		return $filas ? $filas->todas() : array();
	}

	// Eventos anteriores a una fecha, del mas reciente al mas antiguo.
	public function pasados($antesDe){
		$filas = $this->bd->consulta("SELECT ".self::COLUMNAS." FROM tbleventos WHERE Fecha < '".$this->texto($antesDe)."' ORDER BY Fecha DESC, id DESC");
		return $filas ? $filas->todas() : array();
	}

	// Hay otro evento ese dia (distinto de $exceptoId).
	public function fechaOcupada($fecha, $exceptoId = 0){
		$filas = $this->bd->consulta("SELECT id FROM tbleventos WHERE Fecha = '".$this->texto($fecha)."' AND id <> ".(int)$exceptoId);
		return $filas && $filas->num_rows > 0;
	}

	public function crear($nombre, $fecha, $frente, $precios, $recibeReservas){
		$fila = $this->bd->consulta("SELECT COALESCE(MAX(conta), 0) + 1 FROM tbleventos")->fetch_array();
		return $this->bd->consulta("INSERT INTO tbleventos (conta, nombreArchivo, nomImagen, imagenGrande, Genero, Fecha, ManejaReserva)
			VALUES (".(int)$fila[0].", '".$this->texto($frente)."', '".$this->texto($nombre)."', '".$this->texto($precios)."', '', '"
			.$this->texto($fecha)."', '".($recibeReservas ? 'S' : 'N')."')");
	}

	public function actualizar($id, $nombre, $fecha, $frente, $precios, $recibeReservas){
		return $this->bd->consulta("UPDATE tbleventos
			   SET nomImagen = '".$this->texto($nombre)."',
			       Fecha = '".$this->texto($fecha)."',
			       nombreArchivo = '".$this->texto($frente)."',
			       imagenGrande = '".$this->texto($precios)."',
			       ManejaReserva = '".($recibeReservas ? 'S' : 'N')."'
			 WHERE id = ".(int)$id);
	}

	public function eliminar($id){
		return $this->bd->consulta("DELETE FROM tbleventos WHERE id = ".(int)$id);
	}

	// Algun evento usa ese archivo de imagen (para no borrar del disco uno compartido).
	public function archivoEnUso($archivo){
		$archivo = $this->texto($archivo);
		$filas = $this->bd->consulta("SELECT id FROM tbleventos WHERE nombreArchivo = '".$archivo."' OR imagenGrande = '".$archivo."'");
		return $filas && $filas->num_rows > 0;
	}

	public function esFestivo($fecha){
		$filas = $this->bd->consulta("SELECT * 
					          FROM tblfestivos e 
					         WHERE e.Fecha='".$this->texto($fecha)."'");
		return $filas && $filas->num_rows > 0;
	}
}
