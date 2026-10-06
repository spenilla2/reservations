<?php
include("head.php");
require_once("conect.php");
global $conexion;
$_SESSION["autenticado"]="Y";
$tipo=codigo($_GET['Tipo']);
$NumReserva=0;
$NReserva=$_SESSION['nombre'];
$Perfil=$_SESSION['Perfil'];
$Idliving=$_SESSION['IdLiving'];
$MA=codigo($_GET['MA']);
$NumPer=0;
$TipoCele="";
$NomH="";
$RObservac="";
$RecibeCorreoDefault=" checked";
$deshabilita="";
$imagen="";
$Fecha=date('Y-m-d');

?>

  <section class="container">
		  <input type="hidden" id="idLiving" value="<?php echo $Idliving;?>">
	    <input type="hidden" id="fechareserva">
	    <input type="hidden" id="numReserva" value="<?php echo $NumReserva;?>">
	    <input type="hidden" id="tipoReservaK" value="<?php echo $tipo;?>">
	    <input type="hidden" id="nombreUsuario" value="<?php echo $NReserva;?>">
	    <input type="hidden" id="idClienteVip" value="0">
	    <input type="hidden" id="Perfil" value="<?php echo $Perfil;?>">
      <input type="hidden" id="tipo" value="<?php echo $tipo;?>">   
      <div style="background-color:white;padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
       <h3><strong>Realizar Reservas</strong></h3><br>
   			<form class="form-inline" >
            <div class="form-group">
              <label class="sr-only">Fecha de Reserva:</label>
              <p class="form-control-static"><strong>Indica la Fecha de la Reserva:</strong></p>
            </div>
            <div class="form-group">
              <input name="nombre_de_la_caja" class="form-control" Placeholder="YYYY-MM-DD" type="date" id="dc" size="10" value="<?php echo $Fecha;?>">
            </div>
            <button type="button" class="btn btn-primary" onclick="ResevaForm()"><img src="images/search.png" ></button>
        </form>
        <br>
        
	 <div class="resform" id="resform" style="background-color:white;" >
   </div>
  </div>
   <br><br><br>
 </section>  

<?php 
include("foot.php");
?>