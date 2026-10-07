<?php
$html='
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	<link href="http://reservas.livingnightclub.com.co/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	
	<style>
		html,body{
			height: 50%;
			width:90%;
			background-color: #fff;
			font-size : 12pt;
			padding-left:20px;
			padding-right:20px;
		}
		header{
								width: 60%;
								height: 8em;
								left: 20%;
								position: relative;
								text-align: center;
							}
		.head{
			width: 100%;
			height: 100%;
			max-width: 400px;
			max-height: 132px;
			min-width: 	150px;
			min-height: 1em;
			border-radius: 0.5em 0.5em 0.5em 0.5em;
		    -moz-border-radius: 0.5em 0.5em 0.5em 0.5em;
		    -webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;
		}
		.section1{
			font-size: 1.50em;
			background: #F3F2F2;
			border:0.1em solid #000;
			border-radius: 0.5em 0.5em 0.5em 0.5em;
		    -moz-border-radius: 0.5em 0.5em 0.5em 0.5em;
		    -webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;
		    margin:0.5em;
		    padding:0.5em;
		    display: inline-block;
		    text-align: left;
		    max-width:99%;
		    min-width:88%;
		    -webkit-box-shadow: 15px 15px 44px -7px rgba(0,0,0,0.32);
    		-moz-box-shadow: 15px 15px 44px -7px rgba(0,0,0,0.32);
    		box-shadow: 15px 15px 44px -7px rgba(0,0,0,0.32);	
		}

	</style>
	<title>Sistema para el Registro de Reservas (SIRR)</title>
</head>
<body>
<header>
	<img class="head" src="http://www.reservas.livingnightclub.com.co/images/header.jpg">
</header>
<section class="container" style="margin-top:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;
		    -moz-border-radius: 0.5em 0.5em 0.5em 0.5em;
		    -webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;border:1px solid black;padding-top:2em;padding-left:2em;">
		Estimado(a): <b>'.$NombreUser.',</b>
		<br><br>
		Ser&aacute; un placer recibirlo a usted y a sus acompa&ntilde;antes en las instalaciones de <b>LIVING NIGHT CLUB</b>, y le agradecemos de antemano por habernos escogido entre la gran cantidad de opciones con que cuenta la ciudad de Santiago de Cali y sus alrededores.
		<br>
		<br>Por medio del presente Correo Electr&oacute;nico tenemos el agrado de confirmar su reserva seg&uacute;n los siguientes criterios:

		<ul>
			<li>D&iacute;a de la Reserva: <b>'.$FechaReserva.'</b></li>
			<li>Reserva a nombre de: <b>'.$NombreReserva.'</b></li>
			<li>Cantidad de Personas: <b>'.$Personas.'</b></li>
			<li>Motivo de la Reserva: <b>'.$Motivo.'</b></li>
			<li>Nombre de los Homenajeados y/o Reconocimiento especial para:<br><b> '.$Homenajeados.'.</b></li>
			<li>N&uacute;mero de Reserva: <b>'.$Reserva.'.</b></li>
			<li>Observaciones: <br><b>'.$Observaciones.'.</b></li>
		</ul>
		<b>Recomendaciones Importantes:</b>
		<ol>
			<li>Las Reservas no tienen ning&uacute;n costo.</li>
			<li>Las Reservas se levantan a partir de las 10:30 p.m. de la noche de la Reserva.</li>
			<li>Para las reservas de Celebraci&oacute;n de Cumplea&ntilde;os de grupos de 10 personas en adelante obsequiamos:
			<br><br>
				<ul>
					<li>La Segunda Botella del Licor que est&eacute;s tomando.
						<br><p id="leyenda"><strong>Deben de estar en la discoteca por lo menos 10 personas de la reserva.</strong></p>
					</li>
				</ul>
				<br>
			</li>
			<li><b>LIVING NIGHT CLUB</b> se reserva el derecho de admisi&oacute;n para comodidad suya y de sus invitados, lo invitamos a seguir las siguientes recomendaciones:
				<br><br>
				<ul>
					<li>No se permite el Ingreso de Personas alcoholizadas.</li>
					<li>No se permite el Ingreso de Menores de Edad.</li>
					<li>V&iacute;stase acorde a la Ocasi&oacute;n y al lugar.</li>
					<li>No se permite el ingreso de Hombres Solos.</li>
					<li>No se permite el ingreso de Mujeres en Sandalias, Zapatillas (a excepci&oacute;n de los d&iacute;as Jueves Freaknights).</li>
					<li>No se permite el ingreso de Mujeres Embarazadas 
						<br>(Esta demostrado que los altos decibeles afectan el desarrollo normal del feto en gestaci&oacute;n).
					</li>
					<li>No se permite el Ingreso de Zapatillas deportivas &oacute; con colores Fluorescentes asi como Zapatillas extravagantes.</li>
					<li>No se permite el ingreso ni consumo de sustancias Alucin&oacute;genas.</li>
					<li>No se permite el ingreso de ning&uacute;n tipo de Armas de fuego y/o blancas.</li>
					<li>No se permite portar Gorras, Boinas, Sombreros, etc. dentro del establecimiento.</li> 
					<li>No se permite el uso de carrieles o bolsos masculinos dentro del Establecimiento.</li>
					<li><b>LIVING NIGHT CLUB</b> es un establecimiento libre de Humo por lo tanto no se permite fumar dentro de nuestras Instalaciones.</li>
					<li>En <b>LIVING NIGHT CLUB</b> no se permite el ingreso de licor.</li>
					<li>En <b>LIVING NIGHT CLUB</b> no se permite el ingreso de Alimentos a excepci&oacute;n de Mani y bombones.</li>
				</ul>

			</li>
		</ol>
		Cordialmente,
		<br><br>
		<b>EQUIPO DE TRABAJO LIVING NIGHT CLUB</b>
	</div>
</div>

</body>
</html>
';
echo $html;
