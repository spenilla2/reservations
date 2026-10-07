<?php
			$htmlCorreo='
					<!DOCTYPE html>
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
						    <title>Sistema para el Registro de Reservas (SIRR)</title>
						    <link href="http://reservas.livingnightclub.com.co/css/bootstrap.css" rel="stylesheet" type="text/css">
						<style>
							body{
								background: url(http://reservas.livingnightclub.com.co/images/fondo1.jpg) no-repeat center center fixed; 
    						    -webkit-background-size: cover;
							    -moz-background-size: cover;
							    -o-background-size: cover;
							    background-size: cover;
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
						</style>
						</head>

						<header>
						</header>
						<body>
							
								<header>
									<img class="head" src="http://reservas.livingnightclub.com.co/images/header.jpg">
								</header><br>
									<section class="container" style="border:1px solid white;background-color: white;border-radius: 0.5em 0.5em 0.5em 0.5em;
									    -moz-border-radius: 0.5em 0.5em 0.5em 0.5em;
									    -webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;
									    -webkit-box-shadow: 15px 15px 44px -7px rgba(0,0,0,0.32);
									    -moz-box-shadow: 15px 15px 44px -7px rgba(0,0,0,0.32);
									    box-shadow: 15px 15px 44px -7px rgba(0,0,0,0.32);
									    "><br>
									<p>Estimado(a) <b>'.strtoupper(htmlspecialchars_decode($Nombre).' '.htmlspecialchars_decode($Apellido)).'</b>,
									  <br>Su cuenta ha sido creada satisfactoriamente
									</p>  
									<p align="center">
									   <b>Usuario:</b> '.$Correo.'<br>
									</p>
									<p>
									  Active su cuenta <a href="http://reservas.livingnightclub.com.co/active.php?id='.$idUsuario.'">AQUI</a>
									</p>
									<div style="text-align:right;">
										<strong>Ingenieria y Desarrollo Santiago Penilla &copy; '.date('Y').'</strong><br><br><br>
									</div>	
								</section>
									
								
						</body>
					</html>
			';
echo $htmlCorreo;
