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
	<div class="container" style="background-color: white;border-radius: 0.5em 0.5em 0.5em 0.5em;
    -moz-border-radius: 0.5em 0.5em 0.5em 0.5em;
    -webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
		<form id="registro" method="post" class="form-horizontal mitad" action="Querys.php" method="POST" onsubmit="recuppass()">
		<fieldset>
		<input type=hidden id="guard" name="guard" value="RCE">
				<h2>RECUPERACI&Oacute;N DE CONTRASE&Ntilde;A</h2>
					<div class="form-group">
 						<label class="col-lg-3 control-label">
 								Digite su Correo Electr&oacute;nico :
 						</label>
 						<div class="col-lg-3">
 							<input class="form-control" type=mail id="mail" name="mail" placeholder="correo@ejemplo.com" title="correo@ejemplo.com" value="" onkeyup="Minus(this)" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" oninput="checkMailExist(this,2)" required>
						</div>
	 			 	</div>
 					<div class="form-group">
 						<div class="col-lg-9 col-lg-offset-3">
 							<button type="submit" id="btnreg" class="btn btn-primary">
 									Recuperar Contrase&ntilde;a
 							</button>
 						</div>
					</div>
		</fieldset>
		</form>

	</div>
</body>
</html>

