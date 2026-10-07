<?php
// Listado de reservas del dia por motivo (cumpleanos o grados).
// $titulo, $fecha y $reservas: filas con nombre, homenajeado, mesas, estado y personas.
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo;?> | Living Night Club</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/tema.css">
    <link rel="stylesheet" href="css/salon_oscuro.css">
</head>
<body>
<div class="container">
    <br>
    <div class="logo-interno">
        <img src="images/logo_prin.jpg" alt="Living">
    </div>
    <p>
        <a class="btn btn-primary" href="dashboard.php?fecha=<?php echo $fecha;?>">&laquo; Atr&aacute;s</a>
    </p>
    <h3><?php echo $titulo;?> del <?php echo $fecha;?>: <?php echo count($reservas);?></h3>
<?php if (count($reservas) == 0) { ?>
    <p>No hay reservas de este tipo para la fecha.</p>
<?php } else { ?>
    <div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>No.</th>
            <th>Nombre de la reserva</th>
            <th>Homenajeado</th>
            <th>Pers.</th>
            <th>Mesa(s)</th>
            <th>Estado</th>
        </tr>
<?php foreach ($reservas as $i => $res) { ?>
        <tr>
            <td><?php echo $i + 1;?></td>
            <td><?php echo $res[0];?></td>
            <td><?php echo $res[1];?></td>
            <td><?php echo $res[4];?></td>
            <td><?php echo $res[2] != "" ? $res[2] : "Sin asignar";?></td>
            <td><?php echo $res[3];?></td>
        </tr>
<?php } ?>
    </table>
    </div>
<?php } ?>
</div>
</body>
</html>
