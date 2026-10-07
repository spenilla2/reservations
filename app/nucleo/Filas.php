<?php
// Resultado de una consulta ya leido en memoria. Las vistas lo recorren sin tocar la conexion.
// Cada fila trae indices numericos y por nombre de columna, igual que mysqli::fetch_array().
class Filas {
	public $num_rows;
	private $filas;
	private $posicion = 0;

	public function __construct(array $filas){
		$this->filas = $filas;
		$this->num_rows = count($filas);
	}

	public function fetch_array(){
		if ($this->posicion >= $this->num_rows) {
			return null;
		}
		return $this->filas[$this->posicion++];
	}

	// Vuelve al inicio para recorrer el mismo resultado otra vez.
	public function reiniciar(){
		$this->posicion = 0;
		return $this;
	}

	public function primera(){
		return $this->num_rows > 0 ? $this->filas[0] : null;
	}

	public function todas(){
		return $this->filas;
	}
}
