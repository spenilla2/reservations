<?php
// $levantadas: resultado de marcar las reservas; $mesasLiberadas: resultado de liberar sus mesas
if($levantadas && $mesasLiberadas){
	$Mensaje='<p style="color:#008040;font-weight:bold;">
					Procedimiento de levantamiento de reservas ejecutado satisfactoriamente!
			</p>';
}else{
	$Mensaje='<p style="color:#FF0000;font-weight:bold;">
					Ha Ocurrido un error! '.$levantadas.'
			</p>';
}
echo $Mensaje;
