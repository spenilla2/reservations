<?php
		$n = $Consulta->num_rows;
		$html="";
		if($n==0){
			$html='
				<p style="color:#FF0000;font-weight:bold;">Reserva no Existe en la Base de Datos</p>
			';
		}else{
			$resultado=$Consulta->fetch_array();
			if($resultado[10]==""){
				$html='<p class="perror">
						La reserva aun no se le a asignado una Mesa
					   </p>';
			}else{
				$html='
			  		<b>Fecha de Reserva : </b>&nbsp;'.$resultado[1].'<br>
			  		<b>Realizada Por : </b>&nbsp;'.$resultado[2].'<br>
			  		<b>Reserva a Nombre de : </b>&nbsp;'.$resultado[3].'<br>
			  		<b>N&uacute;mero de Personas : </b>&nbsp;'.$resultado[4].'<br>
			  		<b>Tipo de Reserva : </b>&nbsp;'.htmlentities($resultado[5]).'<br>
			  		<b>Nombre del Homenajead@ : </b>&nbsp;'.$resultado[6].'<br>
			  		<b>Observaciones : </b>
			  			<br>'.$resultado[7].'<br>
			  		<b>Mesa(s) Asignada(s) : </b>&nbsp;'.$resultado[10].'<br>
			  		<b>Estado de la Reserva : </b>&nbsp;'.$resultado[8].'<br>
			  		<b>Reserva Realizada : </b>&nbsp;'.$resultado[9].'<br>
			  		<b>Reserva No : </b>&nbsp;'.$resultado[0].'<br>';
			  if($resultado[8]=="ACTIVA"){
			  	$html.='<br>
			  			<input type="button"  class="btn btn-primary" value="Confirmar Reserva" onclick="ConfirmaReserva('.$resultado[0].')">';
			  }else{
			  	$html.='';
			  }	
			}
			
		}
		
		echo $html;
