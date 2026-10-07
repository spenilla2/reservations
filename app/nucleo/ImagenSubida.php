<?php
// Recibe una imagen enviada por formulario y la guarda en una carpeta del sitio con un nombre propio.
// El nombre y la extension los decide el servidor a partir del contenido real del archivo, nunca del nombre original.
class ImagenSubida {
	const MAXIMO_BYTES = 5242880; // 5 MB

	private static $extensiones = array(IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp');

	// true si el campo trae un archivo (aunque luego resulte invalido).
	public static function enviada($campo){
		return isset($_FILES[$campo]) && $_FILES[$campo]['error'] != UPLOAD_ERR_NO_FILE;
	}

	// Guarda la imagen del campo en $carpeta (ruta absoluta) y devuelve array(nombre de archivo, '') o array('', error).
	public static function guardar($campo, $carpeta, $prefijo){
		$archivo = $_FILES[$campo];
		if ($archivo['error'] == UPLOAD_ERR_INI_SIZE || $archivo['error'] == UPLOAD_ERR_FORM_SIZE || $archivo['size'] > self::MAXIMO_BYTES) {
			return array('', "pesa demasiado (m&aacute;ximo 5 MB)");
		}
		if ($archivo['error'] != UPLOAD_ERR_OK || !is_uploaded_file($archivo['tmp_name'])) {
			return array('', "no se pudo recibir");
		}
		$info = @getimagesize($archivo['tmp_name']);
		if (!$info || !isset(self::$extensiones[$info[2]])) {
			return array('', "debe ser una imagen JPG, PNG o WEBP");
		}
		$nombre = $prefijo."_".bin2hex(random_bytes(6)).".".self::$extensiones[$info[2]];
		if (!@move_uploaded_file($archivo['tmp_name'], $carpeta."/".$nombre)) {
			error_log("SIRR: no se pudo guardar la imagen en ".$carpeta);
			return array('', "no se pudo guardar en el servidor");
		}
		@chmod($carpeta."/".$nombre, 0644);
		return array($nombre, '');
	}
}
