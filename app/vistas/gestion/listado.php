<?php
		 if($n==0){
		 	echo '<div class="col-md-12">
				<div class="alert alert-warning">
					<button class="close" data-dismiss="alert">
						<span class="glyphicon glyphicon-remove-circle">
						</span>
					</button>
					<strong>Alerta:</strong> No existen Reservas para este d&iacute;a: '.$Fecha.'
				</div>
			</div>';
		 }else{
		 	echo '
		 	<br>
		 	<b>Total Reservas: </b>'.$Resu3[0].' 
		 	<br><b>Cantidad de Personas Esperadas: </b>'.$Resu3[1].'
		 	<table class="table table-hover table-bordered">
	         <tr>
                <th>No.</th>
                <th>Nombre de Reserva</th>
                <th>Reserva</th>
	        	<th>Pers.</th>
	        	<th>Motivo</th>
	        	<th>Fecha de Reserva</th>
	        	<th>Estado</th>
                <th>Mesas</th>
                <th>Aplica Cortesia</th>
                <th>Realizada por</th>
  			  </tr>';
          for($i=0;$i<$n;$i++){
            $Resu = $Consulta1->fetch_array();
			
            $FormatFecha=substr($Resu[1], 8,2)."/".substr($Resu[1], 5,2)."/".substr($Resu[1], 0,4);
           echo '
            <tr>
                 <td align="right">'.($i+1).'</td>
              	 <td align="left">'. mb_strtoupper((html_entity_decode(utf8_encode($Resu[3]))),'UTF-8').'&nbsp;&nbsp;&nbsp;<a href="Querys.php?guard=E&Reserva='.$Resu[0].'">
                        <img src="images/drop.png" width="22px" height="22px" title="Cancelar Reserva">
                      </a>&nbsp;&nbsp;
                      <a href="reserva.php?fecha='.$FormatFecha.'&Tipo=U&Rnum='.$Resu[0].'&MA='.$Resu[11].'" rel="gb_page_center[600, 460]" title="Modificar Reserva ('.$FormatFecha.')">
                       <img src="images/edit.png" width="22px" height="22px" title="Modificar Reserva">
                      </a>
                 </td>
              	 <td align="right">'.$Resu[0].'</td>
              	 <td align="right">'.$Resu[4].'</td>
              	 <td align="center">'.utf8_encode($Resu[14]).'</td>
              	 <td align="right">'.$FormatFecha.'</td>
              	 <td>'.utf8_encode($Resu[15]).'</td>
                 <td>'.$Resu[11].'</td>
                 <td>'.$Resu[13].'</td>
                 <td>'.substr($Resu[16], 0,strpos($Resu[16],'@')).'</td>
            </tr>';
          }
        echo '
	        </table>

		 	';
		} 
