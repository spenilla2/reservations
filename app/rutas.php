<?php
// Tabla de rutas. Cada entrada es array(Controlador, metodo, nivel de acceso):
//   'publico' sin sesion, 'sesion' cualquier usuario conectado, 'admin' solo perfil administrador.
return array(
	// Archivos de entrada en la raiz del sitio
	'paginas' => array(
		'registro.php'         => array('AccesoControlador', 'formularioRegistro', 'publico'),
		'active.php'           => array('AccesoControlador', 'activar', 'publico'),
		'welcome.php'          => array('AccesoControlador', 'bienvenida', 'publico'),
		'desconexion.php'      => array('AccesoControlador', 'salir', 'publico'),
		'rpas.php'             => array('AccesoControlador', 'formularioRecuperar', 'publico'),
		'restablecer.php'      => array('AccesoControlador', 'formularioRestablecer', 'publico'),
		'hdata.php'            => array('AccesoControlador', 'condiciones', 'publico'),

		'main.php'             => array('ReservaControlador', 'inicio', 'sesion'),
		'reserva2.php'         => array('ReservaControlador', 'nueva', 'sesion'),
		'reserva.php'          => array('ReservaControlador', 'editar', 'sesion'),
		'consultaReservas.php' => array('ReservaControlador', 'consultar', 'sesion'),
		'correoReserva.php'    => array('ReservaControlador', 'correo', 'sesion'),

		'ConfReserva.php'      => array('GestionControlador', 'paginaConfirmar', 'admin'),
		'AsignaMesa.php'       => array('GestionControlador', 'paginaAsignar', 'admin'),
		'levantaReservas.php'  => array('GestionControlador', 'paginaLevantar', 'admin'),
		'ListarReservas.php'   => array('GestionControlador', 'paginaListar', 'admin'),

		'GestionSalon.php'     => array('SalonControlador', 'paginaGestion', 'admin'),
		'dashboard.php'        => array('SalonControlador', 'dashboard', 'admin'),
		'reservoir.php'        => array('SalonControlador', 'mapa', 'admin'),
		'Salon.php'            => array('SalonControlador', 'plano', 'publico'),
		'GestionMesa.php'      => array('SalonControlador', 'mesa', 'admin'),
		'GestionMesaA.php'     => array('SalonControlador', 'reserva', 'admin'),
		'Listados.php'         => array('SalonControlador', 'listado', 'admin'),
		'ListaCumple.php'      => array('SalonControlador', 'cumpleanos', 'admin'),
		'ListaGrado.php'       => array('SalonControlador', 'grados', 'admin'),

		'eventos.php'          => array('EventoControlador', 'administrar', 'admin'),
	),

	// Acciones de Querys.php, segun el parametro guard
	'acciones' => array(
		'P'          => array('AccesoControlador', 'login', 'publico'),
		'M'          => array('AccesoControlador', 'existeCorreo', 'publico'),
		'R'          => array('AccesoControlador', 'registrar', 'publico'),
		'RCE'        => array('AccesoControlador', 'recuperar', 'publico'),
		'RST'        => array('AccesoControlador', 'restablecer', 'publico'),

		'ResForm'    => array('ReservaControlador', 'formulario', 'sesion'),
		'RR'         => array('ReservaControlador', 'crear', 'sesion'),
		'RU'         => array('ReservaControlador', 'actualizar', 'sesion'),
		'E'          => array('ReservaControlador', 'cancelar', 'sesion'),

		'CR'         => array('GestionControlador', 'detalle', 'admin'),
		'CRE'        => array('GestionControlador', 'confirmar', 'admin'),
		'CRF'        => array('GestionControlador', 'pendientesDeMesa', 'admin'),
		'CAM'        => array('GestionControlador', 'selectorMesas', 'admin'),
		'AMR'        => array('GestionControlador', 'asignarMesas', 'admin'),
		'AMR1'       => array('GestionControlador', 'asignarMesasSegunEstado', 'admin'),
		'LRes'       => array('GestionControlador', 'paraLevantar', 'admin'),
		'LResScript' => array('GestionControlador', 'levantar', 'admin'),
		'BRF'        => array('GestionControlador', 'listado', 'admin'),
		'BR'         => array('GestionControlador', 'buscar', 'admin'),
		'ConfEntrM'  => array('GestionControlador', 'cortesia', 'admin'),

		'TMCD'       => array('SalonControlador', 'colores', 'admin'),
		'BMOD'       => array('SalonControlador', 'ocupadas', 'admin'),
		'STRE'       => array('SalonControlador', 'filtro', 'admin'),
		'CCliVip'    => array('SalonControlador', 'opcionesAsignacion', 'admin'),
		'IRCVIP'     => array('SalonControlador', 'reservaEnMesa', 'admin'),
		'ARMN'       => array('SalonControlador', 'asignarDesdeTablero', 'admin'),
		'LMM'        => array('SalonControlador', 'liberar', 'admin'),
		'AMCP'       => array('SalonControlador', 'personasCompartida', 'admin'),
		'TMS'        => array('SalonControlador', 'trasladar', 'admin'),
		'INMM'       => array('SalonControlador', 'alternarLlena', 'admin'),

		'RCV'        => array('ClienteVipControlador', 'crear', 'admin'),
		'ACV'        => array('ClienteVipControlador', 'actualizar', 'admin'),
		'BCVIP'      => array('ClienteVipControlador', 'buscar', 'admin'),

		'EVT'        => array('EventoControlador', 'proximos', 'publico'),
	),
);
