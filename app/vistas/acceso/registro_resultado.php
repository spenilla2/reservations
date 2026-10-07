<?php
			$html='
					<!DOCTYPE html>
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
						    <link href="css/style.css" rel="stylesheet" type="text/css">
						    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
						    <link href="css/tema.css" rel="stylesheet" type="text/css">
							<title>Sistema para el Registro de Reservas (SIRR)</title>
							<script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
						</head>
						<style>
							body{
							}
						</style>
						<body>
							
								<header>
									<img class="head" src="images/header.jpg">
								</header>
								<br>
								<section class="container" style="background-color: white;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;"><br>
									<p style="text-align:justify;">Estimado(a) <b>'.strtoupper($Nombre.' '.$Apellido).'</b> muchas gracias por registrarse en nuestra Base de Datos, su informaci&oacute;n es muy importante para nosotros, para continuar con el registro se ha enviado un mensaje de confirmaci&oacute;n al Correo Electr&oacute;nico: <b>'.$Correo.'</b>.<br><br>Por favor active su cuenta para poder realizar las Reservas.</p>
									<p align="center">
										<input type="button" class="btn btn-primary" value="Volver al Inicio" onclick="GoPrin()">
									</p>	
								<div style="text-align:right;">
								<strong>Ingenieria y Desarrollo Santiago Penilla &copy; '.date('Y').'</strong><br><br><br>
								</div>
								</section>
						</body>
					</html>
			';
echo $html;
