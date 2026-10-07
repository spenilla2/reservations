<?php
// $resultado: 'ocupada', 'asignada' o 'error'
if($resultado=='ocupada'){
	echo '<p class="perror">Error E8: Lo siento la Mesa '.$Mesa.' Ya se encuentra asignada!';
}elseif($resultado=='asignada'){
	echo '<p class="pbien">MESA ASIGNADA SATISFACTORIAMENTE</p><input type="button" value="Volver" onClick="GoBack()">';
}else{
	echo '<p class="perror">Error E7</p>';
}
