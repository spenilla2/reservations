<?php
// Enlaces de un solo uso para restablecer la contrasena (tblrecuperaclave).
// En la base solo queda el SHA-256 del token; el token va unicamente en el correo.
class RecuperaClave extends Modelo {
	// Crea un enlace valido por una hora y devuelve el token, o '' si no se pudo guardar.
	public function crear($idLiving){
		$token = bin2hex(random_bytes(32));
		$tokenHash = hash('sha256', $token);
		$idLiving = (int)$idLiving;
		$stmt = $this->bd->preparar("INSERT INTO tblrecuperaclave (STRTOKEN, NUMIDENTLIV, DTMEXPIRA) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
		if (!$stmt) {
			return '';
		}
		$stmt->bind_param("si", $tokenHash, $idLiving);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok ? $token : '';
	}

	// Usuario al que pertenece un token vigente, o 0.
	public function usuarioDe($token){
		$tokenHash = hash('sha256', (string)$token);
		$idLiving = 0;
		$stmt = $this->bd->preparar("SELECT NUMIDENTLIV FROM tblrecuperaclave WHERE STRTOKEN = ? AND DTMEXPIRA > NOW()");
		if ($stmt) {
			$stmt->bind_param("s", $tokenHash);
			$stmt->execute();
			$stmt->bind_result($idLiving);
			$stmt->fetch();
			$stmt->close();
		}
		return (int)$idLiving;
	}

	public function borrarDe($idLiving){
		return $this->bd->consulta("DELETE FROM tblrecuperaclave WHERE NUMIDENTLIV = ".(int)$idLiving);
	}
}
