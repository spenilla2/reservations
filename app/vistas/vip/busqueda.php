<?php
		$n=$Consulta->num_rows;			
		$VarConsulta='
		<table class="tablaconsulta" width="100%">
							<tr>
								<td id="tittabla" width="2%">No.</td>
								<td id="tittabla">NOMBRE</td>
								<td id="tittabla">TEL&Eacute;FONO</td>
								<td id="tittabla">TIPO DE RUMBA</td>
								<td id="tittabla" colspan=2 width="5%">GESTI&Oacute;N</td>
							</tr>';
				for($i=0;$i<$n;$i++){
		  			$res=$Consulta->fetch_array();
		  			  $VarConsulta.= '<tr>
		  						<td id="tittabla">'.($i+1).'</td>
					  			<td>'.htmlentities($res[1]).'</td>
					  			<td>';
					  			if($res[3]==$IdLiving){
					  				$VarConsulta.=$res[5];
					  				
					  			}else{
					  				$VarConsulta.='';
					  				
					  			}
					  $VarConsulta.='</td>
					  			<td>'.$res[8].'</td>
					  			<td>
					  				<a href="GestionCVP.php?idCliVip='.$res[0].'">
			                       		<img src="images/edit.png" width="20px" height="20px" title="Modificar Cliente">
			                      	</a>
			                    </td>
					  			<td>
					  				<img src="images/drop.png" width="22px" height="22px" title="Cancelar Cliente">
					  			</td>
					  		</tr>
				 ';
				}
				$VarConsulta.="</table>";			
		echo "".$VarConsulta;
