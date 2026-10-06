<?php
require_once("auth.php");
exigirAdminPagina();
require_once("conect.php");

$self = $_SERVER['PHP_SELF'];
$fecha=fechaSegura($_GET['fecha']);
if(substr($fecha,2,1)=='/'){
$fecha=substr($fecha,6,4).'-'.substr($fecha,3,2).'-'.substr($fecha,0,2);
}else{
	
}
$orden=entero($_GET['orden']);
header("refresh:70;url=reservoir.php?fecha=".$fecha."&orden=".$orden);

$_SESSION["autenticado"]="Y";
$IdLiving=$_SESSION['IdLiving'];
require_once("conect.php");
global $conexion;
$instruccion = "SELECT m.*,a.strcolor 
                  FROM tblmesareserva m, tbltipoasigna a 
                 WHERE m.dtmfechareserva = '" .$fecha."'
                   AND a.strtipoasigna = m.strtipo
                   AND m.strestadoreserva = 1";
$consulta = $conexion->query($instruccion)
    or die(errorBD());
$nfilas = $consulta->num_rows;

$instruccion1 = "SELECT a.* 
                  FROM tbltipoasigna a
                 WHERE a.STRTIPOASIGNA IN('A','E','C','F')";
$consulta1 = $conexion->query($instruccion1)
    or die(errorBD());
$nfilas1 = $consulta1->num_rows;

$QueryRes="SELECT COUNT(*)
			 FROM tblreservas R,
    			  tblmesareserva mr
			WHERE R.DTMFECHARESERVA = '".$fecha."'
        	  AND R.NUMESTADO = 1
        	  AND R.STRMESAASIGNA <>''
        	  AND R.NUMRESERVA=mr.NUMRESERVA
        	  AND mr.STRESTADORESERVA=1";
$consultaRes = $conexion->query($QueryRes)
    or die(errorBD());
$resulres=$consultaRes->fetch_array();    
$Mesas[0]="N";
$Palcos[0]="N";
$Barras[0]="N";
$Aux[0]="N";

for($i=10;$i<97;$i++){
	$Mesas[$i]="#FFFFFF";
}
for($i=1;$i<21;$i++){
	$Barras[$i]="#FFFFFF";
}
for($i=1;$i<8;$i++){
	$Palcos[$i]="#FFFFFF";
}
for($i=1;$i<3;$i++){
	$Aux[$i]="#FFFFFF";
}
$BarrasOcupadas=0;
$MesasOcupadas=0;
$PalcosOcupados=0;
$AuxiliaresOcupadas=0;
for($i=0;$i<$nfilas;$i++){
	$resultado = $consulta->fetch_array();
	$ident=substr($resultado[3], 0,1);
	$estado=$resultado[5];
	switch ($ident) {
		case 'A':
			$number=substr($resultado[3], 1,strlen($resultado[3]));
			$Aux[$number]=$resultado[7];
			$AuxiliaresOcupadas++;
		break;
		case 'B':
			$number=substr($resultado[3], 1,strlen($resultado[3]));
			$Barras[$number]=$resultado[7];
			$BarrasOcupadas++;
		break;
		case 'P':

			$number = substr($resultado[3], 1,strlen($resultado[3]));
			$Palcos[$number] = $resultado[7];
			$PalcosOcupados++;
		break;
		
		default:
			$number=$resultado[3];
			$Mesas[$number]=$resultado[7];
			$MesasOcupadas++;
		break;
	}
	
	}
echo '  
<html>
<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>Living Night Club</title>
		<meta name="description" content="" />
		<meta name="author" content="Santiago Penilla" />
		<link rel="shortcut icon" href="imagenes/favicon.ico">
		<link rel="stylesheet" type="text/css" href="css/estilo_salon.css">
</head>
<body link="#000000" alink="#000000" vlink="#000000" bgcolor="#0000FF">
<table width="100%" >
<tr>
<td colspan=3 >
<div class="tabla">
	<div class="fila">
		<div class="columna">
			<b><h1>'.$fecha.'</h1></b>
		</div>
	</div>
</div>

</td>
</tr>
<tr>
<td colspan=3 >
<div class="tabla">
	<div class="fila">
		<div class="columna">
			<a style="font-size:12pt;" href="GestionSalon.php"><div><< Atras</div>
			</a>
		</div>
		<div class="columna">
			<a style="font-size:12pt;" href="BuscarCliente.php?fecha='.$fecha.'"><div>Buscar Reserva</div>
			</a>
		</div>
		<div class="columna">
			<a style="font-size:12pt;" href="ListaCumple.php?fecha='.$fecha.'"><div>Listar Cumplea&ntilde;os</div>
			</a>
		</div>
	</div>
</div>

</td>
</tr>

<tr>
<td colspan=3 >
<div class="tabla">
	<div class="fila">';
	 for($i=0;$i<$nfilas1;$i++){
		$res1=$consulta1->fetch_array();
	 	echo '<div class="columna" style="background-color:'.$res1[2].';">
	 		<a style="font-size:11pt;" href="Listados.php?fecha='.$fecha.'&tipo='.$res1[0].'"><div>'.$res1[1].'</div></a>
	 	</div>';
	 }
	echo '
		<div class="columna" style="background-color:#141C22;">
			<a style="font-size:11pt;color:white;" href="Listados.php?fecha='.$fecha.'&tipo=K"><div>Mesas Levantadas</div></a>
		</div>
		<div class="columna" style="background-color:#FF1C22;">
		 <a style="font-size:11pt;color:white;" href="Listados.php?fecha='.$fecha.'&tipo=Cu"><div>Cumplea&ntilde;os</div></a>
		</div>
	</div>
</div>

</td>
</tr>
<tr>
<td colspan=3 >
<div class="tabla">
	<div class="fila">
		<div class="columna">
			<b>Reservas Activas: </b>'.($resulres[0]).'
		</div>
		<div class="columna">
			<b>Auxiliares Disponibles: </b>'.(2-$AuxiliaresOcupadas).'
		</div>
		<div class="columna">
			<b>Barras Disponibles: </b>'.(20-$BarrasOcupadas).'
		</div>
		<div class="columna">
			<b>Mesas Disponibles: </b>'.(86-$MesasOcupadas).'
		</div>
		<div class="columna">
			<b>Palcos Disponibles: </b>'.(7-$PalcosOcupados).'
		</div>
	</div>
</div>

</td>
</tr>
<tr>
<td colspan=3 >
<div class="tabla">
	<div class="fila" style="background-color:black;color:white;">
		<div class="columna" style="width:18%;">
			BA&Ntilde;O DAMAS
		</div>
		<div class="columna">
			TARIMA
		</div>
		<div class="columna" style="width:18%;">
			BA&Ntilde;O CABALLEROS
		</div>
	</div>
</div>

</td>
</tr>
<tr>
	<td width="7%" >
		<table width="60%" height="982px">
			<tr>
				<td class="izq6" bgcolor="'.$Mesas[10].'">
					<a href="GestionMesa.php?mesa=10&fecha='.$fecha.'">
					10
					</a>		
				</td>
				
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[11].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 11"  href="GestionMesa.php?mesa=11&fecha='.$fecha.'">
							11
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[12].'">
				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 12"  href="GestionMesa.php?mesa=12&fecha='.$fecha.'">12
				</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[13].'">
				 <a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 13"  href="GestionMesa.php?mesa=13&fecha='.$fecha.'">13
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[14].'">
				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 14"  href="GestionMesa.php?mesa=14&fecha='.$fecha.'">14
				</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[15].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 15"  href="GestionMesa.php?mesa=15&fecha='.$fecha.'">15
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[16].'">
				   <a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 16"  href="GestionMesa.php?mesa=16&fecha='.$fecha.'">16
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[17].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 17"  href="GestionMesa.php?mesa=17&fecha='.$fecha.'">17
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[18].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 18"  href="GestionMesa.php?mesa=18&fecha='.$fecha.'">18
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[19].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 19"  href="GestionMesa.php?mesa=19&fecha='.$fecha.'">19
					</a>
				</td>
			</tr>
			<tr>
				<td class="izq10" bgcolor="'.$Mesas[20].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 20"  href="GestionMesa.php?mesa=20&fecha='.$fecha.'">20
					</a>
				</td>
			</tr>
		</table>
	</td>
	<td width="86%" >
		<table width="100%" height="700px" >
			<tr>
				<td>
					<table width="100%" height="350px" cellspacing="10" border=0>
						<tr>
							<td width="10%">
								<table  width="30%" cellspacing="10" border=0>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[96].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 96"  href="GestionMesa.php?mesa=96&fecha='.$fecha.'">96
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[95].'">
												<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 95"  href="GestionMesa.php?mesa=95&fecha='.$fecha.'">95
												</a>
										</td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[89].'">
												<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 89"  href="GestionMesa.php?mesa=89&fecha='.$fecha.'">89
												</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[90].'">
												<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 90"  href="GestionMesa.php?mesa=90&fecha='.$fecha.'">90
												</a>
										</td>
									</tr>
									<tr>
										<td colspan=2 class="cenderP"> </td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[88].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 88"  href="GestionMesa.php?mesa=88&fecha='.$fecha.'">88
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[87].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 87"  href="GestionMesa.php?mesa=87&fecha='.$fecha.'">87
											</a>
										</td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[81].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 81"  href="GestionMesa.php?mesa=81&fecha='.$fecha.'">81
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[82].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 82"  href="GestionMesa.php?mesa=82&fecha='.$fecha.'">82
											</a>
										</td>
									</tr>
									<tr>
										<td colspan=2 class="cenderP"> </td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[80].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 80"  href="GestionMesa.php?mesa=80&fecha='.$fecha.'">80
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[79].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 79"  href="GestionMesa.php?mesa=79&fecha='.$fecha.'">79
											</a>
										</td>
									</tr>
									<tr>
										<td class="cender6" bgcolor="'.$Mesas[72].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 72"  href="GestionMesa.php?mesa=72&fecha='.$fecha.'">72
											</a>
										</td>
										<td></td>
									</tr>
								</table>
							</td>
							<td width="80%" valign="bottom">
							   <div style="height:570px;overflow-y:scroll;border:1px solid black;">
							   	<table width="100%" class="tablaconsulta">
							   <tr>
							   		<td colspan=2><a href="reservoir.php?fecha='.$fecha.'&orden=2">Ordenar por Nombre de Reserva</a></td>
							   		<td colspan=2><a href="reservoir.php?fecha='.$fecha.'&orden=1">Ordenar por Mesa</a></td>
							   </tr>
							   	<tr>
							   		<th id="tittabla">No. Mesa</th>
							   		<th id="tittabla">Nombre</th>
							   		<th id="tittabla">Nombre Homenajeado</th>
							   		<th id="tittabla">Observaciones</th>
							   	</tr>';
							   		$Query = "SELECT distinct(m.STRMESA), 
							   						 r.STRNOMBRERESERVA,
							   						 r.STRNOMBREHOMENAJE,
							   						 r.STROBSERVACIONES
             								    FROM tblmesareserva m, 
             								         tbltipoasigna a, 
             								         tblreservas r 
                 							   WHERE m.dtmfechareserva = '" .$fecha."'
                   									   AND a.strtipoasigna = m.strtipo
                   									   AND m.strestadoreserva = 1
                   									   AND r.numestado=1
                   									   AND r.NUMRESERVA=m.NUMRESERVA
                   									   AND m.STRTIPO not in('B','K')
                   									   AND r.STRNOMBRERESERVA NOT IN('MESA BLOQUEADA','Mesa Compartida')
              										   AND r.STRNOMBRERESERVA NOT LIKE '%Grupo%'
                   							ORDER BY ".$orden." asc";
									$consulta2 = $conexion->query($Query)
    												or die(errorBD());
									$nfilas2 = $consulta2->num_rows;
							   		for($i=0;$i<$nfilas2;$i++){
										$resultado = $consulta2->fetch_array();
										echo '<tr>
												<td>'.$resultado[0].'</td>
												<td>'.$resultado[1].'</td>
												<td>'.$resultado[2].'</td>
												<td>'.$resultado[3].'</td>
											  </tr>
											';
									}
							   		echo '
							   </table>
							   !!! --- FIN DEL LISTADO --- !!!
							   </div>
								<table border=0 width="100%" cellspacing="10" height="30px">
									<tr>
										<td class="centroT" bgcolor="'.$Mesas[73].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 73"  href="GestionMesa.php?mesa=73&fecha='.$fecha.'">73
											</a>
										</td>
										<td class="centroP"></td>
										<td class="centroT" bgcolor="'.$Mesas[74].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 74"  href="GestionMesa.php?mesa=74&fecha='.$fecha.'">74
											</a>
										</td>
										<td class="centroP"></td>
										<td class="centroT" bgcolor="'.$Mesas[75].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 75"  href="GestionMesa.php?mesa=75&fecha='.$fecha.'">75
											</a>
										</td>
									</tr>
								</table>
							</td>
							<td width="20%" align="right">
								<table  width="30%" cellspacing="10">
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[94].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 94"  href="GestionMesa.php?mesa=94&fecha='.$fecha.'">94
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[93].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 93"  href="GestionMesa.php?mesa=93&fecha='.$fecha.'">93
											</a>
										</td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[91].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 91"  href="GestionMesa.php?mesa=91&fecha='.$fecha.'">91
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[92].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 92"  href="GestionMesa.php?mesa=92&fecha='.$fecha.'">92
											</a>
										</td>
									</tr>
									<tr>
										<td colspan=2 class="cenderP"> </td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[86].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 86"  href="GestionMesa.php?mesa=86&fecha='.$fecha.'">86
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[85].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 85"  href="GestionMesa.php?mesa=85&fecha='.$fecha.'">85
											</a>
										</td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[83].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 83"  href="GestionMesa.php?mesa=83&fecha='.$fecha.'">83
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[84].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 84"  href="GestionMesa.php?mesa=84&fecha='.$fecha.'">84
											</a>
										</td>
									</tr>
									<tr>
										<td colspan=2 class="cenderP"> </td>
									</tr>
									<tr>
										<td class="cender10" bgcolor="'.$Mesas[78].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 78"  href="GestionMesa.php?mesa=78&fecha='.$fecha.'">78
											</a>
										</td>
										<td class="cender10" bgcolor="'.$Mesas[77].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 77"  href="GestionMesa.php?mesa=77&fecha='.$fecha.'">77
											</a>
										</td>
										
									</tr>
									<tr>
										<td></td>
										<td class="cender6" bgcolor="'.$Mesas[76].'">
											<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 76"  href="GestionMesa.php?mesa=76&fecha='.$fecha.'">76
											</a>
										</td>
										
									</tr>
								</table>
							</td>
						</tr>
					<tr>
				<td colspan=3 valign="bottom">
					<table width="100%" border=0>
						<tr>
						 <td width="35%" valign="bottom">
						 	<table width="100%" border=0 height="300px" cellspacing="10" border-spacing="10px">
						 		<tr>
						 			<td class="bajoder10" bgcolor="'.$Mesas[71].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 71"  href="GestionMesa.php?mesa=71&fecha='.$fecha.'">71
										</a>
									</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[70].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 70"  href="GestionMesa.php?mesa=70&fecha='.$fecha.'">70
										</a>
						 			</td>
						 		</tr>
						 		<tr>	
						 			<td class="bajoder10" bgcolor="'.$Mesas[60].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 60"  href="GestionMesa.php?mesa=60&fecha='.$fecha.'">60
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[61].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 61"  href="GestionMesa.php?mesa=61&fecha='.$fecha.'">61
										</a>
						 			</td>
						 		</tr>	
						 			<td class="bajoder10" bgcolor="'.$Mesas[59].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 59"  href="GestionMesa.php?mesa=59&fecha='.$fecha.'">59
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[58].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 58"  href="GestionMesa.php?mesa=58&fecha='.$fecha.'">58
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoder10" bgcolor="'.$Mesas[48].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 48"  href="GestionMesa.php?mesa=48&fecha='.$fecha.'">48
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[49].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 49"  href="GestionMesa.php?mesa=49&fecha='.$fecha.'">49
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoder10" bgcolor="'.$Mesas[47].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 47"  href="GestionMesa.php?mesa=47&fecha='.$fecha.'">47
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[46].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 46"  href="GestionMesa.php?mesa=46&fecha='.$fecha.'">46
										</a>
						 			</td>
						 		</tr>
						 		<tr>	
						 			<td class="bajoder10" bgcolor="'.$Mesas[36].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 36"  href="GestionMesa.php?mesa=36&fecha='.$fecha.'">36
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[37].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 37"  href="GestionMesa.php?mesa=37&fecha='.$fecha.'">37
										</a>
						 			</td>
						 		</tr>
						 	</table>
						 </td>
						 <td width="0%">
						 </td>
						 <td width="28%">
						 	<table width="100%" border=0 height="300px" cellspacing="10" border-spacing="10px">
						 		<tr>
						 			<td class="bajoderP"></td>
						 			<td class="bajoder6" bgcolor="'.$Mesas[69].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 69"  href="GestionMesa.php?mesa=69&fecha='.$fecha.'">69
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[68].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 68"  href="GestionMesa.php?mesa=68&fecha='.$fecha.'">68
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoderP"></td>
						 			<td class="bajoder6" bgcolor="'.$Mesas[62].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 62"  href="GestionMesa.php?mesa=62&fecha='.$fecha.'">62
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[63].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 63"  href="GestionMesa.php?mesa=63&fecha='.$fecha.'">63
										</a>
						 			</td>
						 		</tr>	
						 			<td class="bajoderP"></td>
						 			<td class="bajoder6" bgcolor="'.$Mesas[57].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 57"  href="GestionMesa.php?mesa=57&fecha='.$fecha.'">57
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[56].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 56"  href="GestionMesa.php?mesa=56&fecha='.$fecha.'">56
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoderP"></td>
						 			<td class="bajoder6" bgcolor="'.$Mesas[50].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 50"  href="GestionMesa.php?mesa=50&fecha='.$fecha.'">50
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[51].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 51"  href="GestionMesa.php?mesa=51&fecha='.$fecha.'">51
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoderP"></td>
						 			<td class="bajoder6" bgcolor="'.$Mesas[45].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 45"  href="GestionMesa.php?mesa=45&fecha='.$fecha.'">45
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[44].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 44"  href="GestionMesa.php?mesa=44&fecha='.$fecha.'">44
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoderP"></td>
						 			<td class="bajoder6" bgcolor="'.$Mesas[38].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 38"  href="GestionMesa.php?mesa=38&fecha='.$fecha.'">38
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[39].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 39"  href="GestionMesa.php?mesa=39&fecha='.$fecha.'">39
										</a>
						 			</td>
						 		</tr>
						 	</table>
						 </td>
						 <td width="2%"></td>
						 <td width="35%">
						 	<table width="100%" height="300px" cellspacing="10" border-spacing="10px">
						 		<tr>
						 			<td class="bajoder10" bgcolor="'.$Mesas[67].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 67"  href="GestionMesa.php?mesa=67&fecha='.$fecha.'">67
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[66].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 66"  href="GestionMesa.php?mesa=66&fecha='.$fecha.'">66
										</a>
						 			</td>
						 		</tr>
						 		<tr>	
						 			<td class="bajoder10" bgcolor="'.$Mesas[64].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 64"  href="GestionMesa.php?mesa=64&fecha='.$fecha.'">64
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[65].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 65"  href="GestionMesa.php?mesa=65&fecha='.$fecha.'">65
										</a>
						 			</td>
						 		</tr>	
						 			<td class="bajoder10" bgcolor="'.$Mesas[55].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 55"  href="GestionMesa.php?mesa=55&fecha='.$fecha.'">55
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[54].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 54"  href="GestionMesa.php?mesa=54&fecha='.$fecha.'">54
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoder10" bgcolor="'.$Mesas[52].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 52"  href="GestionMesa.php?mesa=52&fecha='.$fecha.'">52
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[53].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 53"  href="GestionMesa.php?mesa=53&fecha='.$fecha.'">53
										</a>
						 			</td>
						 		</tr>
						 		<tr>
						 			<td class="bajoder10" bgcolor="'.$Mesas[43].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 43"  href="GestionMesa.php?mesa=43&fecha='.$fecha.'">43
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[42].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 42"  href="GestionMesa.php?mesa=42&fecha='.$fecha.'">42
										</a>
						 			</td>
						 		</tr>
						 		<tr>	
						 			<td class="bajoder10" bgcolor="'.$Mesas[40].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 40"  href="GestionMesa.php?mesa=40&fecha='.$fecha.'">40
										</a>
						 			</td>
						 			<td class="bajoder10" bgcolor="'.$Mesas[41].'">
						 				<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 41"  href="GestionMesa.php?mesa=41&fecha='.$fecha.'">41
										</a>
						 			</td>
						 		</tr>
						 	</table>
						 </td>
						</tr>
					</table>
				</td>
			</tr>
					</table>
				</td>
			</tr>

		</table>
	</td>
	<td width="7%" align="right" style="border:1px solid green;">
		<table width="60%" height="982px" border=0>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[35].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 35"  href="GestionMesa.php?mesa=35&fecha='.$fecha.'">35
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[34].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 34"  href="GestionMesa.php?mesa=34&fecha='.$fecha.'">34
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[33].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 33"  href="GestionMesa.php?mesa=33&fecha='.$fecha.'">33
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[32].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 32"  href="GestionMesa.php?mesa=32&fecha='.$fecha.'">32
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[31].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 31"  href="GestionMesa.php?mesa=31&fecha='.$fecha.'">31
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[30].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 30"  href="GestionMesa.php?mesa=30&fecha='.$fecha.'">30
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[29].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 29"  href="GestionMesa.php?mesa=29&fecha='.$fecha.'">29
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[28].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 28"  href="GestionMesa.php?mesa=28&fecha='.$fecha.'">28
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[27].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 27"  href="GestionMesa.php?mesa=27&fecha='.$fecha.'">27
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[26].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 26"  href="GestionMesa.php?mesa=26&fecha='.$fecha.'">26
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[25].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 25"  href="GestionMesa.php?mesa=25&fecha='.$fecha.'">25
					</a>
				</td>
			</tr>
			<tr>
				<td class="der10" bgcolor="'.$Mesas[24].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 24"  href="GestionMesa.php?mesa=24&fecha='.$fecha.'">24
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[23].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 23"  href="GestionMesa.php?mesa=23&fecha='.$fecha.'">23
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[22].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 22"  href="GestionMesa.php?mesa=22&fecha='.$fecha.'">22
					</a>
				</td>
			</tr>
			<tr>
				<td class="der6" bgcolor="'.$Mesas[21].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: 21"  href="GestionMesa.php?mesa=21&fecha='.$fecha.'">21
					</a>
				</td>
			</tr>
			
		</table>
	</td>
</tr>
<tr>
	<td>
		<table width="70%">
			<tr>
				<td class="aux" bgcolor="'.$Aux[1].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: A1"  href="GestionMesa.php?mesa=A1&fecha='.$fecha.'">A1
					</a>
				</td>
			</tr>
		</table>
	</td>
	<td>
		<table width="100%" border=0 cellspacing="10">
			<tr>
				<td class="barra" bgcolor="'.$Barras[1].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B01"  href="GestionMesa.php?mesa=B1&fecha='.$fecha.'">B01
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[2].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B02"  href="GestionMesa.php?mesa=B2&fecha='.$fecha.'">B02
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[3].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B03"  href="GestionMesa.php?mesa=B3&fecha='.$fecha.'">B03
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[4].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B04"  href="GestionMesa.php?mesa=B4&fecha='.$fecha.'">B04
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[5].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B05"  href="GestionMesa.php?mesa=B5&fecha='.$fecha.'">B05
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[6].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B06"  href="GestionMesa.php?mesa=B6&fecha='.$fecha.'">B06
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[7].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B07"  href="GestionMesa.php?mesa=B7&fecha='.$fecha.'">B07
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[8].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B08"  href="GestionMesa.php?mesa=B8&fecha='.$fecha.'">B08
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[9].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B09"  href="GestionMesa.php?mesa=B9&fecha='.$fecha.'">B09
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[10].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B10"  href="GestionMesa.php?mesa=B10&fecha='.$fecha.'">B10
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[11].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B11"  href="GestionMesa.php?mesa=B11&fecha='.$fecha.'">B11
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[12].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B12"  href="GestionMesa.php?mesa=B12&fecha='.$fecha.'">B12
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[13].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B13"  href="GestionMesa.php?mesa=B13&fecha='.$fecha.'">B13
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[14].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B14"  href="GestionMesa.php?mesa=B14&fecha='.$fecha.'">B14
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[15].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B15"  href="GestionMesa.php?mesa=B15&fecha='.$fecha.'">B15
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[16].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B16"  href="GestionMesa.php?mesa=B16&fecha='.$fecha.'">B16
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[17].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B17"  href="GestionMesa.php?mesa=B17&fecha='.$fecha.'">B17
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[18].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B18"  href="GestionMesa.php?mesa=B18&fecha='.$fecha.'">B18
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[19].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B19"  href="GestionMesa.php?mesa=B19&fecha='.$fecha.'">B19
					</a>
				</td>
				<td class="barra" bgcolor="'.$Barras[20].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: B20"  href="GestionMesa.php?mesa=B20&fecha='.$fecha.'">B20
					</a>
				</td>
			</tr>
		</table>
	</td>
	
	<td align="right">
	<table  width="70%">
			<tr>
				<td class="aux" bgcolor="'.$Aux[2].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: A2"  href="GestionMesa.php?mesa=A2&fecha='.$fecha.'">A2
					</a>
				</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=3>
		<table width="100%" border=0 cellspacing="10">
			<tr>
				<td class="bajoder10" bgcolor="'.$Palcos[1].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P1"  href="GestionMesa.php?mesa=P1&fecha='.$fecha.'">P1
					</a>
				</td>
				<td class="bajoder10" bgcolor="'.$Palcos[2].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P2"  href="GestionMesa.php?mesa=P2&fecha='.$fecha.'">P2
					</a>
				</td>
				<td class="bajoder10" bgcolor="'.$Palcos[3].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P3"  href="GestionMesa.php?mesa=P3&fecha='.$fecha.'">P3
					</a>
				</td>
				<td class="bajoder10" bgcolor="'.$Palcos[4].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P4"  href="GestionMesa.php?mesa=P4&fecha='.$fecha.'">P4
					</a>
				</td>
				<td class="bajoder10" bgcolor="'.$Palcos[5].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P5"  href="GestionMesa.php?mesa=P5&fecha='.$fecha.'">P5
					</a>
				</td>
				<td class="bajoder10" bgcolor="'.$Palcos[6].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P6"  href="GestionMesa.php?mesa=P6&fecha='.$fecha.'">P6
					</a>
				</td>
				<td class="bajoder10" bgcolor="'.$Palcos[7].'">
					<a class="enlace" title="Gesti&oacuote;n sobre la Mesa: P7"  href="GestionMesa.php?mesa=P7&fecha='.$fecha.'">P7
					</a>
				</td>
			</tr>
		</table>
	</td>
</tr>

</table>
</div>

<div id="contenedor">

</div>
</body>
</html>
';?>