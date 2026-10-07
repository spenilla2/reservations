<?php
		 $n=$Consulta1->num_rows;
		 if($n==0){
		 	echo '<p class="perror">
		 			Reserva no Existe en la Base de Datos
		 		  </p>';
		 }else{
		 	echo '
		 	<br>
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
	        	<th colspan=2></th>
  			  </tr>';
          for($i=0;$i<$n;$i++){
            $Resu=$Consulta1->fetch_array();
            $FormatFecha=substr($Resu[1], 8,2)."/".substr($Resu[1], 5,2)."/".substr($Resu[1], 0,4);
           echo '
            <tr>
                 <td align="right">'.($i+1).'</td>
              	 <td align="left">'.$Resu[3].'</td>
              	 <td align="right">'.$Resu[0].'</td>
              	 <td align="right">'.$Resu[4].'</td>
              	 <td align="center">'.htmlentities($Resu[14]).'</td>
              	 <td align="right">'.$FormatFecha.'</td>
              	 <td>'.htmlentities($Resu[15]).'</td>
                 <td>'.$Resu[11].'</td>
                 <td align="center">
              				<a href="Querys.php?guard=E&Reserva='.$Resu[0].'">
                        <img src="images/drop.png" width="22px" height="22px" title="Cancelar Reserva">
                      </a>
              	 </td>
                 <td align="center">';
                  echo '<a href="reserva.php?fecha='.$FormatFecha.'&Tipo=U&Rnum='.$Resu[0].'&MA='.$Resu[11].'" rel="gb_page_center[600, 460]" title="Modificar Reserva ('.$FormatFecha.')">
                       <img src="images/edit.png" width="22px" height="22px" title="Modificar Reserva">
                      </a>
              	 </td>
            </tr>';
          }
        echo '
	        </table>

		 	';
		} 
