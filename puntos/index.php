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

if($lista_tiempos['estado_tiempo'] == 1){
    if($lista_puntos['estado_punto'] == 0 || !isset($lista_puntos['estado_punto'])){
        $sentencia = $conexion->prepare("INSERT INTO puntos(jugador1, jugador2, jugador3, jugador4, limit_jugador1, limit_jugador2, limit_jugador3, limit_jugador4,
        serie, entrada, puntos_jugador1, puntos_jugador2, puntos_jugador3, puntos_jugador4, estado_punto, id_cuenta)
        VALUES ('Jugador 1', 'Jugador 2', 'Jugador 3', 'Jugador 4', 999, 999, 999, 999, 0, 0, 0, 0, 0, 0, 1, :id_cuenta)");
        $sentencia->bindParam(":id_cuenta", $txtID);
        $sentencia->execute();
    }
}

?>

<!doctype html>
<html lang="en">

<!DOCTYPE html>
<html>
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
        .centered-text {
            position: relative;
            text-align: top;
            width: 100%;
            font-size: 50px;
            color: white;
        }
        .table {
            color: white;
        }
        .table-fill {
            width: 100%;
            height: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .table-fill td {
            width: 33.33%;
            height: 33.33%;
            vertical-align: middle;
            padding: 0;
        }

        .table-button {
            display: block;
            width: 100%;
            height: 100%;
            background: none;
            border: none;
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body>
    <!-- Contenido de tu página -->
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center p-0">
        <table class="table table-borderless table-fill m-0">
            <tr>
                <td colspan="3"><h1 id="cuenta" class="centered-text"><?php echo $lista_tiempos['nombre_cuenta'];?></h1></td>
            </tr>
            <tr>
                <td class="col-4"><a class="table-button" href="limit_jugador2.php?txtID=<?php echo $txtID;?>"><img src="../images/jugador_2.png" alt="Botón 2"></a></td>
                <td class="col-4"><a class="table-button" href="limit_jugador3.php?txtID=<?php echo $txtID;?>"><img src="../images/jugador_3.png" alt="Botón 3"></a></td>
                <td class="col-4"><a class="table-button" href="limit_jugador4.php?txtID=<?php echo $txtID;?>"><img src="../images/jugador_4.png" alt="Botón 4"></a></td>
            </tr>
            <tr>
                <td class="col-4"><p class="centered-text">2 Jugadores</p></td>
                <td class="col-4"><p class="centered-text">3 Jugadores</p></td>
                <td class="col-4"><p class="centered-text">4 Jugadores</p></td>
            </tr>
        </table>
    </div>

    <!-- Incluye los archivos JS de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" rel="stylesheet">
</body>
</html>

