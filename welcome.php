<?php
require_once("auth.php");
require_once("conect.php");
global $conexion;
date_default_timezone_set('America/Bogota');
// el usuario sale del login validado en Querys.php (guard=P), nunca de la URL
if(empty($_SESSION['login_validado'])){
	header("location: index.html");
	exit();
}
$user = $conexion->real_escape_string($_SESSION['login_validado']);
unset($_SESSION['login_validado']);
$fechaHoy = date("Y-m-d H:i:s");

$sql = "SELECT u.*,
                  up.NUMCODPERFIL 
		     FROM tblusuarios u, 
			      tblusuariosperfiles up 
			WHERE u.STRMAIL = '".$user."' 
			  AND u.NUMIDENTLIV = up.NUMIDENTLIV";

$query = $conexion->query($sql);
$result = $query->fetch_array();

$_SESSION['loggedin'] = true;
$_SESSION['inicia'] = time();
$_SESSION['login'] = $user;
$_SESSION['manager'] = true;
$_SESSION['ultimoAcceso'] = date("Y-n-j H:i:s");
$_SESSION['autenticado'] = "Y";
$_SESSION['IdLiving'] = $result[0];
$_SESSION['iniciales'] = substr($result[1], 0,1)."".substr($result[2], 0,1);
$_SESSION['nombre'] = htmlspecialchars_decode($result[1])." ".htmlspecialchars_decode($result[2]);
$_SESSION['usuario'] = "cliente";
$_SESSION['Perfil'] = $result[22];

if(isset($_SESSION["loggedin"])){
	$sql = "UPDATE tblusuarios u 
               SET u.DTMFECHAULTINGRESO = '".$fechaHoy."' 
	         WHERE u.STRMAIL = '".$user."'";
	$query = $conexion->query($sql);
	header("location: main.php");
}else{
	header("location: index.html");
}
?>