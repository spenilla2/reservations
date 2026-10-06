<?php
require_once("auth.php");
exigirAdminPagina();
$Fecha = Date('Y-m-d');
include("head.php");
?>
<div class="container"  style="background-color:white; padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
        <h3><strong>Asignaci&oacute;n de Mesas</strong></h3>
        <form class="form-inline">
            <div class="form-group">
              <label class="sr-only">Selecciona la Fecha :</label>
              <p class="form-control-static"><strong>Selecciona la Fecha de las Reservas : </strong></p>
            </div>
            <div class="form-group">
              <input type="hidden" id="NumMesas">
              <input name="nombre_de_la_caja" class="form-control" Placeholder="YYYY-MM-DD" type="date" id="dc" size="15" value="<?php echo $Fecha;?>"> 
            <button type="button" class="btn btn-primary" onclick="CargaReservas()"><img src="images/search.png" ></button>
            </div>
        </form>
        <div style="width:92%; padding:0.5em; overflow-y:scroll;height:auto;" id="resultado">
              
        </div>
     </div>
     <br><br><br><br>

<?php
include("foot.php");
?>
