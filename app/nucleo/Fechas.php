<?php
// Operaciones con fechas en formato DD/MM/YYYY, como las envian los formularios.
class Fechas {
	// Dias entre dos fechas (primera - segunda); 0 si alguna no es valida.
	public static function diferenciaDias($primera, $segunda){
		$valoresPrimera = explode("/", $primera);
		$valoresSegunda = explode("/", $segunda);
		$diaPrimera = $valoresPrimera[0];
		$mesPrimera = $valoresPrimera[1];
		$anyoPrimera = $valoresPrimera[2];
		$diaSegunda = $valoresSegunda[0];
		$mesSegunda = $valoresSegunda[1];
		$anyoSegunda = $valoresSegunda[2];
		$diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);
		$diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);
		if (!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)) {
			return 0;
		} elseif (!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)) {
			return 0;
		}
		return $diasPrimeraJuliano - $diasSegundaJuliano;
	}

	// "sabado 12 de diciembre de 2026" a partir de DD/MM/YYYY.
	public static function textoLargo($fecha){
		$dias = array('domingo', 'lunes', 'martes', 'mi&eacute;rcoles', 'jueves', 'viernes', 's&aacute;bado');
		$meses = array(1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		$dia = (int)substr($fecha, 0, 2);
		$mes = (int)substr($fecha, 3, 2);
		$anio = (int)substr($fecha, 6, 4);
		if (!checkdate($mes, $dia, $anio)) {
			return htmlspecialchars($fecha);
		}
		return $dias[self::diaSemana($fecha)]." ".$dia." de ".$meses[$mes]." de ".$anio;
	}

	// Dia de la semana (0 = domingo) de una fecha DD/MM/YYYY.
	public static function diaSemana($fecha){
		return date("w", mktime(12,0,0,(substr($fecha, 3,2)),(substr($fecha, 0,2)),(substr($fecha, 6,4))));
	}
}
