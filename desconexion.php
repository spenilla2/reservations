<?php
session_start();
session_unset();
session_destroy();
?>
	  <script>
	  		alert("Sesion desconectada Satisfactoriamente!");
	  		location.href="index.html";
	  </script>
