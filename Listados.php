<?php
require_once("conect.php");
global $conexion;
require_once("auth.php");
exigirAdminPagina();
$_SESSION["autenticado"]="Y";
$IdLiving=$_SESSION['IdLiving'];
$fecha=fechaSegura($_GET['fecha']);
$tipo=codigo($_GET['tipo']);
switch ($tipo) {
  case 'K':
    $Query="SELECT mr.strmesa,
         r.strnombrereserva,
         r.STRNOMBREHOMENAJE,
         tc.strdescripcion, 
         ta.strdescripcion, 
         e.strdescripcion,
         CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
         r.strobservaciones,
         r.NUMRESERVA
          FROM tblreservas r, 
               tblmesareserva mr, 
               tbltipocelebra tc, 
               tbltipoasigna ta, 
               tblestados e,
               tblusuarios u
      WHERE r.dtmfechareserva = '".$fecha."'
            AND r.NUMRESERVA = mr.NUMRESERVA
            AND mr.STRESTADORESERVA =0
            AND tc.nummotivo = r.nummotivo
            AND ta.STRTIPOASIGNA = mr.STRTIPO
            AND r.NUMESTADO = e.numestado
            AND r.NUMIDENTLIV = u.NUMIDENTLIV
          ORDER BY 2 asc";
    break;
  case 'Cu':
    $Query="SELECT mr.strmesa,
         r.strnombrereserva,
         r.STRNOMBREHOMENAJE,
         tc.strdescripcion, 
         ta.strdescripcion, 
         e.strdescripcion,
         CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
         r.strobservaciones,
         r.NUMRESERVA
          FROM tblreservas r, 
               tblmesareserva mr, 
               tbltipocelebra tc, 
               tbltipoasigna ta, 
               tblestados e,
               tblusuarios u
      WHERE r.dtmfechareserva = '".$fecha."'
            AND r.NUMRESERVA = mr.NUMRESERVA
            AND mr.STRESTADORESERVA =1
            AND tc.nummotivo = r.nummotivo
            AND ta.STRTIPOASIGNA = mr.STRTIPO
            AND r.NUMESTADO = e.numestado
            AND r.NUMMOTIVO =1
            AND r.NUMESTADO not in(2,0)
            AND r.NUMIDENTLIV = u.NUMIDENTLIV
          ORDER BY 1 asc";
  break;
  default:
    $Query="SELECT mr.strmesa,
         r.strnombrereserva,
         r.STRNOMBREHOMENAJE,
         tc.strdescripcion, 
         ta.strdescripcion, 
         e.strdescripcion,
         CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
         r.strobservaciones,
         r.NUMRESERVA
          FROM tblreservas r, 
               tblmesareserva mr, 
               tbltipocelebra tc, 
               tbltipoasigna ta, 
               tblestados e,
               tblusuarios u
      WHERE r.dtmfechareserva = '".$fecha."'
            AND r.NUMRESERVA = mr.NUMRESERVA
            AND mr.STRESTADORESERVA =1
            AND tc.nummotivo = r.nummotivo
            AND ta.STRTIPOASIGNA = mr.STRTIPO
            AND r.NUMESTADO = e.numestado
            AND mr.STRTIPO='".$tipo."'
            AND r.NUMIDENTLIV = u.NUMIDENTLIV
          ORDER BY 2 asc";
    break;
}
            
$Consulta = $conexion->query($Query)
    or die(errorBD());
$nfilas = $Consulta->num_rows;
$FechaCalc2=strtotime(date("Y-m-d H:i:s",mktime(23,40,59,3,3,2017)));
	//$FechaCalc2=strtotime('2016-10-28');
	$FechaHoyBase= strtotime(date('Y-m-d H:i:s').'');
	$i=($FechaCalc2-$FechaHoyBase);
	
$html='<html>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="css/estilo.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="css/estilo.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
<body>
<div class="container">
<table class="table table-bordered" width="98%">
  <tr>
      <td colspan=2> <a href="reservoir.php?fecha='.$fecha.'&orden=2" style="font-size:12pt;"><< Atras</a></td>
      <td style="font-size:12pt;" colspan=7><b>Total Registros: </b>'.$nfilas.'</td>
  </tr>
</table>

<div style="border:1px solid black;overflow-y:scroll;width:98%;">
<table class="table table-hover table-bordered" width="100%">
  <tr>
    <th>No.</th>    
    <th>No. Reserva</th>		
	  <th>No. Mesa</th>
 	  <th>NOMBRE</th>
    <th>NOMBRE HOMENAJEADO</th>
  	<th>MOTIVO</th>
	  <th>ESTADO</th>
	  <th>OBSERVACIONES</th>
  </tr>';
  $CadCu="";
  for($i=0;$i< $nfilas;$i++){
  	$res=$Consulta->fetch_array()
  		or die(errorBD());
    $CadCu.=$res[0].", ";  
  	$html.='<tr>
      <td>'.($i+1).'</td>
  		<td>'.$res[8].'</td>
  		<td>'.$res[0].'</td>
  		<td>'.htmlentities($res[1]).'</td>
  		<td>'.htmlentities($res[2]).'</td>
  		<td>'.htmlentities($res[3]).'</td>
  	  <td>'.$res[5].'</td>
      <td>'.$res[7].'</td>
  	</tr>';
  	}
  

$html.='
</table>
!!--Fin del Listado --!!
</div>
<div style="border:1px solid black;overflow-y:scroll;width:98%;">
'.$CadCu.'
</div>
</div>
';
echo $html;
?>