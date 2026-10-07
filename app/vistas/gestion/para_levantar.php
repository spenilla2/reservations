<?php
		if($nfilas==0){
			$Mensaje='
				<p class="perror">
				No existen reservas para liberar!
				</p>
			';
		}else{

			$Mensaje='
					<input type="button" class="btn btn-primary" value="Levantar Reservas" onclick="LevantaReserva()">
					<input type="hidden" id="totres" value="'.$nfilas.'">
					<input type="hidden" id="fecha1" value="'.$FechaC.'">
					<table class="table table-hover table-bordered">
						<tr>
							<th>No.</th>
							<th>Reserva</th>
							<th>Cliente</th>
							<th>No. Personas</th>
							<th>Mesa</th>
							<th>Observaciones</th>
							<th>Mantener</th>
						</tr>';
			for($i=0;$i<$nfilas;$i++){
			$Res=$Consulta->fetch_array();
			$Mensaje.='
			<tr>
				<td>'.($i+1).'</td>
				<td>'.$Res[0].'</td>
				<td>'.$Res[3].'</td>
				<td>'.$Res[4].'</td>
				<td>'.$Res[11].'</td>
				<td>'.$Res[7].'</td>
				<td><input type="checkbox" id="C'.($i+1).'" value="'.$Res[0].'"></td>
			</tr>

			';
			}
			$Mensaje.='
			</table>
			';
		}
		//$Mensaje=$nfilas;
		echo $Mensaje;
