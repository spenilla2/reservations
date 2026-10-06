<?php
require_once("conect.php");
global $conexion;
$id=entero($_GET['id']);
$fechaHoy=date("Y-m-d H:i:s");
$Query="select u.STRACTIVO from tblusuarios u where u.NUMIDENTLIV=".$id;
$Consulta = $conexion->query($Query)
		or die(errorBD());
$resultado=$Consulta->fetch_array();
$activo=$resultado[0];			
if($activo==1){
$Cadena='<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Mensaje:</strong>LA CUENTA YA SE ENCUENTRA ACTIVA!
		 </div>';
}else{
$Query="Update tblusuarios u set u.STRACTIVO=1, u.DTMFECHAULTINGRESO='".$fechaHoy."' where u.NUMIDENTLIV=".$id;
$Consulta = $conexion->query($Query)
		or die(errorBD());

if($Consulta==1){
	$Cadena='<div class="alert alert-success">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Mensaje:</strong> CUENTA ACTIVADA SATISFACTORIAMENTE!
			 </div>
					';
	$Query="INSERT INTO tblusuariosperfiles VALUES (".$id.",0)";
	$Consulta = $conexion->query($Query)
		or die(errorBD());
}
else{
	$Cadena="Ha Ocurrido un Error al momento de Activar la cuenta por favor intentelo de Nuevo!".$Consulta;
}
}
echo '
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<title>Sistema para el Registro de Reservas (SIRR)</title>
	<script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
</head>
<style>
body{
	background: url(images/fondo1.jpg) no-repeat center center fixed; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
</style>
<body>
<header>
		<img class="head" src="images/header.jpg">
</header>
<br>
<section class="container" style="border:1px solid white;background-color: white;border-radius: 0.5em 0.5em 0.5em 0.5em;
    -moz-border-radius: 0.5em 0.5em 0.5em 0.5em;
    -webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;"><br>
	'.$Cadena.'.
				
<input type="button" class="btn btn-primary" value="Volver" onclick="GoPrin()"><br><br>
</section>
';
include("foot.php");
?>