<?php
// Respuesta AJAX de la pantalla "Realizar reserva" para la fecha elegida (guard=ResForm).
//   $estado: 'pasada', 'sin_servicio', 'evento' o 'formulario'
// Los id de los campos son los que lee Reservar() en js/scripts.js; no cambiarlos.
?>
<?php if ($estado == 'pasada') { ?>
<div class="rv-aviso">
    <strong>Esa fecha ya pas&oacute;.</strong>
    <span>Elige una fecha de hoy en adelante para hacer tu reserva.</span>
</div>
<?php } elseif ($estado == 'sin_servicio') { ?>
<div class="rv-aviso">
    <strong>Ese d&iacute;a no abrimos.</strong>
    <span>Los domingos solo hay servicio cuando el lunes es festivo. Prueba con otra fecha.</span>
</div>
<?php } elseif ($estado == 'evento') { ?>
<div class="rv-aviso">
    <strong>Ese d&iacute;a es el evento <?php echo htmlspecialchars($nombreEvento);?>.</strong>
    <span>Para esta fecha no se reciben reservas por este medio.</span>
</div>
<?php if ($aficheFrente != "" || $afichePrecios != "") { ?>
<div class="rv-afiches">
<?php if ($aficheFrente != "") { ?>
    <a href="<?php echo $aficheFrente;?>" target="_blank"><img src="<?php echo $aficheFrente;?>" alt="Afiche de <?php echo htmlspecialchars($nombreEvento);?>"></a>
<?php } ?>
<?php if ($afichePrecios != "") { ?>
    <a href="<?php echo $afichePrecios;?>" target="_blank"><img src="<?php echo $afichePrecios;?>" alt="Precios de <?php echo htmlspecialchars($nombreEvento);?>"></a>
<?php } ?>
</div>
<?php } ?>
<?php } else { ?>
<div class="rv-cuerpo<?php echo $aficheFrente != "" || $afichePrecios != "" ? " con-afiche" : "";?>">
<?php if ($aficheFrente != "" || $afichePrecios != "") { ?>
    <div class="rv-evento">
        <p>Ese d&iacute;a: <strong><?php echo htmlspecialchars($nombreEvento);?></strong></p>
<?php if ($aficheFrente != "") { ?>
        <a href="<?php echo $aficheFrente;?>" target="_blank"><img class="rv-afiche" src="<?php echo $aficheFrente;?>" alt="Afiche de <?php echo htmlspecialchars($nombreEvento);?>"></a>
<?php } ?>
<?php if ($afichePrecios != "") { ?>
        <a href="<?php echo $afichePrecios;?>" target="_blank"><img class="rv-afiche" src="<?php echo $afichePrecios;?>" alt="Precios de <?php echo htmlspecialchars($nombreEvento);?>"></a>
<?php } ?>
    </div>
<?php } ?>
    <form id="reserva" class="rv-formulario" onsubmit="Reservar(); return false;">
        <p class="rv-fecha">Reserva para el <strong><?php echo $fechaTexto;?></strong></p>

<?php if ($esAdmin) { ?>
        <div class="rv-campo">
            <label for="clientesVIP">&iquest;Es cliente VIP?</label>
            <select id="clientesVIP" onchange="ClienteVIP()">
                <option value="0">No</option>
<?php foreach ($clientesVip as $vip) { ?>
                <option value="<?php echo $vip[0];?>"><?php echo htmlentities($vip[1]);?></option>
<?php } ?>
            </select>
        </div>
<?php } ?>
        <div class="rv-campo">
            <label for="rname">Reserva a nombre de</label>
            <input type="text" id="rname" value="<?php echo $NombreReserva;?>" placeholder="Nombre con el que llegar&aacute;n al local" autocomplete="name" required>
        </div>

        <div class="rv-fila">
            <div class="rv-campo">
                <label for="cper">N&uacute;mero de personas</label>
                <select id="cper">
<?php for ($i = 2; $i <= 50; $i++) { ?>
                    <option value="<?php echo $i;?>"<?php echo $i == 10 ? ' selected' : '';?>><?php echo $i;?></option>
<?php } ?>
                </select>
            </div>
            <div class="rv-campo">
                <label for="rtipocelabra">Tipo de celebraci&oacute;n</label>
                <select id="rtipocelabra" onchange="PasaNombre()">
<?php foreach ($tiposCelebracion as $tipo) { ?>
                    <option value="<?php echo $tipo[0];?>"<?php echo $tipo[0] == $TipoCele ? ' selected' : '';?>><?php echo htmlentities($tipo[1]);?></option>
<?php } ?>
                </select>
            </div>
        </div>

        <div class="rv-campo">
            <label for="rhomena">Nombre del homenajeado</label>
            <input type="text" id="rhomena" value="" disabled>
            <span class="rv-ayuda">Solo para cumplea&ntilde;os. Si son varios, sep&aacute;ralos con coma.</span>
        </div>

        <div class="rv-campo">
            <label for="robserva">Observaciones</label>
            <textarea id="robserva" rows="3" placeholder="Algo que debamos saber de tu reserva"></textarea>
        </div>

        <label class="rv-casilla">
            <input type="checkbox" id="news" name="news"<?php echo $recibeCorreo ? ' checked' : '';?>>
            <span>Enviarme la confirmaci&oacute;n por correo</span>
        </label>

        <button class="rv-boton" type="submit" id="registro"><?php echo $NombreBoton;?></button>
    </form>
</div>
<?php } ?>
