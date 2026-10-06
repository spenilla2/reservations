<?php
require_once("auth.php");
exigirAdminPagina();
require_once("conect.php");
global $conexion;
$fecha=fechaSegura($_GET['fecha']);

$sql = "SELECT r.strnombrereserva,
               r.STRNOMBREHOMENAJE,
               r.STRMESAASIGNA, 
               COUNT(*) AS Total
			    FROM tblreservas r, 
               tblmesareserva mr, 
               tbltipocelebra tc, 
               tbltipoasigna ta, 
               tblestados e
		     WHERE r.dtmfechareserva = '".$fecha."'
           AND r.NUMRESERVA = mr.NUMRESERVA
           AND mr.STRESTADORESERVA =1
           AND tc.nummotivo = r.nummotivo
           AND r.nummotivo=3
           AND r.numestado=3
           AND ta.STRTIPOASIGNA = mr.STRTIPO
           AND r.NUMESTADO = e.numestado
      GROUP BY r.strnombrereserva,
               r.STRNOMBREHOMENAJE
       ORDER BY 1 ASC";
$query  = $conexion->query($sql);
$nfilas = $query->num_rows;

$html='<html>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="css/estilo.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/bootstrap.css">
    
    <script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
<body>
<div class="container">
<br>
<table class="table table-bordered" width="100%">
  <tr>
  		<td> <a href="dashboard.php?fecha='.$fecha.'" style="font-size:12pt;"><< Atras</a></td>
  		<td style="font-size:12pt;"><b>Total Cumplea&ntilde;os: </b>'.$nfilas.'</td>
  </tr>
  </table>
<div class="table-responsive" style="border:1px solid black;overflow-y:scroll;width:98%;">
<table class="table table-hover table-bordered" width="100%">
  <tr>		
    	<th>NO.</th>
    	<th>NOMBRE RESERVA</th>
      <th>NOMBRE HOMENAJEADO</th>
      <th>Mesa(s)</th>
  </tr>';
    for($i=0;$i<$nfilas;$i++){
    	$res = $query1->fetch_array();
    	$html.='<tr>
    		<td style="font-size:12pt;">'.($i+1).'</td>
    		<td style="font-size:12pt;">'.htmlentities($res[0]).'</td>
    		<td style="font-size:12pt;">'.htmlentities($res[1]).'</td>
        <td style="font-size:12pt;">'.$res[2].'</td>
    	</tr>';
  	}
$html.='
</table>
!!Fin del Listado!!
</div>

</div>
';
echo $html;