<?php
$n = $query->num_rows;
         $tabla='<table width="100%" border=1>
                     <tr>                     
                        <th>No.</th>
                        <th>Nombre de Reserva</th>
                        <th>Pers.</th>
                        <th>Motivo</th>                        
                     </tr>';
                  for($i=0;$i<$n;$i++){
                    $Resu = $query->fetch_array();
                    $FormatFecha=substr($Resu[1], 8,2)."/".substr($Resu[1], 5,2)."/".substr($Resu[1], 0,4);
                   $tabla.='<tr>
                                 <td align="right">
                                    '.($i+1).'
                                 </td>
                                 <td align="left" style="padding-left:10px;">
                                    '. mb_strtoupper((html_entity_decode(utf8_encode($Resu[3]))),'UTF-8').'&nbsp;&nbsp;&nbsp;';
                                    if($Resu[11]!=""){
                                    $tabla.=" Mesas: (".$Resu[11].")";
                                 }else{
                                    $queryMlibre = $mesasLibres->reiniciar();
                                    $nMesasLibres = $queryMlibre->num_rows;
                       
                                    $tabla.= '<select class="form-control" id="mesasDisp'.$i.'" onchange="asignaMesaLast('.$Resu[0].', '.$i.')">
                                                            <option value=0></option>';
                                                                for($j=0;$j<$nMesasLibres;$j++){
                                                                    $Res = $queryMlibre->fetch_array();
                                                                    $tabla.= '<option value="'.$Res[0].'">'.$Res[0].' ( '.$Res[1].' )</option>';
                                                                }
                                    $tabla.='</select>';                                   
                                 }   
                                  $tabla.='
                                 </td>
                                 <td align="right">
                                    '.$Resu[4].'
                                 </td>
                                 <td align="center">
                                    '.utf8_encode($Resu[14]).'
                                 </td>
                             </tr>';
                }
        $tabla.='
            </table>

            ';
?>
<!doctype html>
<html lang="es">
<head>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>Living Night Club</title>
        <meta name="description" content="" />
        <meta name="author" content="Santiago Penilla" />
        <link rel="shortcut icon" href="imagenes/favicon.ico">
        <link rel="stylesheet" type="text/css" href="css/bootstrap5-modal.css">
        <link rel="stylesheet" type="text/css" href="css/estilo_salon.css">

</head>
<style type="text/css">
.btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
      touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}
.btn:focus,
.btn:active:focus,
.btn.active:focus,
.btn.focus,
.btn:active.focus,
.btn.active.focus {
  outline: 5px auto -webkit-focus-ring-color;
  outline-offset: -2px;
}
.btn:hover,
.btn:focus,
.btn.focus {
  color: #333;
  text-decoration: none;
}
.btn:active,
.btn.active {
  background-image: none;
  outline: 0;
  -webkit-box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
          box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
}
.btn.disabled,
.btn[disabled],
fieldset[disabled] .btn {
  cursor: not-allowed;
  filter: alpha(opacity=65);
  -webkit-box-shadow: none;
          box-shadow: none;
  opacity: .65;
}
a.btn.disabled,
fieldset[disabled] a.btn {
  pointer-events: none;
}

.btn-primary {
  color: #fff;
  background-color: #337ab7;
  border-color: #2e6da4;
}
.btn-primary:focus,
.btn-primary.focus {
  color: #fff;
  background-color: #286090;
  border-color: #122b40;
}
.btn-primary:hover {
  color: #fff;
  background-color: #286090;
  border-color: #204d74;
}
.btn-primary:active,
.btn-primary.active,
.open > .dropdown-toggle.btn-primary {
  color: #fff;
  background-color: #286090;
  border-color: #204d74;
}
.btn-primary:active:hover,
.btn-primary.active:hover,
.open > .dropdown-toggle.btn-primary:hover,
.btn-primary:active:focus,
.btn-primary.active:focus,
.open > .dropdown-toggle.btn-primary:focus,
.btn-primary:active.focus,
.btn-primary.active.focus,
.open > .dropdown-toggle.btn-primary.focus {
  color: #fff;
  background-color: #204d74;
  border-color: #122b40;
}
.btn-primary:active,
.btn-primary.active,
.open > .dropdown-toggle.btn-primary {
  background-image: none;
}
.btn-primary.disabled:hover,
.btn-primary[disabled]:hover,
fieldset[disabled] .btn-primary:hover,
.btn-primary.disabled:focus,
.btn-primary[disabled]:focus,
fieldset[disabled] .btn-primary:focus,
.btn-primary.disabled.focus,
.btn-primary[disabled].focus,
fieldset[disabled] .btn-primary.focus {
  background-color: #337ab7;
  border-color: #2e6da4;
}
.btn-primary .badge {
  color: #337ab7;
  background-color: #fff;
}
#listado{
    width:100%;
    border:1px solid black;
    height: 686px;
}
#tabla3 {
    display: table;
    border: 0px solid #000;
    width: 80%;
    text-align: center;
    margin: 0 auto;
    border-spacing: 30px 1px;
}
#tabla4 {
    display: table;
    border: 0px solid #000;
    width: 95%;
    text-align: center;
    margin: 0 auto;
    border-spacing: 5px 0px;
}
#fila3 {
    display: table-row;
    height: 40px;
}
.col3c{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 5px;
    width:60px;     
}
.col3d{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    width:60px;
    margin-right: 100px;
}

#tabla2{
    display: table;
    border: 0px solid #000;
    width: 70%;
    text-align: center;
    margin: 0 auto;
    border-spacing: 5px 5px;
}
#fila2 {
    display: table-row;
    height: 110px;
}
#fila2c {
    display: table-row;
    height: 60px;
}
.col2c{
    display: table-cell;
    border: 0px solid #000;
    vertical-align: middle;
    padding: 5px;
    height:10px;
}
.col2a{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 10px;
    margin: 10px;
}
.col2b{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 5px;
}
#tabla1 {
    display: table;
    border: 0px solid #000;
    width: 100%;
    text-align: center;
    margin: 0 auto;
}
#fila1 {
    display: table-row;
    height: 40px;
}
.col1c{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 5px;
    width:60px;
}
.col1{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 5px;
}
.col2{
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 5px;
}

#tabla {
    display: table;
    border: 0px solid #000;
    width: 43px;
    text-align: center;
    height: 970px;
    border-spacing: 5px 5px;
}
#fila {
    display: table-row;
    height: 99px;
}
#filad {
    display: table-row;
    height: 90px;
}
#filapi {
    display: table-row;
    height: 50px;
}
.col {
    display: table-cell;
    border: 1px solid #000;
    vertical-align: middle;
    padding: 10px;

}

/* Tema oscuro del tablero: fondo negro, mesas y textos en blanco.
   El color de las mesas ocupadas lo pone pintar() en scripts.js. */
html {
    /* barras de desplazamiento y listas desplegables del navegador en version oscura */
    color-scheme: dark;
}
body {
    background-color: #000;
    color: #fff;
}
.mesa {
    border-color: #fff;
    color: #fff;
    cursor: pointer;
}
.mesa.oculta {
    visibility: hidden;
}
/* Logo de fondo en el listado central, atenuado para que las reservas se lean encima */
#listado {
    scrollbar-color: #666 #000;
    background:
        /* se funde a negro hacia los bordes para que no se vea el recuadro de la imagen */
        radial-gradient(circle at center, rgba(0, 0, 0, 0.6) 0, rgba(0, 0, 0, 0.6) 105px, #000 162px),
        url(images/logo_prin.jpg) center center / 320px 320px no-repeat,
        #000;
}
#listado,
#listado table,
#listado th,
#listado td {
    border-color: #fff;
}

/* Ventana de gestion de una mesa: componente modal de Bootstrap 5 en negro, con un halo gris que la separa del tablero */
#modalMesa .modal-content {
    background-color: #000;
    color: #fff;
    border: 1px solid #8a8a8a;
    border-radius: 14px;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.08), 0 0 45px 12px rgba(170, 170, 170, 0.55);
}
#modalMesa .modal-header {
    border-bottom: 1px solid #555;
}
#modalMesa .modal-body {
    padding: 0;
}
#modalMesa iframe {
    display: block;
    width: 100%;
    height: min(78vh, 720px);
    border: 0;
    border-radius: 0 0 14px 14px;
}
</style>

<script src="js/bootstrap5.bundle.min.js"></script>
<script src="js/scripts.js"></script>

<body onload="pintarSalon()">
    <div class="modal fade" id="modalMesa" tabindex="-1" aria-labelledby="tituloModalMesa" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalMesa">Gesti&oacute;n de la mesa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <iframe id="marcoMesa" title="Gesti&oacute;n de la mesa"></iframe>
                </div>
            </div>
        </div>
    </div>
    <script>
        // El tablero se recarga cada 140 segundos, salvo mientras hay una mesa abierta en la ventana.
        setInterval(function(){
            if(!document.getElementById("modalMesa").classList.contains("show")){
                location.reload();
            }
        }, 140000);
    </script>
    <input type="hidden" value="<?php echo $fecha;?>" id="fechadash">
    <table align="center">
        <tr>
            <td colspan="8" align="center"><p style="font-size: 22px;"><?php echo $fecha;?></p></td>
        </tr>
        <tr>
            <td colspan="8">
                <table width="100%" align="center">
                    <tr>
                    <td align="left">
                        <button class="btn btn-primary" onclick="LanzaURL1(1)">
                            Atras
                        </button>
                    </td>
                    <td align="center"><button class="btn btn-primary" onclick="LanzaURL1(2)">
                            Listar Cumplea&ntilde;os
                        </button>
                        <button class="btn btn-primary" onclick="LanzaURL1(3)">
                            Listar Grados
                        </button>    
                    </td>
                    
                    <td align="right">
                        <button class="btn btn-primary" onclick="LanzaURL1(1)">
                            Atras
                        </button>
                    </td>
                </tr>
            </table>
            </td>
        </tr>
        <tr>
            <td>
                <button class="btn btn-primary" onclick="pintarSalon()">Estado Actual</button>
            </td>
            <td>
                <button class="btn btn-primary" onclick="Vacias()">Vacias</button>
            </td>
            <td>
                <button class="btn btn-primary" onclick="TipoRes(1)">Reservas Especiales</button>
            </td>
            <td>
                <button class="btn btn-primary" onclick="TipoRes(2)">Reservas Confirmadas</button>
            </td>
            <td>
                <button class="btn btn-primary" onclick="TipoRes(3)">Reservas Pendientes</button>
            </td>
            <td>
                <button class="btn btn-primary" onclick="TipoRes(4)">Mesas Compartidas</button>
            </td>
            
            <td>
                <button class="btn btn-primary" onclick="TipoRes(5)">Grupo Inamovible</button>
            </td>
            <td>
                <button class="btn btn-primary" onclick="TipoRes(6)">Cumplea&ntilde;os</button>
            </td>
            <td></td>

        </tr>
    </table>
    <table border="0" width="100%">
        <tr>
            <td width="5%">
                <div id="tabla">
                    <div id="filapi">
                        <div class="col mesa" id="8" onclick="lanzaURL(8)">8</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="9" onclick="lanzaURL(9)">9</div>
                    </div>
                    
                    <div id="fila">
                        <div class="col mesa" id="10" onclick="lanzaURL(10)">10</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="11" onclick="lanzaURL(11)">11</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="12" onclick="lanzaURL(12)">12</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="13" onclick="lanzaURL(13)">13</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="14" onclick="lanzaURL(14)">14</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="15" onclick="lanzaURL(15)">15</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="16" onclick="lanzaURL(16)">16</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="17" onclick="lanzaURL(17)">17</div>
                    </div>
                    <div id="fila">
                        <div class="col mesa" id="18" onclick="lanzaURL(18)">18</div>
                    </div>
                </div>

            </td>
            <td>
                <table width="100%" border="0">
                    <tr>
                     <td>
                        <table width="100%" border="0" >
                            <tr>
                                <td width="10%">
                                        <div id="tabla2">
                                            <div id="fila2">
                                                <div class="col2a mesa" id="88" onclick="lanzaURL(88)">88</div>
                                                <div class="col2a mesa" id="89" onclick="lanzaURL(89)">89</div>
                                            </div>
                                        </div>
                                        <br>
                                        <div id="tabla2">
                                            <div id="fila2">
                                                <div class="col2a mesa" id="87" onclick="lanzaURL(87)">87</div>
                                                <div class="col2a mesa" id="86" onclick="lanzaURL(86)">86</div>
                                            </div>
                                            <div id="fila2">
                                                <div class="col2a mesa" id="80" onclick="lanzaURL(80)">80</div>
                                                <div class="col2a mesa" id="81" onclick="lanzaURL(81)">81</div>
                                            </div>
                                        </div>
                                         <br>
                                        <div id="tabla2">
                                            <div id="fila2">
                                                <div class="col2a mesa" id="79" onclick="lanzaURL(79)">79</div>
                                                <div class="col2a mesa" id="78" onclick="lanzaURL(78)">78</div>
                                            </div>
                                            <div id="fila2c">
                                                <div class="col2a mesa" id="68" onclick="lanzaURL(68)">68</div>
                                                <div class="col2a mesa" id="69" onclick="lanzaURL(69)">69</div>
                                            </div>
                                        </div>

                                </td>
                                <td>
                                    <div id="listado" style="overflow-y:scroll;">
                                    <?php echo $tabla;?>
                                    </div>
                                    <div id="tabla3" width="10%">
                                            <div id="fila3">
                                                <div class="col3d mesa" id="70" onclick="lanzaURL(70)">70</div>
                                                <div class="col3c mesa" id="71" onclick="lanzaURL(71)">71</div>
                                                <div class="col3c mesa" id="72" onclick="lanzaURL(72)">72</div>
                                                <div class="col3c mesa" id="73" onclick="lanzaURL(73)">73</div>
                                            </div>
                                        </div>
                                </td>
                                <td width="10%">
                                    <div id="tabla2">
                                            <div id="fila2">
                                                <div class="col2a mesa" id="90" onclick="lanzaURL(90)">90</div>
                                                <div class="col2a mesa" id="91" onclick="lanzaURL(91)">91</div>
                                            </div>
                                        </div>
                                        <br>
                                        <div id="tabla2">
                                            <div id="fila2">
                                                <div class="col2a mesa" id="85" onclick="lanzaURL(85)">85</div>
                                                <div class="col2a mesa" id="84" onclick="lanzaURL(84)">84</div>
                                            </div>
                                            <div id="fila2">
                                                <div class="col2a mesa" id="82" onclick="lanzaURL(82)">82</div>
                                                <div class="col2a mesa" id="83" onclick="lanzaURL(83)">83</div>
                                            </div>
                                        </div>
                                         <br>
                                        <div id="tabla2">
                                            <div id="fila2">
                                                <div class="col2a mesa" id="77" onclick="lanzaURL(77)">77</div>
                                                <div class="col2a mesa" id="76" onclick="lanzaURL(76)">76</div>
                                            </div>
                                            <div id="fila2c">
                                                <div class="col2a mesa" id="74" onclick="lanzaURL(74)">74</div>
                                                <div class="col2a mesa" id="75" onclick="lanzaURL(75)">75</div>
                                            </div>
                                        </div>
                                </td>
                            </tr>
                        </table>
                     </td>   
                    </tr>
                    
                    <tr>
                        <td>
                            <table width="100%" cellspacing ="12">
                                <tr>
                                    <td width="35%">
                                        <div id="tabla1">
                                            <div id="fila1">
                                                <div class="col1 mesa" id="67" onclick="lanzaURL(67)">67</div>
                                                <div class="col2 mesa" id="66" onclick="lanzaURL(66)">66</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="56" onclick="lanzaURL(56)">56</div>
                                                <div class="col2 mesa" id="57" onclick="lanzaURL(57)">57</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa"id="55" onclick="lanzaURL(55)">55</div>
                                                <div class="col2 mesa" id="54" onclick="lanzaURL(54)">54</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="44" onclick="lanzaURL(44)">44</div>
                                                <div class="col2 mesa" id="45" onclick="lanzaURL(45)">45</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="43" onclick="lanzaURL(43)">43</div>
                                                <div class="col2 mesa" id="42" onclick="lanzaURL(42)">42</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="32" onclick="lanzaURL(32)">32</div>
                                                <div class="col2 mesa" id="33" onclick="lanzaURL(33)">33</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="15%">
                                        <div id="tabla1">
                                            <div id="fila1">
                                                <div class="col1c mesa" id="65" onclick="lanzaURL(65)">65</div>
                                                <div class="col1c mesa" id="64" onclick="lanzaURL(64)">64</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1c mesa" id="58" onclick="lanzaURL(58)">58</div>
                                                <div class="col1c mesa" id="59" onclick="lanzaURL(59)">59</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1c mesa" id="53" onclick="lanzaURL(53)">53</div>                                                
                                                <div class="col1c mesa" id="52" onclick="lanzaURL(52)">52</div>
                                            </div>
                                            <div id="fila1">
                                                
                                                <div class="col1c mesa" id="46" onclick="lanzaURL(46)">46</div>                                                
                                                <div class="col1c mesa" id="47" onclick="lanzaURL(47)">47</div>                                                
                                            </div>
                                            <div id="fila1">
                                                <div class="col1c mesa" id="41" onclick="lanzaURL(41)">41</div>
                                                <div class="col1c mesa" id="40" onclick="lanzaURL(40)">40</div>                                                
                                            </div>
                                            <div id="fila1">
                                                <div class="col1c mesa" id="34" onclick="lanzaURL(34)">34</div>                                                
                                                <div class="col1c mesa" id="35" onclick="lanzaURL(35)">35</div>                                                
                                            </div>
                                        </div>
                                    </td>
                                    <td width="35%">
                                        <div id="tabla1">
                                            <div id="fila1">
                                                <div class="col1 mesa" id="63" onclick="lanzaURL(63)">63</div>
                                                <div class="col2 mesa" id="62" onclick="lanzaURL(62)">62</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="60" onclick="lanzaURL(60)">60</div>
                                                <div class="col2 mesa" id="61" onclick="lanzaURL(61)">61</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="51" onclick="lanzaURL(51)">51</div>
                                                <div class="col2 mesa" id="50" onclick="lanzaURL(50)">50</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="48" onclick="lanzaURL(48)">48</div>
                                                <div class="col2 mesa" id="49" onclick="lanzaURL(49)">49</div>
                                            </div>
                                            <div id="fila1">
                                                <div class="col2 mesa" id="39" onclick="lanzaURL(39)">39</div>
                                                <div class="col1 mesa" id="38" onclick="lanzaURL(38)">38</div>
                                                
                                            </div>
                                            <div id="fila1">
                                                <div class="col1 mesa" id="36" onclick="lanzaURL(36)">36</div>
                                                <div class="col2 mesa" id="37" onclick="lanzaURL(37)">37</div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </td>
            <td width="5%" align="right">
                <div id="tabla">
                    <div id="filad">
                        <div class="col mesa" id="31" onclick="lanzaURL(31)">31</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="30" onclick="lanzaURL(30)">30</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="29" onclick="lanzaURL(29)">29</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="28" onclick="lanzaURL(28)">28</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="27" onclick="lanzaURL(27)">27</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="26" onclick="lanzaURL(26)">26</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="25" onclick="lanzaURL(25)">25</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="24" onclick="lanzaURL(24)">24</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="23" onclick="lanzaURL(23)">23</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="22" onclick="lanzaURL(22)">22</div>
                    </div>
                    <div id="filad">
                        <div class="col mesa" id="21" onclick="lanzaURL(21)">21</div>
                    </div>
                    
            </div>


            </td>
        </tr>
        <tr>
            <td valign="top">
                <div id="tabla4" style="border-spacing: 5px 5px; margin-top: -5px;">
                    <div id="fila3">
                        <div class="col3c mesa" id="19" onclick="lanzaURL(19)">19</div>
                    </div>
                    <div id="fila3">
                        <div class="col3c mesa" id="20" onclick="lanzaURL(20)">20</div>
                    </div>
                </div>
            </td>
            <td valign="top">
                <div id="tabla4" >
                    <div id="fila3">
                        <div class="col3c mesa" id="B01" onclick="lanzaURL(101)">B01</div>
                        <div class="col3c mesa" id="B02" onclick="lanzaURL(102)">B02</div>
                        <div class="col3c mesa" id="B03" onclick="lanzaURL(103)">B03</div>
                        <div class="col3c mesa" id="B04" onclick="lanzaURL(104)">B04</div>
                        <div class="col3c mesa" id="B05" onclick="lanzaURL(105)">B05</div>
                        <div class="col3c mesa" id="B06" onclick="lanzaURL(106)">B06</div>
                        <div class="col3c mesa" id="B07" onclick="lanzaURL(107)">B07</div>
                        <div class="col3c mesa" id="B08" onclick="lanzaURL(108)">B08</div>
                        <div class="col3c mesa" id="B09" onclick="lanzaURL(109)">B09</div>
                        <div class="col3c mesa" id="B10" onclick="lanzaURL(110)">B10</div>
                        <div class="col3c mesa" id="B11" onclick="lanzaURL(111)">B11</div>
                        <div class="col3c mesa" id="B12" onclick="lanzaURL(112)">B12</div>
                        <div class="col3c mesa" id="B13" onclick="lanzaURL(113)">B13</div>
                        <div class="col3c mesa" id="B14" onclick="lanzaURL(114)">B14</div>
                        <div class="col3c mesa" id="B15" onclick="lanzaURL(115)">B15</div>
                        <div class="col3c mesa" id="B16" onclick="lanzaURL(116)">B16</div>                        
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3">
                <br>
                <div id="tabla4" >
                    <div id="fila3">
                        <div class="col3c mesa" id="P1" onclick="lanzaURL(201)">P1</div>
                        <div class="col3c mesa" id="P2" onclick="lanzaURL(202)">P2</div>
                        <div class="col3c mesa" id="P3" onclick="lanzaURL(203)">P3</div>
                        <div class="col3c mesa" id="P4" onclick="lanzaURL(204)">P4</div>
                        <div class="col3c mesa" id="P5" onclick="lanzaURL(205)">P5</div>
                        <div class="col3c mesa" id="P6" onclick="lanzaURL(206)">P6</div>
                        <div class="col3c mesa" id="P7" onclick="lanzaURL(207)">P7</div>
                    </div>
                </div>
            </td>
        </tr>


    </table>
</body>
</html>