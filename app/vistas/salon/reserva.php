<?php
  $CantMesas = ($resultado[4]<10?1:floor($resultado[4]/10));
echo '
<html>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <script language="JavaScript" type="text/javascript" src="js/scripts.js"></script>
    <script src="js/jquery-latest.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <style>
    .onoffswitch {
        position: relative; width: 90px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        display: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "BIEN";
        padding-left: 10px;
        background-color: #0099FF; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "MAL";
        padding-right: 10px;
        background-color: #FF0000; color: #FFFFFF;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 18px; margin: 6px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 56px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }    
</style>
<body><br>
<div class="container">
<div style="border:1px solid black;background:#FFF;padding:10px;">';
echo '
  <div style="border:1px solid black; height:543px;overflow-y:scroll;" id="respuesta">
    <input type="hidden" id="Reserva" value="'.$resultado[0].'">
    <input type="hidden" id="fecha" value="'.$resultado[1].'">
    <input type="hidden" id="Mesa" value="'.$Mesa.'">
    <input type="hidden" id="tipof" value="'.$resultado[12].'">
    <input type="hidden" id="estado" value="'.$resultado[16].'">
    <h2 style="padding-left:10px;"><strong>ASIGNACI&Oacute;N DE MESA(S)</strong></h2>
          <ul style="list-style-type:none;font-size:16pt;">
            <li><b>Reserva No: </b>'.$resultado[0].'</li>
            <li><b>Realizada Por: </b>'.$resultado[2].'</li>
            <li><b>Reserva a Nombre de: </b>'.$resultado[3].'</li>
            <li><b>N&uacute;mero de Personas: </b>'.
            $resultado[4].'</li>
            <li><b>Tipo de Reserva: </b>'.htmlentities($resultado[5]).'</li>
            <li><b>Nombre del Homenajead@: </b>'.$resultado[6].'</li>
            <li><b>Observaciones: </b>'.$resultado[7].'
            </li>
            <li><b>Estado desde: </b>'.$resultado[9].'</li>
            <li>              
            <div id="MesaAs" style="border:1px solid black;width:95%;height:300px;overflow-y:scroll;">';
    echo '
    <form class="form-inline mitad" style="padding:10px;">';
    $Cadena="";
    for($i=0;$i<$CantMesas;$i++){
      $Cadena.='
      <div class="form-group">
              <label class="sr-only">Mesa No. '.($i+1).': </label>
              <p class="form-control-static">Mesa No. '.($i+1).': </p>
            </div>
      <div class="form-group">
        <select id="M'.($i+1).'" class="form-control" onchange="validaIgual('.$CantMesas.')">
          <option value="0">Selecciona una Mesa</option>
            ';
      $Consulta = $mesasLibres->reiniciar();
      $n = $Consulta->num_rows;
      for($j=0;$j<$n;$j++){
        $Res = $Consulta->fetch_array();
        $Cadena.='<option value="'.$Res[0].'">'.$Res[0].' ( '.$Res[1].' )</option>';
      }
      $Cadena.="</select></div><br>";
    }
    $Cadena.='
    <input type="checkbox" value="S" id="respecial"> Reserva Especial<br>
    <button class="btn btn-primary" type="button" id="basigna" onclick="AsignaMesa1('.$CantMesas.')">Asignar Mesa(s)</button>
     </form>';
    echo $Cadena;
              echo '</div>
            </li>';
            
          echo '<br>
            <li>
            <input type="button" class="btn btn-primary" value="Modificar Datos" onClick="GoModificar()">
            <input type="button" class="btn btn-primary" value="Confirma Reserva" onClick="ConfirmaReserva('.$resultado[0].')">
            
            </li>
            <li style="padding-top:10px;">
              <div class="onoffswitch">
            <input type="checkbox" onchange="mesaInamovible()" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
            <label class="onoffswitch-label" for="myonoffswitch">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
            </label>
            </div>
            </li>
        </ul></p>
        
        <div id="resform" class="container" style="width:80%;">
        
        </div> 

        </div>

        <div style="margin-top:2px;">
            <input type="button" class="btn btn-primary" value="Volver" onClick="GoBack()">
            <input type="hidden" id="fecha2" value="'.$Fecha.'">
            <input type="hidden" id="Mesa" value="'.$Mesa.'">
            <input type="hidden" id="Reserva" value="'.$resultado[0].'">
            <input type="hidden" id="MRConse" value="'.$resultado[13].'">
            </div>
            </div>
        ';

echo '</div>
</div>
</body>
</html>  
';
?>
