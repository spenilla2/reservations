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
<?php if($vigente){ ?>
		<form id="restablecer" class="form-horizontal mitad" onsubmit="return RestablecerClave()">
		<fieldset>
		<input type="hidden" id="token" value="<?php echo htmlspecialchars($token);?>">
				<h2>NUEVA CONTRASE&Ntilde;A</h2>
					<div class="form-group">
 						<label class="col-lg-3 control-label">
 								Contrase&ntilde;a :
 						</label>
 						<div class="col-lg-3">
 							<input class="form-control" type="password" id="pass" placeholder="Contrase&ntilde;a" minlength="6" required>
						</div>
	 			 	</div>
					<div class="form-group">
 						<label class="col-lg-3 control-label">
 								Confirmar Contrase&ntilde;a :
 						</label>
 						<div class="col-lg-3">
 							<input class="form-control" type="password" id="passc" placeholder="Confirmar la Contrase&ntilde;a" minlength="6" required>
						</div>
	 			 	</div>
 					<div class="form-group">
 						<div class="col-lg-9 col-lg-offset-3">
 							<button type="submit" id="btnreg" class="btn btn-primary">
 									Guardar Contrase&ntilde;a
 							</button>
 						</div>
					</div>
		</fieldset>
		</form>
<?php }else{ ?>
		<h2>El enlace no es v&aacute;lido o ya expir&oacute;.</h2>
		<p>Solicite uno nuevo <a href="rpas.php">AQUI</a>.</p>
		<br>
<?php } ?>
	</div>
<script>
function RestablecerClave(){
	var pass = document.getElementById("pass").value;
	var passc = document.getElementById("passc").value;
	if(pass != passc){
		alert("Las contraseñas no coinciden");
		return false;
	}
	// misma codificacion que usa el login (ValidaPass)
	var passCifrado = Base64.encode(pass);
	var ajax = objetoAjax();
	ajax.open('POST', "Querys.php", true);
	ajax.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	ajax.send('guard=RST&token='+encodeURIComponent(document.getElementById("token").value)+'&pass='+encodeURIComponent(passCifrado));
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			if(ajax.responseText.toString().trim()=="OK"){
				alert("Contraseña actualizada. Ya puede iniciar sesión.");
				location.href="index.html";
			}else{
				alert("El enlace no es válido o ya expiró. Solicite uno nuevo.");
				location.href="rpas.php";
			}
		}
	}
	return false;
}
</script>
</body>
</html>
