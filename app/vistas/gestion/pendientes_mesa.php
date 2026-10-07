<?php
		$n=$Consulta->num_rows;			
		$Mensaje="";
		if($n>0){
			$Mensaje='
						<form class="form-inline">
				            <div class="form-group">
				              <label class="sr-only">Seleccione el Cliente :  </label>
				              <p class="form-control-static"><strong>Seleccione el Cliente :  </strong></p>
				            </div>
				            <div class="form-group">
				              <select id="cliente" class="form-control" onchange="AsignaMesas()">
									<option value="0">Selecciona</option>';
										for ($i=0; $i <$n ; $i++) { 
										$resu=$Consulta->fetch_array();
										$Mensaje.='<option value="'.$resu[0].';'.$resu[4].'">['.$resu[0].'] '.$resu[3].' --> ('.$resu[4].')</option>';
										}
					$Mensaje.='</select>
				            </div>
           				</form>
					   <br>
					   <div id="gestion1">
					   	  <br><br><br><br>
					    </div>
			';
		}else{
			$Mensaje='<p class="perror">NO EXISTEN RESERVAS PENDIENTES PARA ASIGNAR MESA PARA EL DIA '.$Fecha.'</p>';
		}
		echo $Mensaje;
