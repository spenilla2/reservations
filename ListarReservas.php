<?php
require_once("auth.php");
exigirAdminPagina();
require_once("conect.php");
global $conexion;
include("head.php");
$Tipo = entero($_GET['tipo']);
$Mensaje="";
if($Tipo==1){
  $Mensaje="Consultar Reservas";
}else{
  $Mensaje="Consultar Reservas Efectivas para Cortesia";
}
$Fecha = date('Y-m-d');
echo '
<div class="container" style="background-color:white; padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
<div>
      <h3><strong>'.$Mensaje.'</strong></h3><br>
      <form class="form-inline">
            <div class="form-group">
              <label class="sr-only">Fecha de Reserva:</label>
              <p class="form-control-static"><strong>Selecciona la Fecha : </strong></p>
            </div>
            <div class="form-group">
              <input type="date" id="fecha" class="form-control" value="'.$Fecha.'" size=10 placeholder="YYYY-MM-DD">
            </div>
            <button type="button" class="btn btn-primary" onclick="ListaReservas('.$Tipo.')"><img src="images/search.png" ></button>
        </form>
</div><br>
     <div class="table-responsive" id="Respuesta" >
     </div>
</div>
    <br><br><br><br>';
include("foot.php");
?>
