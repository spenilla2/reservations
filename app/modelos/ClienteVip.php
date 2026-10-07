<?php
// Clientes VIP (tblclientesvip).
class ClienteVip extends Modelo {
	public function deUsuario($idLiving){
		return $this->bd->consulta("SELECT * 
	          FROM tblclientesvip vp 
			 WHERE vp.NUMIDENTLIV = ".(int)$idLiving." ORDER BY 2 ASC");
	}

	public function todos(){
		return $this->bd->exigir("SELECT vp.* 
            				  FROM tblclientesvip vp 
            				  ORDER BY 1 DESC");
	}

	public function buscarPorNombre($nombre){
		return $this->bd->exigir("select vp.*,tr.STRDESCRIPCION 
						from tblclientesvip vp, tbltiporumba tr 
					   where tr.NUMTIPORUMBA=vp.NUMTIPORUMBA
						 and vp.STRNOMBRECLIENTE like '%".$this->texto(strtoupper($nombre))."%' 
				    order by 2 asc");
	}

	public function crear($nombre, $genero, $idLiving, $tipoRumba, $telefono, $fechaCreacion, $observaciones){
		return $this->bd->exigir("INSERT INTO tblclientesvip VALUES(NULL, '".$this->texto($nombre).
			                                             "','".$this->texto($genero).
			                                              "',".(int)$idLiving.
			                                               ",".(int)$tipoRumba.
			                                              ",'".$this->texto($telefono).
			                                             "','".$this->texto($fechaCreacion).
			                                             "','".$this->texto($observaciones)."')");
	}

	public function actualizar($idClienteVip, $nombre, $genero, $tipoRumba, $telefono, $observaciones){
		return $this->bd->exigir("UPDATE tblclientesvip v SET v.STRNOMBRECLIENTE = '".$this->texto($nombre).
		                                 "', v.STRGENERO = '".$this->texto($genero).
		                                 "', v.NUMTIPORUMBA = ".(int)$tipoRumba.
		                                  ", v.STRNUMTEL = '".$this->texto($telefono).
		                                 "', v.STROBSERVACIONES = '".$this->texto($observaciones).
		                            "' WHERE v.IDCLIENTEVIP = ".(int)$idClienteVip);
	}
}
