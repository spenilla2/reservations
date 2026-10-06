<?php
require_once("conect.php");
global $conexion;
$fechaHoy = date("Y-m-d");
$Guard = isset($_POST["guard"]) ? $_POST["guard"] : "";
if($Guard==""){
		$Guard = isset($_GET["guard"]) ? $_GET["guard"] : "";
}else{
		$Guard=$Guard;
}
// Control de acceso por accion: publicas (login y registro), de cliente y de gestion.
require_once("auth.php");
$accionesPublicas = array('P','M','R','RCE','RST');
$accionesCliente = array('RR','RU','E','ResForm');
if(in_array($Guard, $accionesPublicas, true)){
	// sin sesion
}elseif(in_array($Guard, $accionesCliente, true)){
	exigirSesionAjax();
}else{
	exigirAdminAjax();
}
function compararFechas($primera, $segunda)
 {
  $valoresPrimera = explode ("/", $primera);   
  $valoresSegunda = explode ("/", $segunda); 
  $diaPrimera    = $valoresPrimera[0];  
  $mesPrimera  = $valoresPrimera[1];  
  $anyoPrimera   = $valoresPrimera[2]; 
  $diaSegunda   = $valoresSegunda[0];  
  $mesSegunda = $valoresSegunda[1];  
  $anyoSegunda  = $valoresSegunda[2];
  $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);  
  $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);  if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
    // "La fecha ".$primera." no es válida";
    return 0;
  }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
    // "La fecha ".$segunda." no es válida";
    return 0;
  }else{
    return  $diasPrimeraJuliano - $diasSegundaJuliano;
  } 
}

switch ($Guard) {
	case 'RCE':
		$correo=texto(strtolower($_POST['mail']));
		$para=$correo;
		$Query="SELECT u.* 
				  FROM tblusuarios u 
				 WHERE u.STRMAIL='".$correo."'";
		$Consulta2 =$conexion->query($Query)
						or die(errorBD());
		$resultado=$Consulta2->fetch_array();
		$Nombre = $resultado[1];
		$Apellido = $resultado[2];
		// enlace de un solo uso, valido por una hora; en la base solo queda el SHA-256 del token
		$enlace = "";
		if($resultado){
			$token = bin2hex(random_bytes(32));
			$tokenHash = hash('sha256', $token);
			$idUsuario = (int)$resultado[0];
			$stmt = $conexion->prepare("INSERT INTO tblrecuperaclave (STRTOKEN, NUMIDENTLIV, DTMEXPIRA) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
			if($stmt){
				$stmt->bind_param("si", $tokenHash, $idUsuario);
				if($stmt->execute()){
					$enlace = "http://reservas.livingnightclub.com.co/restablecer.php?token=".$token;
				}
				$stmt->close();
			}else{
				errorBD();
			}
		}
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
			$titulo = 'Recuperacion de Cuenta en Living Night Club';
			$cabeceras = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$cabeceras .= 'From: Living Night Club<info@livingnightclub.com.co>';
			if(!$resultado){
				// correo no registrado: no se envia nada y se responde igual
				$enviado = 1;
			}elseif($enlace==""){
				$enviado = 0;
			}else{
				$enviado = mail($para, $titulo, $htmlCorreo, $cabeceras);
			}
		$html='
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
				</header><br>
				<section class="container" style="background-color: white;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">';	
					if($enviado==1){
					   $html.='
					   	<h2>Si el correo est&aacute; registrado, se ha enviado un enlace de recuperaci&oacute;n al buz&oacute;n : '.htmlspecialchars($_POST['mail']).'. </h2>
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
	break;
	case 'R':
		$Nombre=$_POST["name"];
		$Apellido=$_POST["apell"];
		$Genero=texto($_POST["genero"]);
		$FechaN=date('Y-m-d',strtotime($_POST["fechanac"]));
		$Correo=texto(strtolower($_POST["mail"]));
		$Ocupacion=entero($_POST["Ocupacion"]);
		$Profesion=1;//$_POST["Profesion"];
		$telefono=texto($_POST["telefono"]);
		$pais=1;
		$depto=1;
		$ciudad=1;
		$trumba=entero($_POST["trumba"]);
		$pass=texto(claveParaGuardar($conexion, $_POST["pass"]));
		$news=$_POST["news"]=="S"?"T":"F";
		$Query="INSERT INTO tblusuarios VALUES(NULL,'".texto(htmlspecialchars(strtoupper($Nombre)))."','"
													  .texto(htmlspecialchars(strtoupper($Apellido)))."','"
													  .$Genero."','"
													  .$FechaN."','"
													  .strtolower($Correo)."',"
													  .$Ocupacion.","
													  .$Profesion.",'"
													  .$telefono."',"
													  .$pais.","
													  .$depto.","
													  .$ciudad.","
													  .$trumba.",'"
													  .$pass."','"
													  .$fechaHoy."','"
													  .$news."','','','','','0','')";
		$Consulta =$conexion->query($Query)
				or die(errorBD());
		if($Consulta==1){
			$Query="SELECT u.NUMIDENTLIV 
			          FROM tblusuarios u 
			         WHERE u.STRMAIL='".$Correo."'";
			$Consulta2 =$conexion->query($Query)
				or die(errorBD());
			$resultado=$Consulta2->fetch_array();
			$html='
					<!DOCTYPE html>
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html" charset="utf-8"/>
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
			$para = $Correo;
			$titulo = 'Creacion de Cuenta Living Night Club';

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
									  Active su cuenta <a href="http://reservas.livingnightclub.com.co/active.php?id='.$resultado[0].'">AQUI</a>
									</p>
									<div style="text-align:right;">
										<strong>Ingenieria y Desarrollo Santiago Penilla &copy; '.date('Y').'</strong><br><br><br>
									</div>	
								</section>
									
								
						</body>
					</html>
			';
			$cabeceras = 'MIME-Version: 1.0' . "\r\n";
			$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$cabeceras .= 'From: Living Night Club<info@livingnightclub.com.co>';
			$enviado = mail($para, $titulo, $htmlCorreo, $cabeceras);
			if ($enviado)
			  $CCadena= 'Email enviado correctamente';
			else
  			  $CCadena= 'Error en el envío del email';	

			echo $html;
		}else{

		}
		//echo $Consulta
	break;
	case 'RST':
		$tokenHash = hash('sha256', isset($_POST['token']) ? (string)$_POST['token'] : '');
		$nuevaClave = isset($_POST['pass']) ? (string)$_POST['pass'] : '';
		$idUsuario = 0;
		$stmt = $conexion->prepare("SELECT NUMIDENTLIV FROM tblrecuperaclave WHERE STRTOKEN = ? AND DTMEXPIRA > NOW()");
		if($stmt){
			$stmt->bind_param("s", $tokenHash);
			$stmt->execute();
			$stmt->bind_result($idUsuario);
			$stmt->fetch();
			$stmt->close();
		}
		if($idUsuario>0 && $nuevaClave!="" && actualizarClave($conexion, $idUsuario, $nuevaClave)){
			// el enlace deja de servir, junto con cualquier otro pendiente del mismo usuario
			$conexion->query("DELETE FROM tblrecuperaclave WHERE NUMIDENTLIV = ".(int)$idUsuario);
			echo "OK";
		}else{
			echo "E1";
		}
	break;
	case 'M':
		$correo = texto($_POST['correo']);
		$Query = "select count(*) 
		            from tblusuarios u 
		           where u.STRMAIL='".$correo."'";
		$Consulta = $conexion->query($Query)
					or die(errorBD());
		$resultado=$Consulta->fetch_array();
		$n=$resultado[0];			
		echo $n;
		break;
	case 'P':
		$user = strtolower($_POST['user']);
		$passc = $_POST['passC'];
		$stmt = $conexion->prepare("select u.NUMIDENTLIV,
		                                   u.STRPASSWORD,
		                                   u.STRACTIVO
		                              from tblusuarios u
		                             where u.STRMAIL = ?");
		$stmt->bind_param("s", $user);
		$stmt->execute();
		$stmt->bind_result($idUsuario, $passGuardado, $activo);
		$existe = $stmt->fetch();
		$stmt->close();
		if($existe){
			if($passc!="" && verificarClave($passc, $passGuardado)){
				// las claves en formato anterior pasan a hash en el primer ingreso correcto
				if(!claveEsHash($passGuardado) && columnaClaveAdmiteHash($conexion)){
					actualizarClave($conexion, $idUsuario, $passc);
				}
				if($activo==1){
					// welcome.php solo crea la sesion si el login quedo validado aqui
					session_regenerate_id(true);
					$_SESSION['login_validado'] = $user;
					echo "UO";
				}else{
					echo "E3";
				}
				
			}else{
				echo "E2";
			}
		}else{
			echo "E1";
		}
	break;
	case 'RR':
	    $IdLiving = entero($_POST['id']);
		$NombreRes = $_POST["NombreRese"];
		$NumPerson = entero($_POST["Numper"]);
		$TipoReserva = entero($_POST["TipoR"]);
		$FechaReserva = texto(substr($_POST["Fecha"],6,4)."-".substr($_POST["Fecha"],3,2)."-".substr($_POST["Fecha"],0,2));
		$NombreHomena = $_POST["NHomena"];
		$RObserva = $_POST["Observa"];
		$ClienteVip = entero($_POST["ClienteVIP"]);
		if(!esAdmin()){
			// un cliente solo reserva a su nombre y sin cliente VIP
			$IdLiving = idLivingSesion();
			$ClienteVip = 0;
		}
		$FechaRealizaRes = date("Y-m-d H:i:s");
		$Resultado1 = "";
		$sql = "SELECT * 
					FROM tblreservas r 
				   WHERE r.NUMIDENTLIV=".$IdLiving." 
				     AND r.DTMFECHARESERVA='".$FechaReserva."'
				     AND r.STRNOMBRERESERVA='".texto($NombreRes)."'
				     AND r.STRNOMBREHOMENAJE='".texto($NombreHomena)."' 
				     AND r.NUMMOTIVO=".$TipoReserva;
		$query  = $conexion->query($sql);
		$n = $query->num_rows;
		if($n>=1){
			$Resultado1="EI1";
		}else{
			$Resultado1="AA2";
			$sql="INSERT INTO tblreservas VALUES(null,'".$FechaReserva."',"
			                                              .$IdLiving.",'"
			                                              .texto(htmlentities(htmlspecialchars(strtoupper(rtrim(ltrim($NombreRes))))))."',"
			                                              .$NumPerson.","
			                                              .$TipoReserva.",'"
			                                              .texto(htmlspecialchars(strtoupper($NombreHomena)))."','"
			                                              .texto(htmlspecialchars(strtoupper($RObserva)))."','"
			                                              .$FechaRealizaRes."',1,'"
			                                              .$FechaRealizaRes."','',"
			                                              .$ClienteVip.",'N');";
			$query = $conexion->query($sql);
		    if($query){
		    	$sql="SELECT max(numreserva) 
		    	        FROM tblreservas 
		    	       WHERE NUMIDENTLIV = ".$IdLiving." 
		    	         AND DTMFECHARESERVA = '".$FechaReserva."'";
				$query  = $conexion->query($sql);
				$Resu = $query->fetch_array();
				$Resultado1="IC1;".$Resu[0];
		    }else{
		    	$Resultado1="False";
		    }		    
		}			
		echo $Resultado1;	
	break;
	case 'RU':
		$IdLiving=entero($_POST['id']);
		$NombreRes=$_POST["NombreRese"]; 
		$NumPerson=entero($_POST["Numper"]);
		$TipoReserva=entero($_POST["TipoR"]);
		$FechaReserva=texto(substr($_POST["Fecha"],6,4)."-".substr($_POST["Fecha"],3,2)."-".substr($_POST["Fecha"],0,2));
		$NombreHomena=$_POST["NHomena"];
		$RObserva=$_POST["Observa"];
		$FechaRealizaRes=date("Y-m-d H:i:s");
		$Reserva=(int)$_POST['NumReserva'];
		if(!reservaPermitida($conexion, $Reserva)){
			echo "False";
			break;
		}
		$Query="UPDATE tblreservas
		           SET STRNOMBRERESERVA = '".texto(htmlspecialchars(strtoupper($NombreRes))).
		           "', NUMPERSONAS = ".$NumPerson.
		            ", NUMMOTIVO = ".$TipoReserva.
		            ", STRNOMBREHOMENAJE = '".texto(htmlspecialchars(strtoupper($NombreHomena))).
		           "', STROBSERVACIONES = '".texto($RObserva).
		            "' WHERE NUMRESERVA = ".$Reserva;
		$Consulta=$conexion->query($Query);
		if($Consulta){
			$Resultado1="UC1;".$Reserva;
		} else{
			$Resultado1="False";
		}
		echo $Resultado1;
		//echo $Query;
	break;
	case "E":
		$Reserva=(int)$_GET['Reserva'];
		if(!reservaPermitida($conexion, $Reserva)){
			echo '<script>
					alert("No tiene permisos sobre esta reserva!");
					location.href="consultaReservas.php";
				  </script>';
			break;
		}
		$Query="UPDATE tblreservas
		           SET NUMESTADO = 0
		         WHERE NUMRESERVA = ".$Reserva;
		$Consulta=$conexion->query($Query);
		if($Consulta){
			echo '<script>
					alert("Reserva Cancelada Satisfactoriamente!");
					location.href="ListarReservas.php?tipo=1";
				  </script>';
		
		}else{
			echo '<script>
					alert("Ha Ocurrido un Error!");
					location.href="ListarReservas.php?tipo=1";
				  </script>';
		
		}
		
	break;
	case 'CR':
		$Reserva=entero($_POST["NumReserva"]);
		$Query = "SELECT r.NUMRESERVA,
						 r.DTMFECHARESERVA,
						 CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
						 r.STRNOMBRERESERVA,
						 r.NUMPERSONAS,
						 t.STRDESCRIPCION,
						 r.STRNOMBREHOMENAJE,
						 r.STROBSERVACIONES,
						 e.STRDESCRIPCION,
						 r.DTMFECHAREALIZARES,
						 r.STRMESAASIGNA	
				    FROM tblreservas r,
				         tblusuarios u,
				         tbltipocelebra t,
				         tblestados e 
				   WHERE r.NUMRESERVA = ".$Reserva." 
				     AND r.NUMIDENTLIV = u.NUMIDENTLIV
				     AND r.NUMMOTIVO = t.NUMMOTIVO
				     AND r.NUMESTADO = e.NUMESTADO";
		$Consulta = $conexion->query($Query)
					or die(errorBD());
		$n = $Consulta->num_rows;
		$html="";
		if($n==0){
			$html='
				<p style="color:#FF0000;font-weight:bold;">Reserva no Existe en la Base de Datos</p>
			';
		}else{
			$resultado=$Consulta->fetch_array();
			if($resultado[10]==""){
				$html='<p class="perror">
						La reserva aun no se le a asignado una Mesa
					   </p>';
			}else{
				$html='
			  		<b>Fecha de Reserva : </b>&nbsp;'.$resultado[1].'<br>
			  		<b>Realizada Por : </b>&nbsp;'.$resultado[2].'<br>
			  		<b>Reserva a Nombre de : </b>&nbsp;'.$resultado[3].'<br>
			  		<b>N&uacute;mero de Personas : </b>&nbsp;'.$resultado[4].'<br>
			  		<b>Tipo de Reserva : </b>&nbsp;'.htmlentities($resultado[5]).'<br>
			  		<b>Nombre del Homenajead@ : </b>&nbsp;'.$resultado[6].'<br>
			  		<b>Observaciones : </b>
			  			<br>'.$resultado[7].'<br>
			  		<b>Mesa(s) Asignada(s) : </b>&nbsp;'.$resultado[10].'<br>
			  		<b>Estado de la Reserva : </b>&nbsp;'.$resultado[8].'<br>
			  		<b>Reserva Realizada : </b>&nbsp;'.$resultado[9].'<br>
			  		<b>Reserva No : </b>&nbsp;'.$resultado[0].'<br>';
			  if($resultado[8]=="ACTIVA"){
			  	$html.='<br>
			  			<input type="button"  class="btn btn-primary" value="Confirmar Reserva" onclick="ConfirmaReserva('.$resultado[0].')">';
			  }else{
			  	$html.='';
			  }	
			}
			
		}
		
		echo $html;
	break;
	case 'CRE':
		$Reserva=entero($_POST["NumReserva"]);
		$FechaReporta = date("Y-m-d H:i:s");
		$sql = "UPDATE tblmesareserva r 
		             SET r.STRTIPO = 'C' 
		           WHERE r.NUMRESERVA = ".$Reserva;
		$queryMR  = $conexion->query($sql);
		
		$sql = "UPDATE tblreservas r 
		           SET r.NUMESTADO = 3, 
		               r.DTMFECHAESTADO = '".$FechaReporta."' 
		         WHERE r.NUMRESERVA = ".$Reserva;
		$queryReserva  = $conexion->query($sql);
		$Mensaje="";
		if($queryMR){
			$Mensaje='
				<div class="col-md-12">
				<div class="alert alert-success">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Mensaje:</strong> Reserva Confirmada Satisfactoriamente! <br>Bienvenido a Living Night Club.
				</div>
			</div>';
		}else{
			$Mensaje='<div class="col-md-12">
				<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> No existen Reservas para este d&iacute;a
				</div>
			</div>';
		}
		echo $Mensaje;
	break;
	case 'CRF':
		$Fecha=texto(substr($_POST["Fecha"], 6,4)."-".substr($_POST["Fecha"], 3,2)."-".substr($_POST["Fecha"], 0,2));
		$Query = "SELECT * 
		   FROM tblreservas R 
		  WHERE R.DTMFECHARESERVA='".$Fecha."' 
		    AND R.NUMESTADO=1 
		    AND R.NUMRESERVA NOT IN(
						SELECT M.NUMRESERVA 
						  FROM tblmesareserva M 
						 WHERE M.DTMFECHARESERVA = '".$Fecha."'
						   AND M.STRESTADORESERVA = '1')
			ORDER BY R.STRNOMBRERESERVA
						   ";
		$Consulta = $conexion->query($Query)
					or die(errorBD());
		$n=$Consulta->num_rows;			
		$Mensaje="";
		if($n>0){
			$Mensaje='
						<form class="form-inline">
				            <div class="form-group">
				              <label class="sr-only">Seleccione el Cliente :  </label>
				              <p class="form-control-static"><strong>Seleccione el Cliente :  </strong></p>
				            </div>
				            <div class="form-group">
				              <select id="cliente" class="form-control" onchange="AsignaMesas()">
									<option value="0">Selecciona</option>';
										for ($i=0; $i <$n ; $i++) { 
										$resu=$Consulta->fetch_array();
										$Mensaje.='<option value="'.$resu[0].';'.$resu[4].'">['.$resu[0].'] '.$resu[3].' --> ('.$resu[4].')</option>';
										}
					$Mensaje.='</select>
				            </div>
           				</form>
					   <br>
					   <div id="gestion1">
					   	  <br><br><br><br>
					    </div>
			';
		}else{
			$Mensaje='<p class="perror">NO EXISTEN RESERVAS PENDIENTES PARA ASIGNAR MESA PARA EL DIA '.$Fecha.'</p>';
		}
		echo $Mensaje;
	break;
	case 'CAM':
		$CantMesas = entero($_POST["cantMesas"]);
		$FechaReserva = texto(substr($_POST["fecha"], 6,4).'-'.substr($_POST["fecha"], 3,2).'-'.substr($_POST["fecha"], 0,2));
		$Query = "SELECT * 
		   FROM tblmesas M 
		  WHERE M.STRMESA NOT IN(
						SELECT MR.STRMESA 
						  FROM tblmesareserva MR 
						 WHERE MR.DTMFECHARESERVA = '".$FechaReserva."'
						   AND MR.STRESTADORESERVA = '1'
						 )";
		$Cadena='
		<form class="form-inline mitad">';
		for($i=0;$i<$CantMesas;$i++){
			$Cadena.='
			<div class="form-group">
              <label class="sr-only">Mesa No. '.($i+1).': </label>
              <p class="form-control-static"><strong>Mesa No. '.($i+1).': </strong></p>
            </div>
			<div class="form-group">
				<select id="M'.($i+1).'" class="form-control" onchange="validaIgual('.$CantMesas.')">
					<option value="0">Selecciona una Mesa</option>
						';
			$Consulta = $conexion->query($Query)
					or die(errorBD());
			$n=$Consulta->num_rows;
			for($j=0;$j<$n;$j++){
				$Res=$Consulta->fetch_array();
				$Cadena.='<option value="'.$Res[0].'">'.$Res[0].' ( '.$Res[1].' )</option>';
			}
			$Cadena.="</select></div><br>";
		}
		$Cadena.='</form><br>
		<input type="checkbox" value="S" id="respecial"> Reserva Especial<br><br>
		<button class="btn btn-primary" type="button" id="basigna" align="center" onclick="AsignaMesa('.$CantMesas.')">Asignar Mesa(s)</button>
			  <br><br><br><br>';
		echo $Cadena;
	break;
	case "AMR":
		$FechaReserva = texto(substr($_POST["Fecha"], 6,4).'-'.substr($_POST["Fecha"], 3,2).'-'.substr($_POST["Fecha"], 0,2));
		$Reserva = entero($_POST["Reserva"]);
		$CMesas = entero($_POST["CMesas"]);
		$MesasAsigna = explode(",", texto($_POST["AMesas"]));
		$FechaRealizaAsigna=date("Y-m-d H:i:s");
		$ReservaEspecial=$_POST["REspecial"];
		$R="N";
		
		if($ReservaEspecial=="true"){
			$R="E";
		}else{
			$R="N";
		}
		$sql="";
		for($i=0;$i<$CMesas;$i++){
			$sql = "INSERT INTO tblmesareserva VALUES(NULL,'".$FechaReserva.
			                                             "',".$Reserva.
			                                             ",'".$MesasAsigna[$i].
			                                             "','".$FechaRealizaAsigna.
			                                             "','".$R.
			                                             "','1')";
			$query = $conexion->query($sql);

		}
		$sql = "UPDATE tblreservas R 
		           SET R.STRMESAASIGNA = '".texto($_POST["AMesas"])."' 
		         WHERE R.NUMRESERVA = ".$Reserva;
		$query = $conexion->query($sql);
		
		$Mensaje="";
		if($query){
			$Mensaje='<ul><p class="pbien">Mesas Asignadas Satisfactoriamente!</p></ul>';
		}else{
			$Mensaje="Error";
		}
		echo $Mensaje;
		
	break;
	case "AMR1":
		$FechaReserva = texto(substr($_POST["Fecha"], 6,4).'-'.substr($_POST["Fecha"], 3,2).'-'.substr($_POST["Fecha"], 0,2));
		$Reserva = entero($_POST["Reserva"]);
		$CMesas = entero($_POST["CMesas"]);
		$MesasAsigna = explode(",", texto($_POST["AMesas"]));
		$FechaRealizaAsigna=date("Y-m-d H:i:s");
		$ReservaEspecial=$_POST["TRes"];
		$Estado =$_POST["Estado"];
		$R="N";
		switch($Estado){
			case '1':
				if($ReservaEspecial=="true"){
					$R="E";
				}else{
					$R="N";
				}
			break;
			case '3':
				$R="C";
			break;
		}
		$sql="";
		for($i=0;$i<$CMesas;$i++){
			$sql="INSERT INTO tblmesareserva VALUES(NULL,'".$FechaReserva.
			                                             "',".$Reserva.
			                                             ",'".$MesasAsigna[$i].
			                                             "','".$FechaRealizaAsigna.
			                                             "','".$R.
			                                             "','1')";
			$query  = $conexion->query($sql);
		}
		$sql = "UPDATE tblreservas R 
		           SET R.STRMESAASIGNA = '".texto($_POST["AMesas"])."' 
		         WHERE R.NUMRESERVA = ".$Reserva;
		$query = $conexion->query($sql);
		$Mensaje="";
		if($query){
			$Mensaje='<ul><p class="pbien">Mesas Asignadas Satisfactoriamente!</p></ul>';
		}else{
			$Mensaje="Error";
		}
		echo $Mensaje;
	break;
	
	case "RCV":
		$Confirma=$_POST["Confirma"];
		$IdentLiv=entero($_POST["identLiv"]);
		$name=texto($_POST["name"]);
		$genero=texto($_POST["genero"]);
		$telefono=texto($_POST["telefono"]);
		$trumba=entero($_POST["trumba"]);
		$FechaCreacion=date("Y-m-d H:i:s");
		$Observa="[".$FechaCreacion."]: ".texto($_POST["Observa"]);
		if($Confirma=="T"){
			$Query="INSERT INTO tblclientesvip VALUES(NULL, '".$name.
			                                             "','".$genero.
			                                              "',".$IdentLiv.
			                                               ",".$trumba.
			                                              ",'".$telefono.
			                                             "','".$FechaCreacion.
			                                             "','".$Observa."')";
			$Consulta = $conexion->query($Query)
					or die(errorBD());
			if($Consulta){
              echo '<script>alert("Cliente VIP '.$name.' fue Creado Satisfactoriamente!");</script>';
			}else{
			  echo '<script>alert("Ha Ocurrido un error al momento de insertar la Informacion en la Base de Datos");</script>'; 
			}			
			$res='<script>
					location.href="ClientVIP.php";
				  </script>';
		}else{
			echo '<script>
					location.href="ClientVIP.php";
				  </script>';
			
		}	   
		echo $res;	   
	break;
	case "ACV":
		$IdClienteVip=entero($_POST["IdClienteVip"]);
		$IdLiving=entero($_POST["IdLiving"]);
		$Nombre=texto($_POST["Nombre"]);
		$Genero=texto($_POST["Genero"]);
		$TRumba=entero($_POST["TRumba"]);
		$Telefono=texto($_POST["Telefono"]);
		$VObserva=$_POST["VObserva"];
		$NObserva=$_POST["NObserva"];
		$Query = "SELECT CONCAT(strnombres,' ', strapellidos) 
		            FROM tblusuarios u 
		           WHERE u.NUMIDENTLIV = ".$IdLiving."";
		$Consulta = $conexion->query($Query)
					or die(errorBD());
		$Res=$Consulta->fetch_array();
		if($NObserva!=""){
			$NCad=$VObserva."<br>"."[<b>".date("Y-m-d H:i:s")." - ".$Res[0]."</b>]: ".$NObserva;
		}else{
			$NCad=$VObserva;
		}
		$QueryU="UPDATE tblclientesvip v SET v.STRNOMBRECLIENTE = '".$Nombre.
		                                 "', v.STRGENERO = '".$Genero.
		                                 "', v.NUMTIPORUMBA = ".$TRumba.
		                                  ", v.STRNUMTEL = '".$Telefono.
		                                 "', v.STROBSERVACIONES = '".texto($NCad).
		                            "' WHERE v.IDCLIENTEVIP = ".$IdClienteVip;
		$Consulta = $conexion->query($QueryU)
					or die(errorBD());
			
		echo $Consulta;
	break;
	case "BCVIP":
		$CBusq=$_POST["Nom"];
		$QuerySelect="select vp.*,tr.STRDESCRIPCION 
						from tblclientesvip vp, tbltiporumba tr 
					   where tr.NUMTIPORUMBA=vp.NUMTIPORUMBA
						 and vp.STRNOMBRECLIENTE like '%".texto(strtoupper($CBusq))."%' 
				    order by 2 asc";
		$Consulta = $conexion->query($QuerySelect)
					or die(errorBD());
		$n=$Consulta->num_rows;			
		$VarConsulta='
		<table class="tablaconsulta" width="100%">
							<tr>
								<td id="tittabla" width="2%">No.</td>
								<td id="tittabla">NOMBRE</td>
								<td id="tittabla">TEL&Eacute;FONO</td>
								<td id="tittabla">TIPO DE RUMBA</td>
								<td id="tittabla" colspan=2 width="5%">GESTI&Oacute;N</td>
							</tr>';
				for($i=0;$i<$n;$i++){
		  			$res=$Consulta->fetch_array();
		  			  $VarConsulta.= '<tr>
		  						<td id="tittabla">'.($i+1).'</td>
					  			<td>'.htmlentities($res[1]).'</td>
					  			<td>';
					  			if($res[3]==$IdLiving){
					  				$VarConsulta.=$res[5];
					  				
					  			}else{
					  				$VarConsulta.='';
					  				
					  			}
					  $VarConsulta.='</td>
					  			<td>'.$res[8].'</td>
					  			<td>
					  				<a href="GestionCVP.php?idCliVip='.$res[0].'">
			                       		<img src="images/edit.png" width="20px" height="20px" title="Modificar Cliente">
			                      	</a>
			                    </td>
					  			<td>
					  				<img src="images/drop.png" width="22px" height="22px" title="Cancelar Cliente">
					  			</td>
					  		</tr>
				 ';
				}
				$VarConsulta.="</table>";			
		echo "".$VarConsulta;
	break;
	case "CCliVip":
		$Codigo=entero($_POST["Cod"]);
		$Fecha=htmlspecialchars($_POST["fecha"]);
		$Variable="";
		switch ($Codigo) {
			case 1:
				$QuerySelect="SELECT vp.* 
            				  FROM tblclientesvip vp 
            				  ORDER BY 1 DESC";
				$Consulta1 = $conexion->query($QuerySelect)
    						 or die(errorBD());
				$filasCV = $Consulta1->num_rows;
				$consulta3 = $conexion->query("SELECT * FROM tbltipocelebra ORDER BY 2 ASC")
         					or die(errorBD());
				$numcelebra = $consulta3->num_rows;
				$Variable=$Fecha.'
				<table class="tdasiM" border="1">
						<tr>
							<td>
								Selecciona el Cliente VIP
							</td>
							<td>
								<select id="clivips" onchange="traeNombre()">
          							<option value=""></option>';
          						for($i=0;$i<$filasCV;$i++){
            						$resulCV=$Consulta1->fetch_array();
            						$Variable .='<option value="'.$resulCV[0].'">'.htmlentities($resulCV[1]).'</option>';
          						}
          		$Variable.='</select>
								</td>
						</tr>
						<tr>
							<td>
								Nombre del Cliente
							</td>
							<td>
								<input type="text" id="AMCliN" value="" size="30" disabled>				
							</td>
						</tr>
						<tr>
							<td>
								N&uacute;mero de Personas
							</td>
							<td>
								<select id="CantPer">
          							<option value="0"></option>';
          							for($i=2;$i<=60;$i++){
          								$Variable.='<option value="'.$i.'">'.$i.'</option>';	
          							}
          							$Variable.='				
							</td>
						</tr>
						<tr>
							<td>
							  Tipo de Celebraci&oacute;n
							</td>
							<td>
								<select id="AMTCeb" onclick="ValidaCelebra()">';
    								for($i=0;$i<$numcelebra;$i++){
    									$result=$consulta3->fetch_array()
    											or die(errorBD());
    									$cadena12="";
    									if($result[0]==5){
    										$cadena12=" selected";
    									}		
                                        $Variable.='<option value='.$result[0].$cadena12.'>'.htmlentities($result[1]).'</option>';
    								}
    								$Variable.='</select>
							</td>
							 <tr>
							 	<td>
							 		Nombre del Homenajeado
							 	</td>
							 	<td>
							 		<input type="text" id="AMTHome" size=30   disabled>
							 	</td>
							 </tr>
							
						</tr>
						<tr>
							<td>Observaciones</td>
							<td><input type="text" value="" size="30" id="AMObse" onkeyup=Mayus(this)></td>
						</tr>
						<tr>
							<td colspan=2 align="right">
								<input type="button" value="Aceptar" onclick=AlmacenaRCVIP()>
							</td>
						</tr>
				</table>

				';
				
			break;
			case 2:
				$sql = "SELECT * FROM tbltipocelebra ORDER BY 2 ASC";
				$query = $conexion->query($sql);
				$numcelebra = $query->num_rows;
				$Variable='
				<div>
					<b>Descripci&oacute;n del Cliente</b>
					<br>
					<select id="CFSe" onchange="GestionForaneo()">
				   			<option value=0>Selecciona una Opci&oacute;n</option>
				   			<option value=1>Define Nombre</option>
				   			<option value=2>Pareja</option>
				   			<option value=3>Grupo 3</option>
				   			<option value=4>Grupo 4</option>
				   			<option value=5>Grupo 5</option>
				   			<option value=6>Grupo 6</option>
				   			<option value=7>Grupo 7</option>
				   			<option value=8>Grupo 8</option>
				   			<option value=9>Grupo 9</option>
				   			<option value=10>Grupo 10</option>
				   			<option value=11>Grupo 11</option>
				   			<option value=12>Grupo 12</option>
				   			<option value=13>Grupo 13</option>
				   			<option value=14>Grupo 14</option>
				   			<option value=15>Grupo 15</option>
				   			<option value=20>Grupo 20</option>
				   			<option value=21>Grupo 20+</option>
				   		</select><br>
				   	<b>Nombre del Cliente</b><br>
				   	<input type="text" id="CFIn1" size=25 disabled><br>
				   	<b>N&uacute;mero de Personas</b> <span id="cper"><h4>0</h4></span>
				   	<input type="range" class="form-control-range" min=2 max=15 onchange="control(2)" id="CantPer" Value =2><br>
				   	<b>Tipo de Celebraci&oacute;n</b><br>
					<select id="AMTCeb" onclick="ValidaCelebra()">
					    <option value=0></option>
					    ';
					        
    						for($i=0;$i<$numcelebra;$i++){
    							$result = $query->fetch_array();
    									$cadena12="";
    									if($result[0]==$i){
    										$cadena12=" selected";
    									}else{
    										$cadena12="";
    									}		
                                        $Variable.='<option value='.$result[0].$cadena12.'>'.htmlentities($result[1]).'</option>';
    								}
    								$Variable.='
    					</select>
    					<br><b>Nombre del Homenajeado</b><br>
    					<input type="text" id="AMTHome" size=25 disabled>
    					<br>
    					<b>Observaciones</b><br>
    					<input type="text" value="" size="25" id="AMObse" onkeyup=Mayus(this)>
    					<br><br>
    					<input class="btn btn-primary" type="button" value="Aceptar" onclick=AlmacenaRCFora()>		
				</div>
				';
			break;
			case 3:
				$Variable='<br>
				<div>
					<b>OBSERVACIONES</b><br><br>
							<input type="text" value="" size="40" id="AMObse" onkeyup=Mayus(this)>
							<BR><br>
							<button class="btn btn-primary" onclick="AlmacenaRCBloq()">Aceptar</button>
				</div>
				';
			break;
			case 4:
				$Variable='
					<div>
						<b>CANTIDAD DE PERSONAS</b> <span id="cper"><h4>0</h4></span>
						<input type="range" class="form-control-range" min=2 max=8 onchange="control(1)" id="CFSe" Value =2>
						<input type="button" class="btn btn-primary"  value="Aceptar" onclick="AlmacenaRMComparte()">
					</div>
				';
			break;
			default:
				$Variable="Sin Codificar";
			break;
		}
		
		echo $Variable;
	break;
	case "IRCVIP":
		$Mesa = texto($_POST["Mesa"]);
		$FechaAsigna = $_POST["FechaRes"];
		$NombreReserva = $_POST["Nombre"];
		$CantPersonas = entero($_POST["NumPer"]);
		$TipoCelebracion = entero($_POST["TipoCelebra"]);
		$NombreHomenajeado = $_POST["NombreH"];
		$Observa = $_POST["Observa"];
		$IdLiving = entero($_POST["IdLiving"]);
		$FechaRealizaRes=date("Y-m-d H:i:s");
		$IdCliVIP = entero($_POST["IdCliVIP"]);
		$TipoAsigna = texto($_POST["TipoAsigna"]);
		if(substr($FechaAsigna, 4,1)=='-'){
			$FechaAsigna = $FechaAsigna;
		}else{
			$FechaAsigna = substr($FechaAsigna, 6,4).'-'.substr($FechaAsigna, 3,2)."-".substr($FechaAsigna, 0,2);
		}
		$sql = "SELECT * 
				  FROM tblmesareserva mr 
				 WHERE mr.STRMESA='".$Mesa."' 
				   AND mr.strestadoreserva='1'
				   AND mr.dtmfechareserva='".texto($FechaAsigna)."'";
		$queryCMesa = $conexion->query($sql);
		$nfilasm = $queryCMesa->num_rows;
        if($nfilasm>0){
        	echo '<p class="perror">Error E8: Lo siento la Mesa '.$Mesa.' Ya se encuentra asignada!';
        }else{
        $sql = "INSERT INTO tblreservas VALUES(null, '".texto($FechaAsigna)."',".
					  								    $IdLiving.",'".
														texto(htmlentities(htmlspecialchars(strtoupper(rtrim(ltrim($NombreReserva))))))."',".
														$CantPersonas.",".
														$TipoCelebracion.",'".
														texto(htmlspecialchars(strtoupper($NombreHomenajeado)))."','".
														texto(htmlspecialchars(strtoupper($Observa)))."','".
														$FechaRealizaRes."',3,'".
														$FechaRealizaRes."','".
														$Mesa."',".
														$IdCliVIP.",'N')";
		$query  = $conexion->query($sql);
		if($query){
        	$sql = "SELECT Max(NUMRESERVA)
				      FROM tblreservas
				     WHERE NUMIDENTLIV =".$IdLiving;		
			$query = $conexion->query($sql);
			$resultado = $query->fetch_array();
			$NumReserva = $resultado[0];
			if($TipoCelebracion==1){
			    $TipoAsigna='T';
			}
			$sql = "INSERT INTO tblmesareserva VALUES(null, '".texto($FechaAsigna)."',"
			                                                  .$NumReserva.",'"
															  .$Mesa."','"
															  .$FechaRealizaRes."','"
															  .$TipoAsigna."',1)";
			$queryI = $conexion->query($sql);
			if($queryI){
				echo '<p class="pbien">MESA ASIGNADA SATISFACTORIAMENTE</p><input type="button" value="Volver" onClick="GoBack()">';
			}else{
				echo '<p class="perror">Error E7'.$Consulta;
			}													 
        }else{
        	echo '<p class="perror">Error E7</p>'.$Consulta;
        } 					
			
        }			    
	break;
	case 'LRes':
		$Mensaje="";
		$Fecha=$_POST["Fecha"];
		$FechaC=texto(substr($Fecha, 6,4).'-'.substr($Fecha, 3,2)."-".substr($Fecha, 0,2));
		$Query = "      SELECT r.*
						  FROM tblreservas r, tblmesareserva mr
						 WHERE r.numestado = 1
						   AND r.dtmfechareserva='".$FechaC."'
						   AND mr.numreserva=r.numreserva
						   AND mr.strtipo<>'E'";
		$Consulta = $conexion->query($Query)
         			or die(errorBD());
		$nfilas=$Consulta->num_rows;
		if($nfilas==0){
			$Mensaje='
				<p class="perror">
				No existen reservas para liberar!
				</p>
			';
		}else{

			$Mensaje='
					<input type="button" class="btn btn-primary" value="Levantar Reservas" onclick="LevantaReserva()">
					<input type="hidden" id="totres" value="'.$nfilas.'">
					<input type="hidden" id="fecha1" value="'.$FechaC.'">
					<table class="table table-hover table-bordered">
						<tr>
							<th>No.</th>
							<th>Reserva</th>
							<th>Cliente</th>
							<th>No. Personas</th>
							<th>Mesa</th>
							<th>Observaciones</th>
							<th>Mantener</th>
						</tr>';
			for($i=0;$i<$nfilas;$i++){
			$Res=$Consulta->fetch_array()
				 or die(errorBD());
			$Mensaje.='
			<tr>
				<td>'.($i+1).'</td>
				<td>'.$Res[0].'</td>
				<td>'.$Res[3].'</td>
				<td>'.$Res[4].'</td>
				<td>'.$Res[11].'</td>
				<td>'.$Res[7].'</td>
				<td><input type="checkbox" id="C'.($i+1).'" value="'.$Res[0].'"></td>
			</tr>

			';
			}
			$Mensaje.='
			</table>
			';
		}
		//$Mensaje=$nfilas;
		echo $Mensaje;
	break;
	case "LResScript":
		$Mensaje="";
		$Fecha=texto($_POST["Fecha"]);
		$ResMante=listaEnteros($_POST["ResMan"]);
		$sql="UPDATE tblreservas r 
				   SET r.numestado=2 
				 WHERE r.dtmfechareserva='".$Fecha."'
				   AND r.numestado=1
				   AND r.numreserva NOT IN(".$ResMante.")
				   AND r.numreserva NOT IN(
				   		SELECT mr.numreserva
				   		  FROM tblmesareserva mr
				   		 WHERE mr.dtmfechareserva='".$Fecha."'
				   		   AND mr.strtipo='E')";
		$query  = $conexion->query($sql);
		if($query){
		$sql1 = "UPDATE tblmesareserva mr 
				   SET mr.strestadoreserva=0 
				 WHERE mr.dtmfechareserva='".$Fecha."'
				   AND mr.numreserva IN(
				   		SELECT r.numreserva
				   		  FROM tblreservas r
				   		 WHERE r.dtmfechareserva='".$Fecha."'
				   		   AND r.numestado='2')";
		$query1  = $conexion->query($sql1);
		if($query1){
			$Mensaje='<p style="color:#008040;font-weight:bold;">
					Procedimiento de levantamiento de reservas ejecutado satisfactoriamente!
			</p>';
		}else{
			$Mensaje='<p style="color:#FF0000;font-weight:bold;">
					Ha Ocurrido un error! '.$query.'
			</p>';
		}	
		
		}else{
			$Mensaje='<p style="color:#FF0000;font-weight:bold;">
					Ha Ocurrido un error! '.$query.'
			</p>';
		}
		echo $Mensaje;
	break;
	case "LMM":
		$Mesa = texto($_POST["Mesa"]);
		$Fecha = texto($_POST["Fecha"]);
		$Reserva = entero($_POST["Reserva"]);
		$MesaN =texto($_POST["MesaN"]);
		$det=texto($_POST["centi"]);
		$cadena="";
		if($det==1){
			$sql = "UPDATE tblmesareserva m 
					   SET m.strestadoreserva=0
					 WHERE m.numreserva=".$Reserva;
			$queryMR  = $conexion->query($sql);

	        $sql = "UPDATE tblreservas r 
					   SET r.STRMESAASIGNA='',
					       r.numestado=5
					 WHERE r.numreserva=".$Reserva;
			$queryReserva  = $conexion->query($sql);
		}else{
			$sql = "UPDATE tblmesareserva m 
					   SET m.strestadoreserva=0
					 WHERE m.dtmfechareserva='".$Fecha."'
					   AND m.strmesa='".$Mesa."'";
			$queryMR  = $conexion->query($sql);
	        
			$sql = "UPDATE tblreservas r 
					   SET r.STRMESAASIGNA='".$MesaN."'
					 WHERE r.numreserva=".$Reserva;
			$queryReserva  = $conexion->query($sql);
	        
		}
		
        echo $queryMR; 					   
	break;
	case "AMCP":
		$Reserva=entero($_POST["Reserva"]);
		$CantPer=entero($_POST["CantPer"]);
		$Query1="";
		if($CantPer>=10){
		$sql = "UPDATE tblmesareserva m
					 SET m.strtipo='I'
				   WHERE m.numreserva=".$Reserva;
		$query  = $conexion->query($sql);
        	
		}else{

		}
		$sql = "UPDATE tblreservas r
					 SET r.numpersonas=".$CantPer."
				   WHERE r.numreserva=".$Reserva;
		$query  = $conexion->query($sql);
        echo $query;
	break;
	case "TMS":
		$Reserva=entero($_POST["Reserva"]);
		$MesaO=$_POST["MesaO"];
		$MesaD=$_POST["MesaD"];
		$Fecha=texto($_POST["Fecha"]);
		$MRConse=entero($_POST["MRConse"]);
		//$Mensaje=$MRConse;
		$sql="UPDATE tblmesareserva mr 
		            SET mr.strmesa='".texto($MesaD)."'
		          WHERE mr.numconsecutivo=".$MRConse;
		$query1  = $conexion->query($sql);
		$sql2="SELECT r.STRMESAASIGNA 
        		   FROM tblreservas r 
		          WHERE r.NumReserva=".$Reserva."";
		$query2  = $conexion->query($sql2);
		$Resu = $query2->fetch_array();
        $Cad1 = $Resu[0];
        $CadRes=str_replace($MesaO, $MesaD, $Cad1);
        $sql3="UPDATE tblreservas r
        			SET r.STRMESAASIGNA='".texto($CadRes)."' 
		          WHERE r.NumReserva=".$Reserva."";
		$query3  = $conexion->query($sql3);
        echo $query3;
	break;
	case 'BRF':
		$Fecha = texto($_POST["fecha"]);
		$Tipo = texto($_POST["tipo"]);
		$Rquery2="";
		if($Tipo==1){
			$Rquery2 = "";
		}else{
			$Rquery2 = " AND r.STRCUMPLERESERVA='S'";
		}
		$Query2 = "SELECT r.*,
                  t.STRDESCRIPCION, 
                  e.STRDESCRIPCION,
                  u.STRMAIL
             FROM tblreservas r, 
                  tbltipocelebra t,
                  tblestados e, 
                  tblusuarios u 
            WHERE r.DTMFECHARESERVA = '".$Fecha."' 
              AND r.NUMMOTIVO=t.NUMMOTIVO 
              AND r.NUMESTADO=e.NUMESTADO
              AND r.NUMESTADO in(1,3)
              AND r.NUMIDENTLIV = u.NUMIDENTLIV
              AND r.STRNOMBRERESERVA NOT IN('MESA BLOQUEADA','Mesa Compartida')
              AND r.STRNOMBRERESERVA NOT LIKE '%Grupo %'
              ".$Rquery2." 
         ORDER BY 4 ASC";
         $Consulta1 = $conexion->query($Query2);
		 $n=$Consulta1->num_rows;
		 $Query3 = "SELECT 
		 		  COUNT(*) as Total,
		 		  SUM(NUMPERSONAS) as PersonEsp
             FROM tblreservas r 
            WHERE r.DTMFECHARESERVA = '".$Fecha."' 
              AND r.NUMESTADO in(1,3)
              AND r.STRNOMBRERESERVA NOT IN('MESA BLOQUEADA','Mesa Compartida')
              AND r.STRNOMBRERESERVA NOT LIKE '%Grupo %'
              ".$Rquery2."";
        $Consulta3 = $conexion->query($Query3);
		$Resu3 = $Consulta3->fetch_array();
            					
		 if($n==0){
		 	echo '<div class="col-md-12">
				<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> No existen Reservas para este d&iacute;a: '.$Fecha.'
				</div>
			</div>';
		 }else{
		 	echo '
		 	<br>
		 	<b>Total Reservas: </b>'.$Resu3[0].' 
		 	<br><b>Cantidad de Personas Esperadas: </b>'.$Resu3[1].'
		 	<table class="table table-hover table-bordered">
	         <tr>
                <th>No.</th>
                <th>Nombre de Reserva</th>
                <th>Reserva</th>
	        	<th>Pers.</th>
	        	<th>Motivo</th>
	        	<th>Fecha de Reserva</th>
	        	<th>Estado</th>
                <th>Mesas</th>
                <th>Aplica Cortesia</th>
                <th>Realizada por</th>
  			  </tr>';
          for($i=0;$i<$n;$i++){
            $Resu = $Consulta1->fetch_array();
			
            $FormatFecha=substr($Resu[1], 8,2)."/".substr($Resu[1], 5,2)."/".substr($Resu[1], 0,4);
           echo '
            <tr>
                 <td align="right">'.($i+1).'</td>
              	 <td align="left">'. mb_strtoupper((html_entity_decode(utf8_encode($Resu[3]))),'UTF-8').'&nbsp;&nbsp;&nbsp;<a href="Querys.php?guard=E&Reserva='.$Resu[0].'">
                        <img src="images/drop.png" width="22px" height="22px" title="Cancelar Reserva">
                      </a>&nbsp;&nbsp;
                      <a href="reserva.php?fecha='.$FormatFecha.'&Tipo=U&Rnum='.$Resu[0].'&MA='.$Resu[11].'" rel="gb_page_center[600, 460]" title="Modificar Reserva ('.$FormatFecha.')">
                       <img src="images/edit.png" width="22px" height="22px" title="Modificar Reserva">
                      </a>
                 </td>
              	 <td align="right">'.$Resu[0].'</td>
              	 <td align="right">'.$Resu[4].'</td>
              	 <td align="center">'.utf8_encode($Resu[14]).'</td>
              	 <td align="right">'.$FormatFecha.'</td>
              	 <td>'.utf8_encode($Resu[15]).'</td>
                 <td>'.$Resu[11].'</td>
                 <td>'.$Resu[13].'</td>
                 <td>'.substr($Resu[16], 0,strpos($Resu[16],'@')).'</td>
            </tr>';
          }
        echo '
	        </table>

		 	';
		} 
	break;
	case 'BR':
		$Reserva=entero($_POST["Reserva"]);
		$Query2 = "SELECT r.*,
                  t.STRDESCRIPCION, 
                  e.STRDESCRIPCION 
             FROM tblreservas r, 
                  tbltipocelebra t,
                  tblestados e 
            WHERE r.numreserva=".$Reserva." 
              AND r.NUMMOTIVO=t.NUMMOTIVO 
              AND r.NUMESTADO=e.NUMESTADO
              AND r.NUMESTADO in(1,3) 
         ORDER BY 1 DESC";
         $Consulta1 = $conexion->query($Query2)
							or die(errorBD());
		 $n=$Consulta1->num_rows;
		 if($n==0){
		 	echo '<p class="perror">
		 			Reserva no Existe en la Base de Datos
		 		  </p>';
		 }else{
		 	echo '
		 	<br>
		 	<table class="table table-hover table-bordered">
	         <tr>
                <th>No.</th>
                <th>Nombre de Reserva</th>
                <th>Reserva</th>
	        	<th>Pers.</th>
	        	<th>Motivo</th>
	        	<th>Fecha de Reserva</th>
	        	<th>Estado</th>
                <th>Mesas</th>
	        	<th colspan=2></th>
  			  </tr>';
          for($i=0;$i<$n;$i++){
            $Resu=$Consulta1->fetch_array();
            $FormatFecha=substr($Resu[1], 8,2)."/".substr($Resu[1], 5,2)."/".substr($Resu[1], 0,4);
           echo '
            <tr>
                 <td align="right">'.($i+1).'</td>
              	 <td align="left">'.$Resu[3].'</td>
              	 <td align="right">'.$Resu[0].'</td>
              	 <td align="right">'.$Resu[4].'</td>
              	 <td align="center">'.htmlentities($Resu[13]).'</td>
              	 <td align="right">'.$FormatFecha.'</td>
              	 <td>'.htmlentities($Resu[14]).'</td>
                 <td>'.$Resu[11].'</td>
                 <td align="center">
              				<a href="Querys.php?guard=E&Reserva='.$Resu[0].'">
                        <img src="images/drop.png" width="22px" height="22px" title="Cancelar Reserva">
                      </a>
              	 </td>
                 <td align="center">';
                  echo '<a href="reserva.php?fecha='.$FormatFecha.'&Tipo=U&Rnum='.$Resu[0].'&MA='.$Resu[11].'" rel="gb_page_center[600, 460]" title="Modificar Reserva ('.$FormatFecha.')">
                       <img src="images/edit.png" width="22px" height="22px" title="Modificar Reserva">
                      </a>
              	 </td>
            </tr>';
          }
        echo '
	        </table>

		 	';
		} 
	break;
	case 'ResForm':
	$fecha = $_POST['Fecha'];
	// perfil e identidad salen de la sesion, no del formulario
	$Perfil = isset($_SESSION['Perfil']) ? $_SESSION['Perfil'] : 0;
	$IdLiving = esAdmin() ? (int)$_POST['IdLiving'] : idLivingSesion();
	$Tipo = $_POST['tipo'];
	$NumPer = 10;
	$RecibeCorreoDefault = "";
	$diasem = date("w",mktime(12,0,0,(substr($fecha, 3,2)),(substr($fecha, 0,2)),(substr($fecha, 6,4))));
	$imagen="";
	$sql = "SELECT * 
	          FROM tblclientesvip vp 
			 WHERE vp.NUMIDENTLIV = ".$IdLiving." ORDER BY 2 ASC";
	$queryVIP = $conexion->query($sql);
	$numCliVIP = $queryVIP->num_rows;
	if($Tipo=="I"){
    	$NombreBoton="Realizar Reserva";
    	$TituloForm="Registro de Reservas";
    	$NumReserva=0;
    	$NombreReserva="";//$NReserva;
    	$TipoCele=5;
    	$RecibeCorreoDefault=" checked";
	}else{
   		$NombreBoton="Actualizar Reserva";
    	$TituloForm="Actualizaci&oacuten de Reservas";
    	$NumReserva=(int)$_POST['Rnum'];
    	if(!reservaPermitida($conexion, $NumReserva)){
    		echo '<p class="perror">No tiene permisos sobre esta reserva</p>';
    		break;
    	}
    	$sql = "SELECT * 
		          FROM tblreservas 
				 WHERE NUMRESERVA = ".$NumReserva."";
		$query = $conexion->query($sql);
		$Result = $query->fetch_array();
		$NombreReserva = $Result[3];
    	$NumPer = $Result[4];
    	$TipoCele = $Result[5];
    	$NomH = $Result[6];
    	$RObservac = $Result[7];
    	$Tipo = "U";
    	$RecibeCorreoDefault = " checked";
	}
	$sql = "SELECT * 
	          FROM tbltipocelebra 
		  ORDER BY 2 ASC";
	$queryTipCel = $conexion->query($sql);
	$numcelebra = $queryTipCel->num_rows;	 
	switch ($diasem) {
		case 0:
			$imagen="images/Domingo.jpg";
		break;
		case 1:
			$imagen="images/pato.jpg";
		break;
		case 2:
			$imagen="images/pato.jpg";
		break;
		
		case 3:
			$imagen="images/Miercoles.jpg";
		break;
		case 4:
			$imagen="images/Jueves.jpg";
		break;
		case 5:
			$imagen="images/Viernes.jpg";
		break;
		case 6:
			$imagen="images/Sabado.jpg";
		break;
		default:
			# code...
		break;
	}
	if(($diasem==0)||($diasem>=1&&$diasem<=6)){
		if(compararFechas($fecha,date('d/m/Y'))<0){
			$Mensaje='<div style="text-align:center;"><br>
			<p class="perror">La Fecha '.$fecha.' es Inferior a la Actual</p><br>
			<img class="imgerror" src="images/duck.jpg">
		</div>';
		}else{
			$Fecha1=texto(substr($fecha, 6,4).'-'.substr($fecha, 3,2)."-".substr($fecha, 0,2));
			$sql = "SELECT * 
					  FROM tbleventos e 
					 WHERE e.Fecha='".$Fecha1."'";
			$query = $conexion->query($sql);
			$n = $query->num_rows;
			$p="A";
			if($n>0){
				$resim = $query->fetch_array();
				if($resim[7]=="S"){
					$p="A";
					$imagen='images/Eventos/'.$resim[4];
				}else{
					$p="B";
				}
			}else{
				$p="A";
			}
			if($p=="A"){
				if($diasem==0){
					$sql = "SELECT * 
					          FROM tblfestivos e 
					         WHERE e.Fecha='".$Fecha1."'";
					$query = $conexion->query($sql);
					$n=$query->num_rows;
					if($n>0){
						$Mensaje='
				<div class="container">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align:center;">
							<img class="resdia" src="'.$imagen.'" >
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<h4><strong>Formulario para Crear Reservas</strong></h4><br>
							<form id="reserva" class="form-horizontal mitad" >
   								<fieldset class="fres">
   									 <div class="form-group">
										<label class="col-lg-5 col-sm-6 col-md-5 control-label">
											Reserva a Nombre de :
										</label>
										<div class="col-lg-6 col-sm-5 col-md-6">
						 					<input type="text" class="form-control" id="rname" value="'.$NombreReserva.'" required>
										</div>
						 		     </div>';
                     if($Perfil==1){
                     	$RecibeCorreoDefault="";
                     	$Mensaje.='
                               	<div class="form-group">
										<label class="col-lg-5 col-sm-6 col-md-5 control-label">
											Es cliente VIP? :
										</label>
										<div class="col-lg-6 col-sm-5 col-md-6">
						 					<select id="clientesVIP" class="form-control" onchange="ClienteVIP()">
                               					<option value="0">NO</option>';
                                    			for($i=0;$i<$numCliVIP;$i++){
													$result = $queryVIP->fetch_array();
                                      				$Mensaje.='<option value="'.$result[0].'">'.htmlentities($result[1]).'</option>';
					                                            }
					                                       $Mensaje.= '
					                            </select>'; 
				                      }else{

				                      }
                               	$Mensaje.='</div>
						 		    </div>
						 		    <div class="form-group">
						 				<label class="col-lg-5 col-sm-6 col-md-5 control-label">
						 					N&uacute;mero de Personas :
						 				</label>
						 				<div class="col-lg-6 col-sm-5 col-md-6">
						 				<select id="cper" class="form-control">';
						 				for($i=2;$i<=50;$i++){
						 					$sel1="";
						 					if($i==10){
						 						$sel1=" selected";
						 					}else{
						 						$sel1=" ";
						 					}
						 					$Mensaje.='<option value="'.$i.'" '.$sel1.'>'.$i.'</option>';
						 				}
						 		$Mensaje.='</select>
						 				</div>
						 		    </div>
						 		    <div class="form-group">
						 				<label class="col-lg-5 col-sm-6 col-md-5 control-label">
						 					Tipo de Celebraci&oacute;n : 
						 				</label>
						 				<div class="col-lg-6 col-sm-5 col-md-6">
						 					<select id="rtipocelabra" class="form-control" onchange="PasaNombre()">';
    								for($i=0;$i<$numcelebra;$i++){
										$result = $queryTipCel->fetch_array();
    									$TC="";
                                        if($result[0]==$TipoCele){
                                            $TC=" selected";
                                        }else{
                                            $TC="";
                                        }        
    									$Mensaje.= '<option value='.$result[0].' '.$TC.'>'.htmlentities($result[1]).'</option>';
    								}
    								$Mensaje.= '
    										</select>	
						 				</div>
						 		    </div>
						 		    <div class="form-group">
						 				<label class="col-lg-5 col-sm-6 col-md-5 control-label">
						 					Nombre del(la) Homenajead@ : 
						 				</label>
						 				<div class="col-lg-6 col-sm-5 col-md-6">
						 					<input type="text" id="rhomena" size="20" value="" class="form-control" disabled>
						 					<span id="helpBlock" class="help-block" style="text-align:justify;">Si tienes m&aacute;s de un Homenajeado separalos con el s&iacute;mbolo coma(,).</span>
						 				</div>
						 		   </div>
    							   <div class="form-group">
 									 <label class="col-lg-5 col-sm-6 col-md-5 control-label">
					 					Observaciones : 
					 				</label>
					 				<div class="col-lg-6 col-sm-5 col-md-6">
					 					<textarea class="form-control" style="text-align:justify;" cols="40" rows="5" id="robserva"  placeholder="Especifica alguna Observaci&oacute;n que debamos conocer acerca de tu reserva"></textarea>
					 				</div>
					 		   	   </div>
					 		   	   <div class="form-group">
 									  <div class="col-lg-9">
 									  <input type="checkbox" id="news" name="news" '.$RecibeCorreoDefault.'> <strong>Desea recibir la Confirmaci&oacute;n Via E-Mail?</strong>
 									  </div>
 								   </div>
 								   <div class="form-group">
 								   	<div class="col-lg-9 col-lg-offset-5">
 									 <button class="btn btn-primary" type="button" id="registro" onclick="Reservar()">'.$NombreBoton.'</button>
 									 </div>
					 		   	   </div>   
						 		</fieldset>
						 	</form>     
						</div>
					</div>
				</div>';		
					}else{
						$Mensaje='
						<div class="container">
							<div class="row">
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align:center;">
									<h4>Lo sentimos NO prestamos servicio</h4>
									<img class="imgerror" src="images/duck.jpg">
								</div>
							</div>
							</div>';
					} 
				}else{
					$Mensaje='
				<div class="container">
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align:center;">
							<img class="resdia" src="'.$imagen.'" >
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<h4><strong>Formulario para Crear Reservas</strong></h4><br>
							<form id="reserva" class="form-horizontal mitad" >
   								<fieldset class="fres">
   									 <div class="form-group">
										<label class="col-lg-5 col-sm-6 col-md-5 control-label">
											Reserva a Nombre de :
										</label>
										<div class="col-lg-6 col-sm-5 col-md-6">
						 					<input type="text" class="form-control" id="rname" value="'.$NombreReserva.'" required>
										</div>
						 		     </div>
    				 		';
                     if($Perfil==1){
                     	$RecibeCorreoDefault="";
                               	$Mensaje.='
                               	<div class="form-group">
										<label class="col-lg-5 col-sm-6 col-md-5 control-label">
											Es cliente VIP? :
										</label>
										<div class="col-lg-6 col-sm-5 col-md-6">
						 					<select id="clientesVIP" class="form-control" onchange="ClienteVIP()">
                               					<option value="0">NO</option>';
                                    			for($i=0;$i<$numCliVIP;$i++){
													$result = $queryVIP->fetch_array();
					                           		$Mensaje.='
					                                		<option value="'.$result[0].'">'.htmlentities($result[1]).'</option>';
					                                            }
					                                $Mensaje.= '
					                            			</select>';     
					                                    }else{

					                                    };
				  				$Mensaje.='
										</div>
						 		    </div>
                            		<div class="form-group">
						 				<label class="col-lg-5 col-sm-6 col-md-5 control-label">
						 					N&uacute;mero de Personas :
						 				</label>
						 				<div class="col-lg-6 col-sm-5 col-md-6">
						 				<select id="cper" class="form-control">';
						 				for($i=2;$i<=50;$i++){
						 					$sel1="";
						 					if($i==10){
						 						$sel1=" selected";
						 					}else{
						 						$sel1=" ";
						 					}
						 					$Mensaje.='<option value="'.$i.'" '.$sel1.'>'.$i.'</option>';
						 				}
						 		$Mensaje.='</select>
						 				</div>
						 		    </div> 
    								<div class="form-group">
						 				<label class="col-lg-5 col-sm-6 col-md-5 control-label">
						 					Tipo de Celebraci&oacute;n : 
						 				</label>
						 				<div class="col-lg-6 col-sm-5 col-md-6">
						 					<select id="rtipocelabra" class="form-control" onchange="PasaNombre()">';
    								for($i=0;$i<$numcelebra;$i++){
										$result = $queryTipCel->fetch_array();
    									$TC="";
                                        if($result[0]==$TipoCele){
                                            $TC=" selected";
                                        }else{
                                            $TC="";
                                        }        
    									$Mensaje.= '<option value='.$result[0].' '.$TC.'>'.htmlentities($result[1]).'</option>';
    								}
    								$Mensaje.= '
    										</select>	
						 				</div>
						 		    </div>
						 		    <div class="form-group">
						 				<label class="col-lg-5 col-sm-6 col-md-5 control-label">
						 					Nombre del(la) Homenajead@ : 
						 				</label>
						 				<div class="col-lg-6 col-sm-5 col-md-6">
						 					<input type="text" id="rhomena" size="20" value="" class="form-control" disabled>
						 					<span id="helpBlock" class="help-block" style="text-align:justify;">Si tienes m&aacute;s de un Homenajeado separalos con el s&iacute;mbolo coma(,).</span>
						 				</div>
						 		   </div>
    							   <div class="form-group">
 									 <label class="col-lg-5 col-sm-6 col-md-5 control-label">
					 					Observaciones : 
					 				</label>
					 				<div class="col-lg-6 col-sm-5 col-md-6">
					 					<textarea class="form-control" style="text-align:justify;" cols="40" rows="5" id="robserva" placeholder="Especifica alguna Observaci&oacute;n que debamos conocer acerca de tu reserva"></textarea>
					 				</div>
					 		   	   </div>
					 		   	   <div class="form-group">
 									  <div class="col-lg-9">
 									  <input type="checkbox" id="news" name="news" '.$RecibeCorreoDefault.'> <strong>Desea recibir la Confirmaci&oacute;n Via E-Mail?</strong>
 									  </div>
 								   </div>
 								   <div class="form-group">
 								   	<div class="col-lg-9 col-lg-offset-5">
 									 <button class="btn btn-primary" type="button" id="registro" onclick="Reservar()">'.$NombreBoton.'</button>
 									 </div>
					 		   	   </div> 
       					</fieldset>	
   				  </form>
						</div>
					</div>
				</div>';
				}
				
			}else{
				$Mensaje='
				<div class="container" style="text-align:center;padding-bottom:1em;">
					<h4><strong>'.$resim[4].'</strong></h4><br>
					<div class="row">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align:center;">
							<img class="resdia" src="'.("images/Eventos/".$resim[3]).'" >
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
							<img class="resdia" src="'.("images/Eventos/".$resim[5]).'" >
						</div>

					</div>	
				</div>';			
			}
			
			
		}
	
	}else{
		$Mensaje='<div class="container">
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:center;">
									<h4>Lo sentimos NO prestamos servicio</h4>
									<img class="resdia" src="images/duck.jpg">
								</div>
							</div>
							</div>';
	}
	echo $Mensaje;
	break;
	case 'ConfEntrM':
		$confi = texto($_POST['cumpli']);
		$Reserva = entero($_POST['RNum']);
		$Query="update tblreservas r 
				   SET r.STRCUMPLERESERVA='".$confi."' 
				 WHERE r.NUMRESERVA=".$Reserva."";
		$Consulta = $conexion->query($Query)
				or die(errorBD());
		$Mensaje="";
		if($Consulta == 1){
			$Mensaje='
				<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> Reserva Actualizada Satisfactoriamente! 
				</div>
			</div>
		</div>';
		}else{
			$Mensaje='
				<div class="row">
			<div class="col-md-12">
				<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> Ha Ocurrido un Error
				</div>
			</div>
		</div>';
		}	
	
	echo $Mensaje;
	break;
	case 'TMCD';
		$fecha = texto($_POST['fecha']);
		$sql = "SELECT DISTINCT  M.STRMESA,
								CASE 
									WHEN LENGTH(M.STRTIPO)>1 THEN '#FF0000'
									ELSE A.STRCOLOR
								END 
                  FROM tblmesareserva M, 
                  	     tbltipoasigna A 
                 WHERE M.DTMFECHARESERVA = '" .$fecha."'
                   AND A.STRTIPOASIGNA = substring(M.STRTIPO,1,1)
                   AND M.STRESTADORESERVA in(1,3)
               	ORDER BY M.STRMESA ASC";
		$query  = $conexion->query($sql);
		$nfilas = $query->num_rows;
		$Cadena = $nfilas."|0;";
		for($i=0;$i<$nfilas;$i++){
			$res = $query->fetch_array();
			if(substr($res[0],0,1)=='B'){
			    if(strlen($res[0])==2){
			        $res[0]=substr($res[0],0,1)."0".substr($res[0],1,1);
			    }
			}
			$Cadena.=$res[0]."|".$res[1].";";
		}
		echo $Cadena;
	break;
	case 'BMOD':
		$fecha = texto($_POST['fecha']);
		$sql = "SELECT A.STRMESA 
				    FROM tblmesas A LEFT JOIN(
										SELECT DISTINCT
											   M.STRMESA 
										  FROM tblmesareserva M
										 WHERE M.DTMFECHARESERVA = '".$fecha."' 
										   AND M.STRESTADORESERVA = 1
									  ORDER BY M.STRMESA ASC
                                    ) P ON P.STRMESA= A.STRMESA
				   WHERE P.STRMESA IS NOT NULL";
		$query = $conexion->query($sql);
		$nfilas = $query->num_rows;
		$Cadena = $nfilas.";";
		for($i=0;$i<$nfilas;$i++){
			$res = $query->fetch_array();
			$Cadena.=$res[0].";";
		}
		echo $Cadena;
	break;
	case 'STRE':
		$fecha = texto($_POST['fecha']);
		$tipo = entero($_POST['tipo']);
		$Cadena="";
		switch ($tipo) {
			case 1:
				$sql = "SELECT A.STRMESA 
						FROM tblmesas A LEFT JOIN(
											SELECT DISTINCT
												M.STRMESA 
											FROM tblmesareserva M
											WHERE M.DTMFECHARESERVA = '".$fecha."' 
											AND M.STRESTADORESERVA = 1
											AND substring(M.STRTIPO,1,1) ='E'
										ORDER BY M.STRMESA ASC
										) P ON P.STRMESA= A.STRMESA
					WHERE P.STRMESA IS NULL";
			break;
			case 2:
				$sql = "SELECT A.STRMESA 
				    FROM tblmesas A LEFT JOIN(
										SELECT DISTINCT
											   M.STRMESA 
										  FROM tblmesareserva M
										 WHERE M.DTMFECHARESERVA = '".$fecha."' 
										   AND M.STRESTADORESERVA = 1
										   AND substring(M.STRTIPO,1,1) ='C'
									  ORDER BY M.STRMESA ASC
                                    ) P ON P.STRMESA= A.STRMESA
				   WHERE P.STRMESA IS NULL";
			break;
			case 3:
				$sql = "SELECT A.STRMESA 
						    FROM tblmesas A LEFT JOIN(
												SELECT DISTINCT
													   M.STRMESA 
												  FROM tblmesareserva M
												 WHERE M.DTMFECHARESERVA = '".$fecha."' 
												   AND M.STRESTADORESERVA = 1
												   AND substring(M.STRTIPO,1,1) in('N','E')
											  ORDER BY M.STRMESA ASC
		                                    ) P ON P.STRMESA= A.STRMESA
						   WHERE P.STRMESA IS NULL";
			break;
			case 4:
				$sql = "SELECT A.STRMESA 
						    FROM tblmesas A LEFT JOIN(
												SELECT DISTINCT
													   M.STRMESA 
												  FROM tblmesareserva M
												 WHERE M.DTMFECHARESERVA = '".$fecha."' 
												   AND M.STRESTADORESERVA = 1
												   AND substring(M.STRTIPO,1,1) in('K')
											  ORDER BY M.STRMESA ASC
		                                    ) P ON P.STRMESA= A.STRMESA
						   WHERE P.STRMESA IS NULL";
			break;
			case 5:
				$sql = "SELECT A.STRMESA 
						    FROM tblmesas A LEFT JOIN(
												SELECT DISTINCT
													   M.STRMESA 
												  FROM tblmesareserva M
												 WHERE M.DTMFECHARESERVA = '".$fecha."' 
												   AND M.STRESTADORESERVA = 1
												   AND substring(M.STRTIPO,2,1) in('I')
											  ORDER BY M.STRMESA ASC
		                                    ) P ON P.STRMESA= A.STRMESA
						   WHERE P.STRMESA IS NULL";
			break;
			case 6:
				$sql = "SELECT A.STRMESA 
				    FROM tblmesas A LEFT JOIN(
										SELECT DISTINCT
											   M.STRMESA 
										  FROM tblmesareserva M,
										  	   tblreservas r
										 WHERE M.DTMFECHARESERVA = '".$fecha."'
										   AND r.NUMRESERVA=M.NUMRESERVA 
										   AND M.STRESTADORESERVA = 1
										   AND r.NUMMOTIVO=1
									  ORDER BY M.STRMESA ASC
                                    ) P ON P.STRMESA= A.STRMESA
				   WHERE P.STRMESA IS NULL";
			break;
			
			default:
				# code...
			break;
		}
		$query  = $conexion->query($sql);
		$nfilas = $query->num_rows;
		$Cadena = $nfilas.";";
		for($i=0;$i<$nfilas;$i++){
			$res = $query->fetch_array();
			$Cadena.=$res[0].";";
		}
		echo $Cadena;
	break;
	case "INMM":
		$Mesa=texto($_POST['mesa']);
		$Reserva=entero($_POST['reserva']);
		$TipoA=$_POST['tipof'];
		$TipoN=$TipoA;
		if(strlen($TipoA)>1){
			$TipoN=substr($TipoA,0,1);
		}else{
			$TipoN=$TipoA."I";
		}
		$sql="UPDATE tblmesareserva m 
			       SET m.STRTIPO='".texto($TipoN)."'
			     WHERE m.NUMRESERVA=".$Reserva;
	    $query  = $conexion->query($sql);
		echo $query;
	break;
	case "ARMN":
		$mesa=texto($_POST['mesa']);
		$Reserva=entero($_POST['reserva']);
		$sql = "SELECT r.DTMFECHARESERVA,
		               r.NUMMOTIVO
		          FROM tblreservas r  
				WHERE NUMRESERVA = ".$Reserva;
		$query = $conexion->query($sql);
		$res = $query->fetch_array();		
		$fechaReserva = $res[0];
		$nummotivo = $res[1];
		$tipo = 'C';
		if($nummotivo ==1){
		    $tipo = 'T';
		}
		$FechaReporta = date("Y-m-d H:i:s");
		$Mensaje = "";
		$sql = "SELECT * 
		          FROM tblmesareserva mr 
				 WHERE mr.STRMESA = '".$mesa."'
				   AND mr.DTMFECHARESERVA= '".$fechaReserva."'
				   AND mr.STRESTADORESERVA='1'";
		$query = $conexion->query($sql);
		$nfilas = $query->num_rows;
		if($nfilas>0){
			$Mensaje = "La Mesa (".$mesa.") ya se encuentra asignada";
		}else{
			$sql = "INSERT INTO tblmesareserva VALUES(NULL,'".$fechaReserva.
												     "',".$Reserva.
													 ",'".$mesa.
													"','".$FechaReporta.
													"','".$tipo."','1')";
			$query = $conexion->query($sql);
					
			$sql = "UPDATE tblreservas r 
					   SET r.NUMESTADO = 3, 
						   r.DTMFECHAESTADO = '".$FechaReporta."', 
						   r.STRMESAASIGNA = '".$mesa."'
					WHERE r.NUMRESERVA = ".$Reserva;
			$queryReserva  = $conexion->query($sql);
			$Mensaje = "Reserva (".$Reserva.") Asignada satisfactoriamente en la mesa ".$mesa;
		}
		echo $Mensaje;
	break;
	default:
		# code...e
	break;
}

?>

		