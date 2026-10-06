<?php
require_once("conect.php");
global $conexion;

$consulta = $conexion->query("select * from tblocupacion order by 2 asc")
         or die(errorBD());
$numocupa = $consulta->num_rows;
$consulta1 = $conexion->query("select * from tblprofesion order by 2 asc")
         or die(errorBD());
$numprof = $consulta1->num_rows;
$consulta2 = $conexion->query("select * from tblpais order by 2 asc")
         or die(errorBD());
$numpais = $consulta2->num_rows;
$consulta3 = $conexion->query("select * from tbltiporumba order by 1 asc")
         or die(errorBD());
$numrumba = $consulta3->num_rows;
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<title>Sistema para el Registro de Reservas (SIRR)</title>
	<script language="JavaScript" type="text/javascript" src="popcalendar.js"></script>
	<script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
	<style>
		body{
			background-color: black;
			}
	</style>
</head>
<body>
	
<header>
		<img class="head" src="images/header.jpg">
</header>
<section class="container" style="background-color: white;">
	<form id="registro" class="form-horizontal mitad" action="Querys.php" method="POST" onsubmit="Registrar()">
		<h2>REGISTRO DE USUARIOS NUEVOS</h2>
		<fieldset>
		   <input type=hidden id="guard" name="guard" value="R">
		   <input type=hidden id="VMail" name="VMail" value="">
		   <div class="form-group">
				<label class="col-lg-3 control-label">
					Nombres :
				</label>
				<div class="col-lg-3">
 					<input class="form-control" type="text" id="name" title="Se debe de escribir un Nombre" name="name" placeholder="1er y 2do Nombre" value="" required>
				</div>
 		   </div>
		   <div class="form-group">
 				<label class="col-lg-3 control-label">
 					Apellidos :
 				</label>
 				<div class="col-lg-3">
 					<input class="form-control" type="text" name="apell" id="apell" placeholder="1er y 2do Apellido" value="" required>
 				</div>
 		   </div>
 		   <div class="form-group">
 				<label class="col-lg-3 control-label">
 					G&eacute;nero :
 				</label>
 				<div class="col-lg-9">
 					<label class="radio-inline">
 					<input type="radio" id="genero" name="genero" value="M"> 
 						Masculino
 					</label>
 					<label class="radio-inline">
 					<input type=radio name="genero" id="genero" value="F">
 						Femenino
 					</label>
 				</div>
 		   </div>	
	       <div class="form-group">
 				<label class="col-lg-3 control-label">
 					Fecha de Nacimiento :
 				</label>
				<div class="col-lg-3">
 					<input class="form-control" name="fechanac" Placeholder="dd/mm/yyyy" type="date" id="fechanac" value="" required>
 		 		</div>
 		   </div>
 		   <div class="form-group">
 				<label class="col-lg-3 control-label">
 					Correo Electr&oacute;nico :
 				</label>
 				<div class="col-lg-3">
 					<input class="form-control" type=mail id="mail" name="mail" placeholder="correo@ejemplo.com" title="correo@ejemplo.com" value="" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" oninput="checkMailExist(this,1)" required>
				</div>
	 	   </div>
 		   <div class="form-group">
 				<label class="col-lg-3 control-label">
 					E-Mail Confirmaci&oacute;n :
 				</label>
 				<div class="col-lg-3">
 					<input class="form-control" type=mail id="mail2" name="mail2" placeholder="correo@ejemplo.com" title="correo@ejemplo.com" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" oninput="checkMail(this,1)" value="" title="" required>
				</div>
		   </div>
		   <div class="form-group">
				<label class="col-lg-3 control-label">
					Ocupaci&oacute;n :
				</label>
				<div class="col-lg-3">
 					<select class="form-control" id="Ocupacion" name="Ocupacion" required>
	      				<option value="">Selecciona una Opci&oacute;n</option>
	      				<?php 
	      					for($i=0;$i<$numocupa;$i++){
	      						$res=$consulta->fetch_array();
	      							echo '<option value="'.$res[0].'">'.$res[1].'</option>';
	      				 	}
	      				 ?>	
	      			</select>
				</div>
		   </div>
		   <div class="form-group">
			 	<label class="col-lg-3 control-label">
			 		Tel&eacute;fono :
			 	</label>
 				<div class="col-lg-3">
 					<input class="form-control" type="tel" id="telefono" name="telefono" placeholder="N&uacute;mero telef&oacute;nico" value="" required>
				</div>
			 </div>
		   <div class="form-group">
			 	<label class="col-lg-3 control-label">
			 		Tipo de Rumba :
			 	</label>
 				<div class="col-lg-3">
 					<select class="form-control" id="trumba" name="trumba" required>
	      				<option value="">
	      					Seleccione una Opci&oacute;n
	      				</option>
	      			<?php
	      				for($i=0;$i<$numrumba;$i++){
	      					$res=$consulta3->fetch_array();
	      						echo '<option value="'.$res[0].'">'.htmlentities($res[1]).'</option>';
	      				 }
	      			?>		
	      			</select>
				</div>
		   </div>		
		   <div class="form-group">
			 	<label class="col-lg-3 control-label">
			 		Contrase&ntilde;a :
			 	</label>
 				<div class="col-lg-3">
 					<input class="form-control" type="password" id="pass" name="pass" placeholder="Contrase&ntilde;a" value="" required>
				</div>
		   </div>
		   <div class="form-group">
				<label class="col-lg-3 control-label">
			 		Confirmaci&oacute;n de Contrase&ntilde;a :
			 	</label>
 				<div class="col-lg-3">
 					<input class="form-control" type="password" id="passc" name="passc" placeholder="Confirmar la Contrase&ntilde;a" value="" oninput="checkMail(this,2)" required>
				</div>
		   </div>
		   <div >
		   		<div class="form-group" style="padding-left: 20px;">
 				<div class="checkbox col-lg-9 col-lg-offset-3">
 					<input type="checkbox" id="news" name="news" value="S" checked> Deseo Recibir Noticias de Living Night Club y Eventos.
 				</div>
		   </div>
		   <div class="form-group" style="padding-left: 20px;">
 				<div class="checkbox col-lg-9 col-lg-offset-3 col-md-6 col-xs-12">
 					<input type="checkbox" id="hdata1" name="hdata1" onclick="hdata()"> He Le&iacute;do y Acepto las condiciones de tratamiento de Informaci&oacute;n establecidas <a href="hdata.php">AQUI</a>.
 				</div>
		   </div>
		   
		   </div>		
		   <div class="form-group">
 				<div class="col-lg-9 col-lg-offset-3">
 					<button type="submit" id="btnreg" class="btn btn-primary" disabled>
 						Realizar Registro
 					</button>
 				</div>
		   </div>
		</fielset>
	</form>
</section>
</body>

</html>

