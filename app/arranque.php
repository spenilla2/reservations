<?php
// Punto comun de arranque: rutas base y carga automatica de clases.
define('SIRR_RAIZ', dirname(__DIR__));
define('SIRR_APP', __DIR__);

spl_autoload_register(function($clase){
	foreach (array('nucleo', 'modelos', 'controladores') as $carpeta) {
		$archivo = SIRR_APP."/".$carpeta."/".$clase.".php";
		if (is_file($archivo)) {
			require_once($archivo);
			return;
		}
	}
});
