<?php
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
  	$res=$Consulta->fetch_array();
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
