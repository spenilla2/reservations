<?php
// $confirmado: el usuario acepto guardar; $creado: resultado del alta; $name: nombre del cliente
if($confirmado){
	if($creado){
		echo '<script>alert("Cliente VIP '.$name.' fue Creado Satisfactoriamente!");</script>';
	}else{
		echo '<script>alert("Ha Ocurrido un error al momento de insertar la Informacion en la Base de Datos");</script>';
	}
}
echo '<script>
					location.href="ClientVIP.php";
				  </script>';
