<?php
		$Cadena='
		<form class="form-inline mitad">';
		for($i=0;$i<$CantMesas;$i++){
			$Cadena.='
			<div class="form-group">
              <label class="sr-only">Mesa No. '.($i+1).': </label>
              <p class="form-control-static"><strong>Mesa No. '.($i+1).': </strong></p>
            </div>
			<div class="form-group">
				<select id="M'.($i+1).'" class="form-control" onchange="validaIgual('.$CantMesas.')">
					<option value="0">Selecciona una Mesa</option>
						';
			$Consulta = $mesasLibres->reiniciar();
			$n=$Consulta->num_rows;
			for($j=0;$j<$n;$j++){
				$Res=$Consulta->fetch_array();
				$Cadena.='<option value="'.$Res[0].'">'.$Res[0].' ( '.$Res[1].' )</option>';
			}
			$Cadena.="</select></div><br>";
		}
		$Cadena.='</form><br>
		<input type="checkbox" value="S" id="respecial"> Reserva Especial<br><br>
		<button class="btn btn-primary" type="button" id="basigna" align="center" onclick="AsignaMesa('.$CantMesas.')">Asignar Mesa(s)</button>
			  <br><br><br><br>';
		echo $Cadena;
