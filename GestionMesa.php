<?php
require_once("conect.php");
global $conexion;
$Mesa=codigo($_GET['mesa']);
$Fecha=fechaSegura($_GET['fecha']);
require_once("auth.php");
exigirAdminPagina();
$_SESSION["autenticado"]="Y";
$IdLiving = $_SESSION['IdLiving'];
switch($Mesa){
  case 97:
      $Mesa="A1";
  break;
  case 98:
      $Mesa="A2";
  break;
  case 101:
      $Mesa="B1";
  break;
  case 102:
      $Mesa="B2";
  break;
  case 103:
      $Mesa="B3";
  break;
  case 104:
      $Mesa="B4";
  break;
  case 105:
      $Mesa="B5";
  break;
  case 106:
      $Mesa="B6";
  break;
  case 107:
      $Mesa="B7";
  break;
  case 108:
      $Mesa="B8";
  break;
  case 109:
      $Mesa="B9";
  break;
  case 110:
      $Mesa="B10";
  break;
  case 111:
      $Mesa="B11";
  break;
  case 112:
      $Mesa="B12";
  break;
  case 113:
      $Mesa="B13";
  break;
  case 114:
      $Mesa="B14";
  break;
  case 115:
      $Mesa="B15";
  break;
  case 116:
      $Mesa="B16";
  break;
  case 117:
      $Mesa="B17";
  break;
  case 118:
      $Mesa="B18";
  break;
  case 119:
      $Mesa="B19";
  break;
  case 120:
      $Mesa="B20";
  break;
  case 201:
      $Mesa="P1";
  break;
  case 202:
      $Mesa="P2";
  break;
  case 203:
      $Mesa="P3";
  break;
  case 204:
      $Mesa="P4";
  break;
  case 205:
      $Mesa="P5";
  break;
  case 206:
      $Mesa="P6";
  break;
  case 207:
      $Mesa="P7";
  break;

}
$sql = "SELECT * 
          FROM tblmesareserva mr
         WHERE mr.dtmfechareserva = '".$Fecha."' 
           AND mr.strmesa = '".$Mesa."'
           AND mr.strestadoreserva=1";
$queryMReservada = $conexion->query($sql);
$nfilasMreservada = $queryMReservada->num_rows;

$sql = "SELECT * 
             FROM tblmesas M 
            WHERE M.STRMESA NOT IN(
                    SELECT MR.STRMESA 
                      FROM tblmesareserva MR 
                     WHERE MR.DTMFECHARESERVA = '".$Fecha."'
                       AND MR.STRESTADORESERVA = '1'
                    )";
$queryMlibre = $conexion->query($sql);
$nMesasLibres = $queryMlibre->num_rows;
echo '
<html>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
    <script src="js/jquery-latest.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
    .onoffswitch {
        position: relative; width: 90px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "BIEN";
        padding-left: 10px;
        background-color: #0099FF; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "MAL";
        padding-right: 10px;
        background-color: #FF0000; color: #FFFFFF;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 18px; margin: 6px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 56px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }    
</style>
<body><br>
<div class="container">
<div style="border:1px solid black;background:#FFF;padding:10px;">  
<h2>GESTION A LA MESA:('.$Mesa.')</h2>

';
if($nfilasMreservada>0){
  $res = $queryMReservada->fetch_array();
  $sql = "SELECT r.NUMRESERVA,
                 r.DTMFECHARESERVA,
                 CONCAT_WS(' ',u.STRNOMBRES,u.STRAPELLIDOS) as NombreUsuario,
                 r.STRNOMBRERESERVA,
                 r.NUMPERSONAS,
                 t.STRDESCRIPCION,
                 r.STRNOMBREHOMENAJE,
                 r.STROBSERVACIONES,
                 e.STRDESCRIPCION,
                 r.DTMFECHAREALIZARES,
                 r.STRMESAASIGNA,
                 ta.STRDESCRIPCION,
                 mr.STRTIPO,
                 mr.NUMCONSECUTIVO,
                 r.NUMMOTIVO,
                 r.STRCUMPLERESERVA,
                 r.DTMFECHAREALIZARES,
                 r.NUMESTADO,
                 r.NUMMOTIVO  
            FROM tblreservas r,
                 tblusuarios u,
                 tbltipocelebra t,
                 tblestados e,
                 tblmesareserva mr,
                 tbltipoasigna ta 
           WHERE r.NUMRESERVA = ".$res[2]." 
             AND r.NUMIDENTLIV = u.NUMIDENTLIV
             AND r.NUMMOTIVO = t.NUMMOTIVO
             AND r.NUMESTADO = e.NUMESTADO
             AND r.NUMRESERVA = mr.NUMRESERVA
             AND SUBSTRING(mr.STRTIPO,1,1) = ta.STRTIPOASIGNA
             AND mr.STRMESA='".$Mesa."'";
  $queryDataReserva = $conexion->query($sql);    
  $resultado = $queryDataReserva->fetch_array();
  $desconf="";
  $VarY="";
  $VarN="";
  if($resultado[14]==1){
    if($resultado[15]=="S"){
      $VarY=" selected";
      $VarN="";
    }else{
      $VarY="";
      $VarN=" selected";
    }
  }else{
    $desconf=" disabled";
    $VarY="";
    $VarN=" selected";
  }
 $P=$resultado[1];
 $chek="";
 if(strlen($resultado[12])>1){
   $chek=" ";
 }else{
  $chek=" checked";
 }
	echo '
  <div style="border:1px solid black; height:543px;overflow-y:scroll;" id="respuesta">
    <input type="hidden" id="Reserva" value="'.$resultado[0].'">
    <input type="hidden" id="fecha" value="'.$resultado[1].'">
    <input type="hidden" id="Mesa" value="'.$Mesa.'">
    <input type="hidden" id="tipof" value="'.$resultado[12].'">
    <input type="hidden" id="mesas" value="'.$resultado[10].'">

    <h2 style="padding-left:10px;"><strong>INFORMACI&Oacute;N DE LA ASIGNACI&Oacute;N</strong></h2>
          <ul style="list-style-type:none;font-size:16pt;">
            <li><b>Reserva No: </b>'.$resultado[0].'</li>
            <li><b>Reserva Realizada: </b>'.$resultado[16].'</li>
            <li><b>Realizada Por: </b>'.$resultado[2].'</li>
            <li><b>Reserva a Nombre de: </b>'.$resultado[3].'</li>
            <li><b>N&uacute;mero de Personas: </b>';
            if($resultado[12]=="K"){
              echo '<select id="ActCantPer">';
              for($i=1;$i<=10;$i++){
                $cad="";
                if($i==$resultado[4]){
                    $cad=" selected";
                }else{

                }
                echo '<option value="'.$i.'" '.$cad.'>'.$i.'</option>';
              }
              echo '</select>
                      <input type="button" value="Act" onClick="ActMCompartida()">
            </li>';
            }else{
              echo ''.$resultado[4].'</li>';
            }
            echo '
            
            <li><b>Tipo de Reserva: </b>'.htmlentities($resultado[5]).'</li>
            <li><b>Nombre del Homenajead@: </b>'.$resultado[6].'</li>
            <li><b>Observaciones: </b>'.$resultado[7].'
            </li>
            <li><b>Mesa(s) Asignada(s): </b>'.$resultado[10].'</li>
            <li><b>Tipo de Asignaci&oacute;n: </b>'.$resultado[11].'</li>
            <li><b>Estado de la Reserva: </b>'.$resultado[8].'</li>
            <li><b>Estado desde: </b>'.$resultado[9].'</li>';
            if($resultado[18]==1&&$resultado[17]==3){
                echo '
                  <li><b>Llega Cupo completo?</b>
                  <select id="cumpli" '.$desconf.'>
                    <option value="S" '.$VarY.'>Si</option>
                    <option value="N" '.$VarN.'>No</option>
                  </select>
                  <input type="button" class="btn btn-primary" value="Actualizar" onClick="EntrMani()">
                  </li>';
            }else{

            }
          echo '
            <li><input type="button" class="btn btn-primary" value="Modificar Datos" onClick="GoModificar()">
            </li>
            <li style="padding-top:10px;">
              <div class="onoffswitch">
            <input type="checkbox" onchange="mesaInamovible()" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" '.$chek.'>
            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
            </div>
            </li>
        </ul></p>
        
        <div id="resform" class="container" style="width:80%;">
        
        </div> 

        </div>

        <div style="margin-top:2px;">
            <input type="button" class="btn btn-primary" value="Liberar Mesa" onClick="LiberaMesa()">
            ';
            if($resultado[12]=='N'||$resultado[12]=='E'){
              echo '&nbsp;&brvbar;&nbsp;&nbsp;<input type="button" class="btn btn-primary" value="Confirma Reserva" onClick="ConfirmaReserva('.$resultado[0].')">
              
              ';
            }
            echo '
             &nbsp;&brvbar;&nbsp;&nbsp;<b>Trasladar a:</b>
             <select id="traslada">
             <option value=0></option>
             ';
             for($i=0;$i<$nMesasLibres;$i++){
              $Res = $queryMlibre->fetch_array();
                echo '<option value="'.$Res[0].'">'.$Res[0].' ( '.$Res[1].' )</option>';
             }
            echo '</select>
            <input type="button" class="btn btn-primary" value="Traslada" onClick="TrasladarGrupo()">&nbsp;&nbsp;&brvbar;&nbsp;
            <input type="button" class="btn btn-primary" value="Volver" onClick="GoBack()">
            <input type="hidden" id="fecha2" value="'.$Fecha.'">
            <input type="hidden" id="Mesa" value="'.$Mesa.'">
            <input type="hidden" id="Reserva" value="'.$resultado[0].'">
            <input type="hidden" id="MRConse" value="'.$resultado[13].'">
            </div>
            </div>
        ';
}else{
echo '
<div style="border:1px solid black; height:543px;overflow-y:scroll;padding:10px;width:100%;" id="respuesta">
    Tipo de Asignaci&oacute;n
      <form name="tipoasigna">
        <input type="radio" name="tcliente" value="2" onClick="AsignacionMesa(2)"> Cliente Foraneo
        <input type="radio" name="tcliente" value="3" onClick="AsignacionMesa(3)"> Bloquear<br>
        <input type="radio" name="tcliente" value="4" onClick="AsignacionMesa(4)"> Compartida
        <input type="hidden" name="tcliente" value="5" checked>
      </form>
    
  <div id="divCliVip" style="margin:10px;width:90%; height:260px;border-top:1px solid black;padding:10px;"> 
    </div>
  <input type="hidden" id="idLiv" value="'.$IdLiving.'"> 
  <input type="hidden" id="fecha" value="'.$Fecha.'"">
  <input type="hidden" id="Mesa" value="'.$Mesa.'">
  <input type="hidden" id="IdClvp" value="0">
  <input type="hidden" id="TipoAsigna" value="">';
}
echo '</div>
</div>
  </body>
  </html>'; 

