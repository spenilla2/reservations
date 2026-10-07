<?php
		$htmlCorreo='
					<!DOCTYPE html>
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
						    <title>Sistema para el Registro de Reservas (SIRR)</title>
						<style>
							body{
								background: #000;
								height: 100%;
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
					    text-align: center;
					    max-width:99%;
					    min-width:88%;
					}

						</style>
						</head>

						<header>
						</header>
						<body>
							
								<header>
									<img class="head" src="http://reservas.livingnightclub.com.co/images/header.jpg">
								</header>
								<section class="section1">
									<p>Estimado(a) <b>'.htmlspecialchars_decode($Nombre).' '.htmlspecialchars_decode($Apellido).'</b>,
									  <br>Realizo un procedimiemto para recuperar los datos de registro
									</p>  
									<p align="center">
									   <b>Usuario :</b> '.$correo.'<br>
									   <a href="'.$enlace.'">Crear una nueva contrase&ntilde;a</a>
									</p>
									<p>
									   Este enlace es v&aacute;lido por una hora. Si no solicit&oacute; el cambio, ignore este mensaje.
									</p>
										
								</section>
						</body>
					</html>
			';
echo $htmlCorreo;
