<?php
// Envio de correos HTML con el remitente del club.
class Correo {
	public static function enviar($para, $titulo, $html, $remitente = 'info@livingnightclub.com.co', $copia = ''){
		$cabeceras = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$cabeceras .= 'From: Living Night Club<'.$remitente.'>';
		if ($copia != '') {
			$cabeceras .= "\r\n".'Cc: '.$copia;
		}
		return mail($para, $titulo, $html, $cabeceras);
	}
}
