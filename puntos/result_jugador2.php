<?php

include "../bd.php";

if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
}

$sentencia = $conexion->prepare("SELECT * FROM tiempos t INNER JOIN cuentas c ON c.id_cuenta = t.id_cuenta WHERE t.id_cuenta=:id_cuenta ORDER BY t.id_tiempo DESC LIMIT 1");
$sentencia->bindParam(":id_cuenta", $txtID);
$sentencia->execute();
$lista_tiempos = $sentencia->fetch(PDO::FETCH_LAZY);

$sentencia = $conexion->prepare("SELECT * FROM puntos WHERE id_cuenta=:id_cuenta ORDER BY id_punto DESC LIMIT 1");
$sentencia->bindParam(":id_cuenta", $txtID);
$sentencia->execute();
$lista_puntos = $sentencia->fetch(PDO::FETCH_LAZY);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tu página</title>
    <!-- Incluye los archivos CSS de Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-image: url('../images/fondo.jpg'); /* Cambia la ruta por la ubicación de tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            user-select: none;
            font-family: "Rajdhani";
            text-shadow: 0 0 15px rgba(255, 0, 0, 0.6);
        }

        .table {
            width: 100%;
            height: 100%;
            text-align: center;
            border-collapse: collapse;
            display: table;
            color: white;
        }
        .td {
            text-align: center;
            vertical-align: middle;
        }
        

    </style>
</head>
<body>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center p-0">
        <table class="table table-fill m-0">
            <tr>
                <td colspan="5" class="table-cell col-12 td"><h1 id="cuenta"><?php echo $lista_tiempos['nombre_cuenta'];?></h1></td>
            </tr>
            <tr>
                <td rowspan="5" class="table-cell col-3 td"><h1><?php echo $lista_puntos['jugador1'];?></h1></td>

                <?php if($lista_puntos['puntos_jugador1'] >= $lista_puntos['puntos_jugador2']) {?>
                    <td class="table-cell col-2 td"><h1 >Ganador</h1></td>
                <?php } else {?>
                    <td class="table-cell col-2 td"><h1>Perdedor</h1></td>
                <?php }?>

                <td class="table-cell col-2"><h1>vs</h1></td>
                
                <?php if($lista_puntos['puntos_jugador2'] >= $lista_puntos['puntos_jugador1']) {?>
                    <td class="table-cell col-2 td"><h1>Ganador</h1></td>
                <?php } else {?>
                    <td class="table-cell col-2 td"><h1>Perdedor</h1></td>
                <?php }?>

                <td rowspan="5" class="table-cell col-3"><h1><?php echo $lista_puntos['jugador2'];?></h1></td>
            </tr>

            <tr class="table-row">
                <td class="table-cell col-2"><h1><?php echo $lista_puntos['puntos_jugador1'];?></h1></td>
                <td class="table-cell col-2"><h1>Resultado</h1></td>
                <td class="table-cell col-2"><h1><?php echo $lista_puntos['puntos_jugador2'];?></h1></td>
            </tr>

            <tr class="table-row">
                <td class="table-cell col-2"><h1><?php echo $lista_puntos['entrada'];?></h1></td>
                <td class="table-cell col-2"><h1>Entradas</h1></td>
                <td class="table-cell col-2"><h1><?php echo $lista_puntos['entrada'];?></h1></td>
            </tr>

            <tr class="table-row">
                <td class="table-cell col-2"><h1><?php echo intval(($lista_puntos['puntos_jugador1'] * 1000) / $lista_puntos['entrada']);?></h1></td>
                <td class="table-cell col-2"><h1>Promedio</h1></td>
                <td class="table-cell col-2"><h1><?php echo intval(($lista_puntos['puntos_jugador2'] * 1000) / $lista_puntos['entrada']);?></h1></td>
            </tr>

            <tr class="table-row">
                <td class="table-cell col-2"><h1><?php echo $lista_puntos['max_serie1'];?></h1></td>
                <td class="table-cell col-2"><h1>Mayor Serie</h1></td>
                <td class="table-cell col-2"><h1><?php echo $lista_puntos['max_serie2'];?></h1></td>
            </tr>

            <tr class="table-row">
                <td colspan="5" class="table-cell col-12">
                <a name="" id="" class="btn btn-secondary" href="index.php?txtID=<?php echo $txtID;?>" 
                role="button" onclick="return confirm('¿Estás seguro de que quieres finalizar?')">Finalizar</a>

                </td>
            </tr>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" rel="stylesheet">
</body>
</html>



