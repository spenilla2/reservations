<?php
		$Mensaje="";
		if($queryMR){
			$Mensaje='
				<div class="col-md-12">
				<div class="alert alert-success">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Mensaje:</strong> Reserva Confirmada Satisfactoriamente! <br>Bienvenido a Living Night Club.
				</div>
			</div>';
		}else{
			$Mensaje='<div class="col-md-12">
				<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> No existen Reservas para este d&iacute;a
				</div>
			</div>';
		}
		echo $Mensaje;
