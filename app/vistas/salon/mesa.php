<?php
// Gestion de una mesa en una fecha. Se muestra dentro de la ventana modal del tablero.
//   $Mesa, $Fecha, $infoMesa (fila de tblmesas o null)
//   $nfilasMreservada > 0 si esta ocupada; entonces $resultado trae la reserva (ver Reserva::detalleEnMesa)
//   $queryMlibre / $nMesasLibres: mesas libres de la fecha, para trasladar
// Los id de los campos son los que leen las funciones de js/scripts.js; no cambiarlos.
$ocupada = $nfilasMreservada > 0;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa <?php echo $Mesa;?></title>
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="css/salon_oscuro.css" rel="stylesheet" type="text/css">
    <link href="css/mesa.css" rel="stylesheet" type="text/css">
    <script src="js/scripts.js"></script>
</head>
<body>
    <header class="mesa-cabecera">
        <h1>Mesa <?php echo $Mesa;?></h1>
<?php if ($ocupada) { ?>
        <span class="insignia ocupada"><?php echo htmlentities($resultado[11]);?></span>
<?php } else { ?>
        <span class="insignia libre">Libre</span>
<?php } ?>
<?php if ($infoMesa) { ?>
        <span class="mesa-dato"><?php echo htmlentities($infoMesa[2]);?> &middot; capacidad <?php echo htmlentities($infoMesa[1]);?> personas</span>
<?php } ?>
    </header>

<?php if ($ocupada) {
    // "Llega cupo completo" solo aplica a cumpleanos
    $esCumple = $resultado[14] == 1;
    $cupoCompleto = $esCumple && $resultado[15] == "S";
?>
    <input type="hidden" id="Reserva" value="<?php echo $resultado[0];?>">
    <input type="hidden" id="fecha" value="<?php echo $resultado[1];?>">
    <input type="hidden" id="fecha2" value="<?php echo $Fecha;?>">
    <input type="hidden" id="Mesa" value="<?php echo $Mesa;?>">
    <input type="hidden" id="tipof" value="<?php echo $resultado[12];?>">
    <input type="hidden" id="mesas" value="<?php echo $resultado[10];?>">
    <input type="hidden" id="MRConse" value="<?php echo $resultado[13];?>">

    <div id="respuesta">
        <dl class="ficha">
            <dt>Reserva No.</dt>
            <dd><?php echo $resultado[0];?></dd>
            <dt>Estado</dt>
            <dd><?php echo $resultado[8];?> <span class="mesa-dato">desde <?php echo $resultado[9];?></span></dd>

            <dt>A nombre de</dt>
            <dd><?php echo $resultado[3];?></dd>
            <dt>Realizada por</dt>
            <dd><?php echo $resultado[2];?></dd>

            <dt>Personas</dt>
            <dd>
<?php if ($resultado[12] == "K") { ?>
                <select id="ActCantPer">
<?php for ($i = 1; $i <= 10; $i++) { ?>
                    <option value="<?php echo $i;?>"<?php echo $i == $resultado[4] ? ' selected' : '';?>><?php echo $i;?></option>
<?php } ?>
                </select>
                <input type="button" value="Actualizar" onClick="ActMCompartida()">
<?php } else { ?>
                <?php echo $resultado[4];?>
<?php } ?>
            </dd>
            <dt>Tipo de reserva</dt>
            <dd><?php echo htmlentities($resultado[5]);?></dd>

            <dt>Homenajead@</dt>
            <dd><?php echo $resultado[6] != "" ? $resultado[6] : "&mdash;";?></dd>
            <dt>Mesa(s)</dt>
            <dd><?php echo $resultado[10];?></dd>

            <dt>Observaciones</dt>
            <dd class="ancho"><?php echo $resultado[7] != "" ? $resultado[7] : "&mdash;";?></dd>

            <dt>Reserva realizada</dt>
            <dd class="ancho"><?php echo $resultado[16];?></dd>
<?php if ($resultado[18] == 1 && $resultado[17] == 3) { ?>

            <dt>&iquest;Llega cupo completo?</dt>
            <dd class="ancho">
                <select id="cumpli"<?php echo $esCumple ? '' : ' disabled';?>>
                    <option value="S"<?php echo $cupoCompleto ? ' selected' : '';?>>Si</option>
                    <option value="N"<?php echo $cupoCompleto ? '' : ' selected';?>>No</option>
                </select>
                <input type="button" class="btn btn-primary btn-sm" value="Actualizar" onClick="EntrMani()">
            </dd>
<?php } ?>
        </dl>

        <div class="ajustes">
            <input type="button" class="btn btn-secundario" value="Modificar datos" onClick="GoModificar()">
            <div class="onoffswitch">
                <input type="checkbox" onchange="mesaInamovible()" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"<?php echo strlen($resultado[12]) > 1 ? '' : ' checked';?>>
                <label class="onoffswitch-label" for="myonoffswitch">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div>
        <div id="resform"></div>
    </div>

    <div class="acciones">
<?php if ($resultado[12] == 'N' || $resultado[12] == 'E') { ?>
        <input type="button" class="btn btn-primary" value="Confirmar reserva" onClick="ConfirmaReserva(<?php echo $resultado[0];?>)">
<?php } ?>
        <input type="button" class="btn btn-peligro" value="Liberar mesa" onClick="LiberaMesa()">
        <span class="traslado">
            <label for="traslada">Trasladar a</label>
            <select id="traslada">
                <option value=0></option>
<?php for ($i = 0; $i < $nMesasLibres; $i++) { $Res = $queryMlibre->fetch_array(); ?>
                <option value="<?php echo $Res[0];?>"><?php echo $Res[0];?> ( <?php echo $Res[1];?> )</option>
<?php } ?>
            </select>
            <input type="button" class="btn btn-secundario" value="Trasladar" onClick="TrasladarGrupo()">
        </span>
        <input type="button" class="btn btn-secundario al-final" value="Cerrar" onClick="GoBack()">
    </div>
<?php } else { ?>
    <input type="hidden" id="idLiv" value="<?php echo $IdLiving;?>">
    <input type="hidden" id="fecha" value="<?php echo $Fecha;?>">
    <input type="hidden" id="Mesa" value="<?php echo $Mesa;?>">
    <input type="hidden" id="IdClvp" value="0">
    <input type="hidden" id="TipoAsigna" value="">

    <div id="respuesta">
        <p class="opciones-titulo">&iquest;Qu&eacute; quieres hacer con esta mesa?</p>
        <form name="tipoasigna" class="opciones">
            <label class="opcion">
                <input type="radio" name="tcliente" value="2" onClick="AsignacionMesa(2)">
                <span class="opcion-caja">
                    <span class="opcion-nombre">Cliente for&aacute;neo</span>
                    <span class="opcion-detalle">Lleg&oacute; al local sin reservar y ocupa la mesa ahora.</span>
                </span>
            </label>
            <label class="opcion">
                <input type="radio" name="tcliente" value="3" onClick="AsignacionMesa(3)">
                <span class="opcion-caja">
                    <span class="opcion-nombre">Bloquear</span>
                    <span class="opcion-detalle">La mesa no se podr&aacute; asignar en esta fecha.</span>
                </span>
            </label>
            <label class="opcion">
                <input type="radio" name="tcliente" value="4" onClick="AsignacionMesa(4)">
                <span class="opcion-caja">
                    <span class="opcion-nombre">Mesa compartida</span>
                    <span class="opcion-detalle">Varios grupos peque&ntilde;os comparten la mesa.</span>
                </span>
            </label>
            <input type="hidden" name="tcliente" value="5" checked>
        </form>
        <div id="divCliVip"></div>
    </div>

    <div class="acciones">
        <input type="button" class="btn btn-secundario al-final" value="Cerrar" onClick="GoBack()">
    </div>
<?php } ?>
</body>
</html>
