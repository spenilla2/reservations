<?php
		$Variable="";
		switch ($Codigo) {
			case 1:
				$Consulta1 = $clientesVip;
				$filasCV = $Consulta1->num_rows;
				$consulta3 = $tiposCelebracion;
				$numcelebra = $consulta3->num_rows;
				$Variable=$Fecha.'
				<table class="tdasiM" border="1">
						<tr>
							<td>
								Selecciona el Cliente VIP
							</td>
							<td>
								<select id="clivips" onchange="traeNombre()">
          							<option value=""></option>';
          						for($i=0;$i<$filasCV;$i++){
            						$resulCV=$Consulta1->fetch_array();
            						$Variable .='<option value="'.$resulCV[0].'">'.htmlentities($resulCV[1]).'</option>';
          						}
          		$Variable.='</select>
								</td>
						</tr>
						<tr>
							<td>
								Nombre del Cliente
							</td>
							<td>
								<input type="text" id="AMCliN" value="" size="30" disabled>				
							</td>
						</tr>
						<tr>
							<td>
								N&uacute;mero de Personas
							</td>
							<td>
								<select id="CantPer">
          							<option value="0"></option>';
          							for($i=2;$i<=60;$i++){
          								$Variable.='<option value="'.$i.'">'.$i.'</option>';	
          							}
          							$Variable.='				
							</td>
						</tr>
						<tr>
							<td>
							  Tipo de Celebraci&oacute;n
							</td>
							<td>
								<select id="AMTCeb" onclick="ValidaCelebra()">';
    								for($i=0;$i<$numcelebra;$i++){
    									$result=$consulta3->fetch_array();
    									$cadena12="";
    									if($result[0]==5){
    										$cadena12=" selected";
    									}		
                                        $Variable.='<option value='.$result[0].$cadena12.'>'.htmlentities($result[1]).'</option>';
    								}
    								$Variable.='</select>
							</td>
							 <tr>
							 	<td>
							 		Nombre del Homenajeado
							 	</td>
							 	<td>
							 		<input type="text" id="AMTHome" size=30   disabled>
							 	</td>
							 </tr>
							
						</tr>
						<tr>
							<td>Observaciones</td>
							<td><input type="text" value="" size="30" id="AMObse" onkeyup=Mayus(this)></td>
						</tr>
						<tr>
							<td colspan=2 align="right">
								<input type="button" value="Aceptar" onclick=AlmacenaRCVIP()>
							</td>
						</tr>
				</table>

				';
				
			break;
			case 2:
				$query = $tiposCelebracion;
				$numcelebra = $query->num_rows;
				$Variable='
				<div>
					<b>Descripci&oacute;n del Cliente</b>
					<br>
					<select id="CFSe" onchange="GestionForaneo()">
				   			<option value=0>Selecciona una Opci&oacute;n</option>
				   			<option value=1>Define Nombre</option>
				   			<option value=2>Pareja</option>
				   			<option value=3>Grupo 3</option>
				   			<option value=4>Grupo 4</option>
				   			<option value=5>Grupo 5</option>
				   			<option value=6>Grupo 6</option>
				   			<option value=7>Grupo 7</option>
				   			<option value=8>Grupo 8</option>
				   			<option value=9>Grupo 9</option>
				   			<option value=10>Grupo 10</option>
				   			<option value=11>Grupo 11</option>
				   			<option value=12>Grupo 12</option>
				   			<option value=13>Grupo 13</option>
				   			<option value=14>Grupo 14</option>
				   			<option value=15>Grupo 15</option>
				   			<option value=20>Grupo 20</option>
				   			<option value=21>Grupo 20+</option>
				   		</select><br>
				   	<b>Nombre del Cliente</b><br>
				   	<input type="text" id="CFIn1" size=25 disabled><br>
				   	<b>N&uacute;mero de Personas</b> <span id="cper"><h4>0</h4></span>
				   	<input type="range" class="form-control-range" min=2 max=15 onchange="control(2)" id="CantPer" Value =2><br>
				   	<b>Tipo de Celebraci&oacute;n</b><br>
					<select id="AMTCeb" onclick="ValidaCelebra()">
					    <option value=0></option>
					    ';
					        
    						for($i=0;$i<$numcelebra;$i++){
    							$result = $query->fetch_array();
    									$cadena12="";
    									if($result[0]==$i){
    										$cadena12=" selected";
    									}else{
    										$cadena12="";
    									}		
                                        $Variable.='<option value='.$result[0].$cadena12.'>'.htmlentities($result[1]).'</option>';
    								}
    								$Variable.='
    					</select>
    					<br><b>Nombre del Homenajeado</b><br>
    					<input type="text" id="AMTHome" size=25 disabled>
    					<br>
    					<b>Observaciones</b><br>
    					<input type="text" value="" size="25" id="AMObse" onkeyup=Mayus(this)>
    					<br><br>
    					<input class="btn btn-primary" type="button" value="Aceptar" onclick=AlmacenaRCFora()>		
				</div>
				';
			break;
			case 3:
				$Variable='<br>
				<div>
					<b>OBSERVACIONES</b><br><br>
							<input type="text" value="" size="40" id="AMObse" onkeyup=Mayus(this)>
							<BR><br>
							<button class="btn btn-primary" onclick="AlmacenaRCBloq()">Aceptar</button>
				</div>
				';
			break;
			case 4:
				$Variable='
					<div>
						<b>CANTIDAD DE PERSONAS</b> <span id="cper"><h4>0</h4></span>
						<input type="range" class="form-control-range" min=2 max=8 onchange="control(1)" id="CFSe" Value =2>
						<input type="button" class="btn btn-primary"  value="Aceptar" onclick="AlmacenaRMComparte()">
					</div>
				';
			break;
			default:
				$Variable="Sin Codificar";
			break;
		}
		
		echo $Variable;
