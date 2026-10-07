<?php
// Tablas de referencia: ocupaciones, tipos de rumba, motivos de celebracion, tipos de asignacion.
class Catalogo extends Modelo {
	public function ocupaciones(){
		return $this->bd->exigir("select * from tblocupacion order by 2 asc");
	}

	public function tiposRumba(){
		return $this->bd->exigir("select * from tbltiporumba order by 1 asc");
	}

	public function tiposCelebracion(){
		return $this->bd->exigir("SELECT * FROM tbltipocelebra ORDER BY 2 ASC");
	}

	// Tipos de asignacion que se muestran como convenciones en el mapa del salon.
	public function tiposAsignacionMapa(){
		return $this->bd->exigir("SELECT a.* 
                   FROM tbltipoasigna a
                  WHERE a.STRTIPOASIGNA IN('A','E','C','F')");
	}
}
