<?php
echo '<div class="container" style="background-color:white; padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
        <h3><strong>Confirmaci&oacute;n de Reservas</strong></h3><br>
	      <form class="form-inline">
            <div class="form-group">
              <label class="sr-only">Indique el N&uacute;mero de la Reserva : </label>
              <p class="form-control-static"><strong>Indique el N&uacute;mero de la Reserva : </strong></p>
            </div>
            <div class="form-group">
              <input type="tel" class="form-control" id="numreserva" size="15" style="text-align:right;">
            </div>
            <button type="button" class="btn btn-primary" onclick="ValidaReserva()"><img src="images/search.png" ></button>
        </form>
            <div class="resform">
                <div id="respuesta" style="padding:1em;text-align:left;">
                      
                </div>
            </div>

        </div>
        <br><br>
                      ';
