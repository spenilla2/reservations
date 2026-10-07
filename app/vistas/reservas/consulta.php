<?php
echo '
<div class="container">';
if($Perfil==1){
  echo '<div style="background-color:white; padding:1em;border-radius: 0.5em 0.5em 0.5em 0.5em;-moz-border-radius: 0.5em 0.5em 0.5em 0.5em;-webkit-border-radius: 0.5em 0.5em 0.5em 0.5em;">
      <h3><strong>Consultar Reservas</strong></h3><br>
      <form class="form-inline">
            <div class="form-group">
              <label class="sr-only">Fecha de Reserva:</label>
              <p class="form-control-static"><strong>Digite el N&uacute;mero de la Reserva : </strong></p>
            </div>
            <div class="form-group">
              <input type="number" id="Rese" class="form-control" value="" size=5 placeholder="# Reserva">
            </div>
            <button type="button" class="btn btn-primary" onclick="BuscarReserva()"><img src="images/search.png" ></button>
        </form>
';
 
}else{

}
echo '
<div class="table-responsive" id="Respuesta" style="background-color:white;padding:1em;">
	        <table class="table table-hover table-bordered" width="100%">
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
            $Resu = $query->fetch_array();
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
                  echo '
                    <a href="reserva.php?fecha='.$FormatFecha.'&Tipo=U&Rnum='.$Resu[0].'&MA='.$Resu[11].'">
                       <img src="images/edit.png" width="22px" height="22px" title="Modificar Reserva">
                      </a>
              	 </td>
            </tr>';
          }
        echo '
	        </table>
          <br><br><br>
	   </div></div></div>
      <br><br><br>
     ';
