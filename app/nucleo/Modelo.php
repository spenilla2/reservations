<?php
// Base de los modelos: todo el SQL de la aplicacion vive en clases que heredan de esta.
abstract class Modelo {
	protected $bd;

	public function __construct(){
		$this->bd = BaseDatos::obtener();
	}

	protected function texto($valor){
		return $this->bd->texto($valor);
	}
}
