<?php
		$Mensaje="";
		if($query){
			$Mensaje='<ul><p class="pbien">Mesas Asignadas Satisfactoriamente!</p></ul>';
		}else{
			$Mensaje="Error";
		}
		echo $Mensaje;
