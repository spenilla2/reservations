<?php
require_once("auth.php");
exigirAdminPagina();
$Fecha=Date('Y-m-d');
include("head.php");
echo '<div class="container" style="background-color:white; padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
    <h3><strong>Procedimiento para Levantar Reservas</strong></h3>
        <form class="form-inline">
            <div class="form-group">
              <label class="sr-only">Selecciona la Fecha :</label>
              <p class="form-control-static"><strong>Selecciona la Fecha del d&iacute;a : </strong></p>
            </div>
            <div class="form-group">
              <input type="hidden" id="NumMesas">
            <input name="nombre_de_la_caja" class="form-control" Placeholder="dd/mm/yyyy" type="date" id="dc" size="15" value="'.$Fecha.'"> 
            <button type="button" class="btn btn-primary" onclick="CargaReservasPendientes()"><img src="images/search.png" ></button>
            </div>
        </form>      
        <div class="divlibm" id="resultado">
        </div>
	  </div>
    <br><br><br><br>
    <br><br><br><br>
    <br><br><br><br>
    <br><br><br><br>
    <br><br><br><br>
';

include("foot.php");
?>