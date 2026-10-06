<?php
include("head.php");
require_once("conect.php");
global $conexion;
$_SESSION['autenticado']="Y";
$NReserva=$_SESSION['nombre'];
$Perfil=$_SESSION['Perfil'];
$IdLiving = $_SESSION['IdLiving'];

$MA=codigo($_GET['MA']);
$tipo=codigo($_GET['Tipo']);
$Fecha=fechaSegura($_GET['fecha']);
$NumReserva=(int)$_GET['Rnum'];
if(!reservaPermitida($conexion, $NumReserva)){
  echo "<script>window.location='consultaReservas.php';</script>";
  exit();
}
$diasem=date("w",mktime(12,0,0,(substr($Fecha, 3,2)),(substr($Fecha, 0,2)),(substr($Fecha, 6,4))));
$imagen="";
  
$RecibeCorreoDefault=" checked";
$deshabilita="";
$imagen="";
$NombreBoton="Actualizar Reserva";
$TituloForm="Actualizaci&oacuten de Reservas";
$Query1 = "SELECT * 
             FROM tblreservas 
            WHERE NUMRESERVA=".$NumReserva;
$Consulta  = $conexion->query($Query1);
$Result = $Consulta->fetch_array();
$NombreReserva=$Result[3];
$NumPer=$Result[4];
$TipoCele=$Result[5];
$NomH=$Result[6];
$RObservac=$Result[7];
$Tipo="U";
$des="";
if($TipoCele==1){
  $des="";
}else{
  $des=" disabled";
}
if($MA==""){
    $deshabilita="";
}else{
    $deshabilita=" disabled";
}
$Query2 = "SELECT * 
             FROM tblclientesvip vp 
            WHERE vp.NUMIDENTLIV = ".$IdLiving." 
         ORDER BY 2 ASC";
$consulta4 = $conexion->query($Query2);
$numCliVIP = $consulta4->num_rows;
$Query3 = "SELECT * 
             FROM tbltipocelebra 
         ORDER BY 2 ASC";
$consulta3 = $conexion->query($Query3);
$numcelebra = $consulta3->num_rows;
switch ($diasem) {
    case 0:
      $imagen="images/Domingo.jpg";
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
echo '
<div class="container" style="background-color:white;padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">

		  <input type="hidden" id="idLiving" value="'.$IdLiving.'">
	    <input type="hidden" id="fechareserva">
	    <input type="hidden" id="numReserva" value="'.$NumReserva.'">
	    <input type="hidden" id="tipoReservaK" value="'.$tipo.'">
	    <input type="hidden" id="nombreUsuario" value="'.$NReserva.'">
	    <input type="hidden" id="idClienteVip" value="0">
	    <input type="hidden" id="Perfil" value="'.$Perfil.'">
      <input type="hidden" id="tipo" value="'.$tipo.'">
  <div class="row">
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="text-align:center;">
            <img class="resdia" src="'.$imagen.'">
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <h4><strong>Formulario para Actualizaci&oacute;n de Reservas</strong></h4><br>
        <form id="reserva" class="form-horizontal mitad" >
          <fieldset class="fres">
            <div class="form-group">
                    <label class="col-lg-5 col-sm-6 col-md-5 control-label">
                      Reserva a Nombre de :
                    </label>
                    <div class="col-lg-6 col-sm-5 col-md-6">
                      <input type="text" class="form-control" id="rname" value="'.$NombreReserva.'" required>
                    </div>
            </div>        ';
            if($Perfil==1){
                      $RecibeCorreoDefault="";
              echo '
                  <div class="form-group">
                    <label class="col-lg-5 col-sm-6 col-md-5 control-label">
                      Es cliente VIP? :
                    </label>
                    <div class="col-lg-6 col-sm-5 col-md-6">
                      <select id="clientesVIP" class="form-control" onchange="ClienteVIP()" '.$deshabilita.'>
                                        <option value="0">NO</option>';
                                          for($i=0;$i<$numCliVIP;$i++){
                                              $result = $consulta4->fetch_array();
                                     echo '
                                          <option value="'.$result[0].'">'.htmlentities($result[1]).'</option>';
                                                      }
                                                 echo '
                                      </select>';     
                                              }else{

                                              };
            echo '
                    </div>
                  </div>
               <div class="form-group">
                    <label class="col-lg-5 col-sm-6 col-md-5 control-label">
                      N&uacute;mero de Personas :
                    </label>
                    <div class="col-lg-6 col-sm-5 col-md-6">
                    <span id="cper"><h4>'.$NumPer.'</h4></span>
            <input type="range" class="form-control-range" min=2 max=60 onchange="prueba(3)" id="rnumperson" Value ="'.$NumPer.'">
                      </div>
                    </div>
                    <div class="form-group">
                    <label class="col-lg-5 col-sm-6 col-md-5 control-label">
                      Tipo de Celebraci&oacute;n : 
                    </label>
                    <div class="col-lg-6 col-sm-5 col-md-6">
                      <select id="rtipocelabra" class="form-control" onchange="PasaNombre()">';
                    for($i=0;$i<$numcelebra;$i++){
                      $result=$consulta3->fetch_array();
                                        $TC="";
                                        if($result[0]==$TipoCele){
                                            $TC=" selected";
                                        }else{
                                            $TC="";
                                        }        
                      echo '<option value='.$result[0].' '.$TC.'>'.htmlentities($result[1]).'</option>';
                    }
                    echo '
                        </select> 
                    </div>
                    </div>
                    <div class="form-group">
                    <label class="col-lg-5 col-sm-6 col-md-5 control-label">
                      Nombre del(la) Homenajead@ : 
                    </label>
                    <div class="col-lg-6 col-sm-5 col-md-6">
                      <input type="text" id="rhomena" size="20" value="'.$NomH.'" class="form-control" '.$des.'>
                      <span id="helpBlock" class="help-block" style="text-align:justify;">Si tienes m&aacute;s de un Homenajeado separalos con el s&iacute;mbolo coma(,).</span>
                    </div>
                   </div>
                   <div class="form-group">
                   <label class="col-lg-5 col-sm-6 col-md-5 control-label">
                    Observaciones : 
                  </label>
                  <div class="col-lg-6 col-sm-5 col-md-6">
                    <textarea class="form-control" style="text-align:justify;" cols="40" rows="5" id="robserva" placeholder="Especifica alguna Observaci&oacute;n que debamos conocer acerca de tu reserva">'.$RObservac.'</textarea>
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
 <div class="resform" id="resform">
    
    </div>

</div>
   <br><br><br>
';
include("foot.php");
?>