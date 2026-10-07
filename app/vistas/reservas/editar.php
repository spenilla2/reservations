<?php
// Pantalla "Modificar reserva" (reserva.php). Mismo estilo que "Realizar reserva".
// Los id de los campos son los que lee Reservar() en js/scripts.js; no cambiarlos.
?>
<link href="css/reservas.css" rel="stylesheet" type="text/css">
<section class="container">
    <input type="hidden" id="idLiving" value="<?php echo $IdLiving;?>">
    <input type="hidden" id="fechareserva">
    <input type="hidden" id="numReserva" value="<?php echo $NumReserva;?>">
    <input type="hidden" id="tipoReservaK" value="<?php echo $tipo;?>">
    <input type="hidden" id="nombreUsuario" value="<?php echo $NReserva;?>">
    <input type="hidden" id="idClienteVip" value="0">
    <input type="hidden" id="Perfil" value="<?php echo $Perfil;?>">
    <input type="hidden" id="tipo" value="<?php echo $tipo;?>">

    <div class="rv-tarjeta">
        <h1>Modificar reserva No. <?php echo $NumReserva;?></h1>
        <p class="rv-sub">
            Para el <strong><?php echo $fechaTexto;?></strong><?php echo $mesas != "" ? " &middot; mesa ".$mesas : "";?>.
            La fecha no se puede cambiar: si necesitas otro d&iacute;a, cancela esta reserva y crea una nueva.
        </p>

        <form id="reserva" class="rv-formulario" onsubmit="Reservar(); return false;">
<?php if ($esAdmin) { ?>
            <div class="rv-campo">
                <label for="clientesVIP">&iquest;Es cliente VIP?</label>
                <select id="clientesVIP" onchange="ClienteVIP()"<?php echo $vipBloqueado ? ' disabled' : '';?>>
                    <option value="0">No</option>
<?php foreach ($clientesVip as $vip) { ?>
                    <option value="<?php echo $vip[0];?>"><?php echo htmlentities($vip[1]);?></option>
<?php } ?>
                </select>
            </div>
<?php } ?>
            <div class="rv-campo">
                <label for="rname">Reserva a nombre de</label>
                <input type="text" id="rname" value="<?php echo $NombreReserva;?>" required>
            </div>

            <div class="rv-fila">
                <div class="rv-campo">
                    <label for="rnumperson">N&uacute;mero de personas</label>
                    <select id="rnumperson">
<?php if ($NumPer < 2 || $NumPer > 60) { ?>
                        <option value="<?php echo $NumPer;?>" selected><?php echo $NumPer;?></option>
<?php } ?>
<?php for ($i = 2; $i <= 60; $i++) { ?>
                        <option value="<?php echo $i;?>"<?php echo $i == $NumPer ? ' selected' : '';?>><?php echo $i;?></option>
<?php } ?>
                    </select>
                </div>
                <div class="rv-campo">
                    <label for="rtipocelabra">Tipo de celebraci&oacute;n</label>
                    <select id="rtipocelabra" onchange="PasaNombre()">
<?php foreach ($tiposCelebracion as $tipoCelebracion) { ?>
                        <option value="<?php echo $tipoCelebracion[0];?>"<?php echo $tipoCelebracion[0] == $TipoCele ? ' selected' : '';?>><?php echo htmlentities($tipoCelebracion[1]);?></option>
<?php } ?>
                    </select>
                </div>
            </div>

            <div class="rv-campo">
                <label for="rhomena">Nombre del homenajeado</label>
                <input type="text" id="rhomena" value="<?php echo $NomH;?>"<?php echo $TipoCele == 1 ? '' : ' disabled';?>>
                <span class="rv-ayuda">Solo para cumplea&ntilde;os. Si son varios, sep&aacute;ralos con coma.</span>
            </div>

            <div class="rv-campo">
                <label for="robserva">Observaciones</label>
                <textarea id="robserva" rows="3" placeholder="Algo que debamos saber de tu reserva"><?php echo $RObservac;?></textarea>
            </div>

            <label class="rv-casilla">
                <input type="checkbox" id="news" name="news"<?php echo $recibeCorreo ? ' checked' : '';?>>
                <span>Enviarme la confirmaci&oacute;n por correo</span>
            </label>

            <div class="rv-botones">
                <a class="rv-boton rv-boton-secundario" href="consultaReservas.php">Volver sin guardar</a>
                <button class="rv-boton" type="submit" id="registro">Guardar cambios</button>
            </div>
        </form>

        <div class="resform" id="resform" aria-live="polite"></div>
    </div>
</section>
