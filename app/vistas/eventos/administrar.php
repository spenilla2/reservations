<?php
// Administracion de eventos (eventos.php): formulario para crear o editar y la lista de eventos.
//   $enEdicion: evento que se esta editando o null; $proximos / $pasados: listas con rutaFrente, rutaPrecios y fechaTexto
//   $aviso: array('texto', 'bien') con el resultado de la ultima accion, o null
$editando = $enEdicion != null;
?>
<link href="css/reservas.css" rel="stylesheet" type="text/css">
<link href="css/eventos.css" rel="stylesheet" type="text/css">
<section class="container">
    <div class="rv-tarjeta ev-tarjeta">
        <h1><?php echo $editando ? "Editar evento" : "Nuevo evento";?></h1>
        <p class="rv-sub">Un evento es un concierto con dos im&aacute;genes: la frontal, que se muestra en la cartelera, y la de precios.</p>

<?php if ($aviso) { ?>
        <p class="ev-aviso <?php echo $aviso['bien'] ? 'bien' : 'mal';?>" role="alert"><?php echo $aviso['texto'];?></p>
<?php } ?>

        <form method="post" action="eventos.php" enctype="multipart/form-data" class="rv-formulario">
            <input type="hidden" name="accion" value="guardar">
            <input type="hidden" name="id" value="<?php echo $editando ? (int)$enEdicion['id'] : 0;?>">

            <div class="rv-fila">
                <div class="rv-campo">
                    <label for="nombre">Nombre del evento</label>
                    <input type="text" id="nombre" name="nombre" maxlength="100" value="<?php echo $editando ? htmlspecialchars($enEdicion['nombre']) : '';?>" placeholder="Artista o nombre del concierto" required>
                </div>
                <div class="rv-campo">
                    <label for="fecha">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="<?php echo $editando ? $enEdicion['fecha'] : '';?>"<?php echo $editando ? '' : ' min="'.$hoy.'"';?> required>
                </div>
            </div>

            <div class="rv-fila">
                <div class="rv-campo">
                    <label for="frente">Imagen frontal<?php echo $editando ? " (d&eacute;jala vac&iacute;a para conservar la actual)" : "";?></label>
                    <input type="file" id="frente" name="frente" accept="image/jpeg,image/png,image/webp" data-vista="vistaFrente"<?php echo $editando ? '' : ' required';?>>
                    <img class="ev-vista" id="vistaFrente" alt="Vista previa de la imagen frontal"<?php echo $editando && EventoControlador::rutaImagen($enEdicion['frente']) != "" ? ' src="'.EventoControlador::rutaImagen($enEdicion['frente']).'"' : ' hidden';?>>
                </div>
                <div class="rv-campo">
                    <label for="precios">Imagen de precios<?php echo $editando ? " (d&eacute;jala vac&iacute;a para conservar la actual)" : "";?></label>
                    <input type="file" id="precios" name="precios" accept="image/jpeg,image/png,image/webp" data-vista="vistaPrecios"<?php echo $editando ? '' : ' required';?>>
                    <img class="ev-vista" id="vistaPrecios" alt="Vista previa de la imagen de precios"<?php echo $editando && EventoControlador::rutaImagen($enEdicion['precios']) != "" ? ' src="'.EventoControlador::rutaImagen($enEdicion['precios']).'"' : ' hidden';?>>
                </div>
            </div>
            <span class="rv-ayuda ev-ayuda">JPG, PNG o WEBP de hasta 5 MB cada una.</span>

            <label class="rv-casilla">
                <input type="checkbox" name="reservas" value="S"<?php echo !$editando || $enEdicion['reservas'] == 'S' ? ' checked' : '';?>>
                <span>Ese d&iacute;a se pueden hacer reservas desde el sistema</span>
            </label>

            <div class="rv-botones">
<?php if ($editando) { ?>
                <a class="rv-boton rv-boton-secundario" href="eventos.php">Cancelar</a>
<?php } else { ?>
                <span></span>
<?php } ?>
                <button class="rv-boton" type="submit"><?php echo $editando ? "Guardar cambios" : "Crear evento";?></button>
            </div>
        </form>
    </div>

    <div class="rv-tarjeta ev-tarjeta">
        <h1>Pr&oacute;ximos eventos</h1>
<?php if (count($proximos) == 0) { ?>
        <p class="rv-sub">No hay eventos programados de hoy en adelante.</p>
<?php } else { ?>
        <div class="ev-lista">
<?php foreach ($proximos as $evento) { ?>
            <article class="ev-evento">
                <div class="ev-imagenes">
<?php foreach (array('rutaFrente' => 'Frontal', 'rutaPrecios' => 'Precios') as $clave => $rotulo) { ?>
<?php if ($evento[$clave] != "") { ?>
                    <a href="<?php echo $evento[$clave];?>" target="_blank" title="Ver imagen <?php echo strtolower($rotulo);?>"><img src="<?php echo $evento[$clave];?>" alt="<?php echo $rotulo;?>" loading="lazy"><span><?php echo $rotulo;?></span></a>
<?php } else { ?>
                    <span class="ev-sin-imagen">Sin imagen<br><?php echo strtolower($rotulo);?></span>
<?php } ?>
<?php } ?>
                </div>
                <div class="ev-datos">
                    <h2><?php echo htmlspecialchars($evento['nombre']);?></h2>
                    <p class="ev-fecha"><?php echo $evento['fechaTexto'];?></p>
                    <p class="ev-reservas <?php echo $evento['reservas'] == 'S' ? 'si' : 'no';?>"><?php echo $evento['reservas'] == 'S' ? "Recibe reservas" : "Sin reservas por el sistema";?></p>
                    <div class="ev-acciones">
                        <a class="btn btn-default btn-sm" href="eventos.php?editar=<?php echo (int)$evento['id'];?>">Editar</a>
                        <form method="post" action="eventos.php" onsubmit="return confirm('&iquest;Eliminar este evento y sus im&aacute;genes?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo (int)$evento['id'];?>">
                            <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
                        </form>
                    </div>
                </div>
            </article>
<?php } ?>
        </div>
<?php } ?>

<?php if (count($pasados) > 0) { ?>
        <details class="ev-pasados">
            <summary>Eventos pasados (<?php echo count($pasados);?>)</summary>
            <table>
<?php foreach ($pasados as $evento) { ?>
                <tr>
                    <td><?php echo $evento['fecha'];?></td>
                    <td><?php echo htmlspecialchars($evento['nombre']);?></td>
                    <td>
                        <form method="post" action="eventos.php" onsubmit="return confirm('&iquest;Eliminar este evento?');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?php echo (int)$evento['id'];?>">
                            <a href="eventos.php?editar=<?php echo (int)$evento['id'];?>">Editar</a>
                            <button type="submit" class="ev-enlace">Eliminar</button>
                        </form>
                    </td>
                </tr>
<?php } ?>
            </table>
        </details>
<?php } ?>
    </div>
</section>
<script>
    // vista previa de cada imagen en cuanto se elige el archivo
    [].forEach.call(document.querySelectorAll('input[type="file"][data-vista]'), function(campo){
        campo.addEventListener("change", function(){
            var vista = document.getElementById(campo.getAttribute("data-vista"));
            if(campo.files && campo.files[0]){
                vista.src = URL.createObjectURL(campo.files[0]);
                vista.hidden = false;
            }
        });
    });
</script>
