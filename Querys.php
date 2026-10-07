<?php
// Archivo de entrada de las acciones AJAX y formularios: el parametro guard decide la ruta (ver app/rutas.php).
require(__DIR__."/app/arranque.php");
Despachador::accion();
// las respuestas siempre terminaron con este espacio en blanco; se conserva por los scripts que las leen
?>

		