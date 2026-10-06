<?php
require_once("sesion.php");
$_SESSION["autenticado"]="Y";
$login=$_SESSION["nombre"];
$UAcces=$_SESSION['ultimoAcceso'];
$Perfil=$_SESSION['Perfil'];
$iniciales=$_SESSION['iniciales'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Sistema para Registro de Reservas</title>
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/estilo.css">
	<link rel="stylesheet" type="text/css" href="css/estilo_salon.css">
	<style type="text/css">
		body{
			padding-top:85px;
			background: url(images/fondo1.jpg) no-repeat center center fixed; 
		    -webkit-background-size: cover;
		    -moz-background-size: cover;
		    -o-background-size: cover;
		    background-size: cover;
		}
	</style>
	<script src="js/jquery-latest.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/scripts.js"></script>
	<script src="js/popcalendar.js"></script>
</head>
<body>
		<header>
			<nav class="navbar navbar-default navbar-fixed-top navbar-inverse">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-1">
							<span class="sr-only">Menu</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>	
						</button>
						<a href="main.php" class="navbar-brand">
							Living Night Club
						</a>
					</div>
					<div class="collapse navbar-collapse" id="navbar-1">
						<ul class="nav navbar-nav">
							<li class="dropdown">
								<a href="" class="dropdown-toggle" data-toggle="dropdown">
									Reservas 
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="reserva2.php?Tipo=I&MA=0">
											<span class="glyphicon glyphicon-calendar"></span> Realizar Reserva
										</a>
									</li>
									<li>
										<a href="consultaReservas.php">
											<span class="glyphicon glyphicon-eye-open"></span> Consultar Reservas
										</a>
									</li>
								</ul>
							</li>
							<?php if($Perfil==1){
							?>
							<li class="dropdown">
								<a href="" class="dropdown-toggle" data-toggle="dropdown">
									Gesti&oacute;n 
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="ConfReserva.php"><span class="glyphicon glyphicon-ok"></span> Confirmar Reservas</a>
									</li>
									<li>
										<a href="AsignaMesa.php"><span class="glyphicon glyphicon-screenshot"></span> Asignar Mesas</a>
									</li>
									<li>
										<a href="levantaReservas.php"><span class="glyphicon glyphicon-fire"></span> Levantar Reservas</a>
									</li>
									<li>
										<a href="GestionSalon.php"><span class="glyphicon glyphicon-link"></span> Gesti&oacute;n en el Sal&oacute;n</a>
									</li>
									<li>
										<a href="ListarReservas.php?tipo=1"><span class="glyphicon glyphicon-th-list"></span> Consulta Reservas</a>
									</li>
									<li>
										<a href="ListarReservas.php?tipo=2"><span class="glyphicon glyphicon-th-list"></span> Consulta Reservas Efectivas</a>
									</li>
								</ul>
							</li>
							<li class="dropdown">
								<a href="" class="dropdown-toggle" data-toggle="dropdown">
									Clientes VIP 
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li>
										<a href="#"><span class="glyphicon glyphicon-info-sign"></span> Creaci&oacute;n de Cliente</a>
									</li>
									<li>
										<a href="#"><span class="glyphicon glyphicon-eye-open"></span> Consultar Clientes VIP</a>
									</li>
								</ul>
							</li>
							<?php }else{

						   }?>
						</ul>

						<ul class="nav navbar-nav navbar-right">
							<li>
								<a href="">
									<span class="glyphicon glyphicon-user"></span>
									<?php echo ' '.$iniciales;?>
								</a>
							</li>
							<li><a href="desconexion.php"><span class="glyphicon glyphicon-remove"></span> Cerrar Sesi&oacute;n</a></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
