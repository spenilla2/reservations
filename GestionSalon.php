<?php
require_once("auth.php");
exigirAdminPagina();
include("head.php");
$Fecha=date('Y-m-d');
?>
<div class="container"  style="background-color:white; padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
		<h3>Gesti&oacute;n al Sal&oacute;n</h3><br>
    <form class="form-inline">
            <div class="form-group">
              <label class="sr-only">Selecciona la Fecha :</label>
              <p class="form-control-static"><strong>Selecciona la Fecha : </strong></p>
            </div>
            <div class="form-group">
              <input type="hidden" id="NumMesas">
              <input name="nombre_de_la_caja" class="form-control" Placeholder="YYYY-MM-DD" type="date" id="dc" size="15" value="<?php echo $Fecha;?>">
            <button type="button" class="btn btn-primary" onclick="LlamaMapa()"><img src="images/search.png" ></button>
            </div>
        </form>
            		  

      </div>
      <br><br><br><br>
                      <br><br><br><br>
                      <br><br><br><br>
                      <br><br><br><br>
                      <br><br><br><br>    
<?php 
include("foot.php");
?>