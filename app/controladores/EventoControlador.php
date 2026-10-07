<?php
// Eventos (conciertos): administracion para el perfil administrador y listado publico para la cartelera del login.
class EventoControlador extends Controlador {
	// eventos.php: lista y formulario (GET) y guardar / eliminar (POST). Tras cada cambio se redirige a la lista.
	public function administrar(){
		$eventos = new Evento();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (Entrada::post('accion') == 'eliminar') {
				$this->eliminar($eventos);
			} else {
				$this->guardar($eventos);
			}
			return;
		}

		$enEdicion = Entrada::getEntero('editar') > 0 ? $eventos->porId(Entrada::getEntero('editar')) : null;
		$hoy = date('Y-m-d');
		$aviso = Sesion::dato('aviso_eventos');
		unset($_SESSION['aviso_eventos']);
		$this->pagina('eventos/administrar', array(
			'aviso' => $aviso,
			'enEdicion' => $enEdicion,
			'hoy' => $hoy,
			'proximos' => $this->conRutas($eventos->proximos($hoy)),
			'pasados' => $this->conRutas($eventos->pasados($hoy)),
		));
	}

	// guard=EVT (publico): eventos de hoy en adelante, en JSON, para la cartelera del login.
	public function proximos(){
		$eventos = new Evento();
		$lista = array();
		foreach ($this->conRutas($eventos->proximos(date('Y-m-d'))) as $evento) {
			if ($evento['rutaFrente'] != "") {
				$lista[] = array(
					'nombre' => $evento['nombre'],
					'fecha' => $evento['fecha'],
					'fechaTexto' => html_entity_decode($evento['fechaTexto'], ENT_QUOTES, 'UTF-8'),
					'frente' => $evento['rutaFrente'],
					'precios' => $evento['rutaPrecios'],
				);
			}
		}
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($lista);
	}

	private function guardar(Evento $eventos){
		$id = Entrada::postEntero('id');
		$actual = $id > 0 ? $eventos->porId($id) : null;
		$nombre = trim(Entrada::post('nombre'));
		$fecha = Entrada::post('fecha');
		$errores = array();

		if ($id > 0 && !$actual) {
			$this->volver("El evento ya no existe.", false);
		}
		if ($nombre == "") {
			$errores[] = "Escribe el nombre del evento.";
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !checkdate((int)substr($fecha, 5, 2), (int)substr($fecha, 8, 2), (int)substr($fecha, 0, 4))) {
			$errores[] = "Elige una fecha v&aacute;lida.";
		} elseif ($eventos->fechaOcupada($fecha, $id)) {
			$errores[] = "Ya hay otro evento registrado para esa fecha.";
		}
		// al crear, las dos imagenes son obligatorias; al editar solo se cambian las que se envien
		$imagenes = array('frente' => "La imagen frontal", 'precios' => "La imagen de precios");
		foreach ($imagenes as $campo => $rotulo) {
			if (!$actual && !ImagenSubida::enviada($campo)) {
				$errores[] = $rotulo." es obligatoria.";
			}
		}
		if ($errores) {
			$this->volver(implode(" ", $errores), false, $id);
		}

		$archivos = array('frente' => $actual ? $actual['frente'] : "", 'precios' => $actual ? $actual['precios'] : "");
		$reemplazados = array();
		$nuevos = array();
		foreach ($imagenes as $campo => $rotulo) {
			if (ImagenSubida::enviada($campo)) {
				list($archivo, $error) = ImagenSubida::guardar($campo, $this->carpeta(), "evento_".$campo);
				if ($error != "") {
					// no se deja en disco la otra imagen si ya se habia guardado en este mismo intento
					foreach ($nuevos as $nuevo) {
						@unlink($this->carpeta()."/".$nuevo);
					}
					$this->volver($rotulo." ".$error.".", false, $id);
				}
				$reemplazados[] = $archivos[$campo];
				$archivos[$campo] = $archivo;
				$nuevos[] = $archivo;
			}
		}

		$recibeReservas = Entrada::post('reservas') == 'S';
		if ($actual) {
			$eventos->actualizar($id, $nombre, $fecha, $archivos['frente'], $archivos['precios'], $recibeReservas);
		} else {
			$eventos->crear($nombre, $fecha, $archivos['frente'], $archivos['precios'], $recibeReservas);
		}
		foreach ($reemplazados as $anterior) {
			$this->borrarArchivo($eventos, $anterior);
		}
		$this->volver($actual ? "Evento actualizado." : "Evento creado.", true);
	}

	private function eliminar(Evento $eventos){
		$evento = $eventos->porId(Entrada::postEntero('id'));
		if (!$evento) {
			$this->volver("El evento ya no existe.", false);
		}
		$eventos->eliminar($evento['id']);
		$this->borrarArchivo($eventos, $evento['frente']);
		$this->borrarArchivo($eventos, $evento['precios']);
		$this->volver("Evento eliminado.", true);
	}

	// Deja un mensaje para la siguiente carga de la pagina y redirige a ella.
	private function volver($texto, $bien, $editar = 0){
		$_SESSION['aviso_eventos'] = array('texto' => $texto, 'bien' => $bien);
		$this->redirigir("eventos.php".($editar > 0 ? "?editar=".(int)$editar : ""));
	}

	private function carpeta(){
		return SIRR_RAIZ."/images/Eventos";
	}

	// Borra del disco una imagen que ya no usa ningun evento.
	private function borrarArchivo(Evento $eventos, $archivo){
		$archivo = basename((string)$archivo);
		if ($archivo != "" && !$eventos->archivoEnUso($archivo) && is_file($this->carpeta()."/".$archivo)) {
			@unlink($this->carpeta()."/".$archivo);
		}
	}

	// Agrega a cada evento la ruta web de sus imagenes ("" si el archivo no existe) y la fecha en palabras.
	private function conRutas(array $lista){
		foreach ($lista as $i => $evento) {
			$lista[$i]['rutaFrente'] = self::rutaImagen($evento['frente']);
			$lista[$i]['rutaPrecios'] = self::rutaImagen($evento['precios']);
			$lista[$i]['fechaTexto'] = Fechas::textoLargo(substr($evento['fecha'], 8, 2)."/".substr($evento['fecha'], 5, 2)."/".substr($evento['fecha'], 0, 4));
		}
		return $lista;
	}

	// Ruta web de una imagen de images/Eventos, o "" si el archivo no existe (para no pintar imagenes rotas).
	public static function rutaImagen($archivo){
		$archivo = basename((string)$archivo);
		if ($archivo == "" || !is_file(SIRR_RAIZ."/images/Eventos/".$archivo)) {
			return "";
		}
		return "images/Eventos/".rawurlencode($archivo);
	}
}
