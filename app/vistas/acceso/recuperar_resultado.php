<?php
		$html='
			<!DOCTYPE html>
			<html>
			<head>
				<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
				</header><br>
				<section class="container" style="background-color: white;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">';	
					if($enviado==1){
					   $html.='
					   	<h2>Si el correo est&aacute; registrado, se ha enviado un enlace de recuperaci&oacute;n al buz&oacute;n : '.htmlspecialchars($correo).'. </h2>
					   ';
					}else{
						$html.='
					   	<h2>Se ha producido un error por favor comuniquese con el Administrador. </h2>
					   ';
					}
					
				$html.='<button type="button" id="btnreg" onclick="GoPrin()" class="btn btn-primary">
			 									Ir al Inicio
			 		</button>
			 		<br>
			 		<br>
				</section>	
			</body>
			</html>';
			echo $html;
