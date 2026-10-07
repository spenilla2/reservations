<?php // Pantalla "Realizar reserva": se elige la fecha y el formulario llega por AJAX (vista reservas/formulario). ?>
<link href="css/reservas.css" rel="stylesheet" type="text/css">
<section class="container">
    <input type="hidden" id="idLiving" value="<?php echo $Idliving;?>">
    <input type="hidden" id="fechareserva">
    <input type="hidden" id="numReserva" value="<?php echo $NumReserva;?>">
    <input type="hidden" id="tipoReservaK" value="<?php echo $tipo;?>">
    <input type="hidden" id="nombreUsuario" value="<?php echo $NReserva;?>">
    <input type="hidden" id="idClienteVip" value="0">
    <input type="hidden" id="Perfil" value="<?php echo $Perfil;?>">
    <input type="hidden" id="tipo" value="<?php echo $tipo;?>">

    <div class="rv-tarjeta">
        <h1>Reserva tu mesa</h1>
        <p class="rv-sub">Elige la fecha y completa los datos de tu reserva.</p>

        <div class="rv-campo rv-campo-fecha">
            <label for="dc">Fecha de la reserva</label>
            <input type="date" id="dc" name="nombre_de_la_caja" value="<?php echo $Fecha;?>" min="<?php echo $Fecha;?>" onchange="ResevaForm()">
        </div>

        <div class="resform" id="resform" aria-live="polite"></div>
    </div>
</section>
<script>
    // el formulario de la fecha de hoy se carga al entrar, sin tener que pulsar nada
    window.addEventListener("load", function(){ ResevaForm(); });
</script>
