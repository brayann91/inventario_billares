<?php

include "../bd.php";

$sentencia = $conexion->prepare("SELECT * FROM tiempos");
$sentencia->execute();
$lista_tiempos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

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
            overflow: hidden;
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
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><a class="table-button" href="jugador1.html"><img src="../images/jugador2.png" alt="Botón 2"></a></td>
                <td><a class="table-button" href="jugador2.html"><img src="../images/jugador3.png" alt="Botón 3"></a></td>
                <td><a class="table-button" href="jugador3.html"><img src="../images/jugador4.png" alt="Botón 4"></a></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <!-- Incluye los archivos JS de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

