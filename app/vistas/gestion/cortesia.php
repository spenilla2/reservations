<?php
		$Mensaje="";
		if($Consulta == 1){
			$Mensaje='
				<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> Reserva Actualizada Satisfactoriamente! 
				</div>
			</div>
		</div>';
		}else{
			$Mensaje='
				<div class="row">
			<div class="col-md-12">
				<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> Ha Ocurrido un Error
				</div>
			</div>
		</div>';
		}	
	
	echo $Mensaje;
