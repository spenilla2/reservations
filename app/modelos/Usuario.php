<?php
// Usuarios (tblusuarios), sus perfiles (tblusuariosperfiles) y el manejo de contrasenas.
class Usuario extends Modelo {
	private $columnaAdmiteHash = null;

	public function porCorreo($correo){
		$filas = $this->bd->exigir("SELECT u.* 
				  FROM tblusuarios u 
				 WHERE u.STRMAIL='".$this->texto($correo)."'");
		return $filas->fetch_array();
	}

	// Usuario con su perfil; solo existe para cuentas activadas.
	public function conPerfil($correo){
		$filas = $this->bd->consulta("SELECT u.*,
                  up.NUMCODPERFIL 
		     FROM tblusuarios u, 
			      tblusuariosperfiles up 
			WHERE u.STRMAIL = '".$this->texto($correo)."' 
			  AND u.NUMIDENTLIV = up.NUMIDENTLIV");
		return $filas ? $filas->fetch_array() : null;
	}

	// Devuelve array(id, clave guardada, activo) o null si el correo no existe.
	public function credenciales($correo){
		$stmt = $this->bd->preparar("select u.NUMIDENTLIV,
		                                   u.STRPASSWORD,
		                                   u.STRACTIVO
		                              from tblusuarios u
		                             where u.STRMAIL = ?");
		$stmt->bind_param("s", $correo);
		$stmt->execute();
		$stmt->bind_result($id, $clave, $activo);
		$existe = $stmt->fetch();
		$stmt->close();
		return $existe ? array($id, $clave, $activo) : null;
	}

	public function cuantosConCorreo($correo){
		$filas = $this->bd->exigir("select count(*) 
		            from tblusuarios u 
		           where u.STRMAIL='".$this->texto($correo)."'");
		$fila = $filas->fetch_array();
		return $fila[0];
	}

	public function idPorCorreo($correo){
		$filas = $this->bd->exigir("SELECT u.NUMIDENTLIV 
			          FROM tblusuarios u 
			         WHERE u.STRMAIL='".$this->texto($correo)."'");
		$fila = $filas->fetch_array();
		return $fila[0];
	}

	public function nombreCompleto($idLiving){
		$filas = $this->bd->exigir("SELECT CONCAT(strnombres,' ', strapellidos) 
		            FROM tblusuarios u 
		           WHERE u.NUMIDENTLIV = ".(int)$idLiving."");
		$fila = $filas->fetch_array();
		return $fila[0];
	}

	// $d: nombre, apellido, genero, fechaNacimiento, correo, ocupacion, telefono, tipoRumba, clave, noticias, fechaIngreso
	public function crear(array $d){
		return $this->bd->exigir("INSERT INTO tblusuarios VALUES(NULL,'".$this->texto(htmlspecialchars(strtoupper($d['nombre'])))."','"
													  .$this->texto(htmlspecialchars(strtoupper($d['apellido'])))."','"
													  .$this->texto($d['genero'])."','"
													  .$this->texto($d['fechaNacimiento'])."','"
													  .$this->texto(strtolower($d['correo']))."',"
													  .(int)$d['ocupacion'].","
													  ."1,'"
													  .$this->texto($d['telefono'])."',"
													  ."1,"
													  ."1,"
													  ."1,"
													  .(int)$d['tipoRumba'].",'"
													  .$this->texto($this->claveParaGuardar($d['clave']))."','"
													  .$this->texto($d['fechaIngreso'])."','"
													  .$this->texto($d['noticias'])."','','','','','0','')");
	}

	public function registrarIngreso($correo, $fecha){
		return $this->bd->consulta("UPDATE tblusuarios u 
               SET u.DTMFECHAULTINGRESO = '".$this->texto($fecha)."' 
	         WHERE u.STRMAIL = '".$this->texto($correo)."'");
	}

	public function estaActivo($idLiving){
		$filas = $this->bd->exigir("select u.STRACTIVO from tblusuarios u where u.NUMIDENTLIV=".(int)$idLiving);
		$fila = $filas->fetch_array();
		return $fila[0] == 1;
	}

	// Activa la cuenta y le crea el perfil de cliente.
	public function activar($idLiving, $fecha){
		$idLiving = (int)$idLiving;
		$ok = $this->bd->exigir("Update tblusuarios u set u.STRACTIVO=1, u.DTMFECHAULTINGRESO='".$this->texto($fecha)."' where u.NUMIDENTLIV=".$idLiving);
		if ($ok == 1) {
			$this->bd->exigir("INSERT INTO tblusuariosperfiles VALUES (".$idLiving.",0)");
		}
		return $ok == 1;
	}

	// --- Contrasenas ---
	// El navegador envia la clave en Base64 (scripts.js); ese valor es el que se compara y se hashea.
	// Solo se guarda con hash si la columna STRPASSWORD tiene 60 caracteres o mas.
	public function columnaAdmiteHash(){
		if ($this->columnaAdmiteHash === null) {
			$this->columnaAdmiteHash = false;
			$filas = $this->bd->consulta("SELECT CHARACTER_MAXIMUM_LENGTH
			                                FROM information_schema.COLUMNS
			                               WHERE TABLE_SCHEMA = DATABASE()
			                                 AND TABLE_NAME = 'tblusuarios'
			                                 AND COLUMN_NAME = 'STRPASSWORD'");
			if ($filas && ($fila = $filas->fetch_array())) {
				$this->columnaAdmiteHash = ((int)$fila[0] >= 60);
			}
		}
		return $this->columnaAdmiteHash;
	}

	public function claveEsHash($guardada){
		$info = password_get_info((string)$guardada);
		return !empty($info['algo']);
	}

	public function verificarClave($clave, $guardada){
		if ($this->claveEsHash($guardada)) {
			return password_verify((string)$clave, (string)$guardada);
		}
		return hash_equals((string)$guardada, (string)$clave);
	}

	public function claveParaGuardar($clave){
		if ($this->columnaAdmiteHash()) {
			return password_hash((string)$clave, PASSWORD_DEFAULT);
		}
		return (string)$clave;
	}

	public function actualizarClave($idLiving, $clave){
		$nueva = $this->claveParaGuardar($clave);
		$idLiving = (int)$idLiving;
		$stmt = $this->bd->preparar("UPDATE tblusuarios SET STRPASSWORD = ? WHERE NUMIDENTLIV = ?");
		if (!$stmt) {
			return false;
		}
		$stmt->bind_param("si", $nueva, $idLiving);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}
}
