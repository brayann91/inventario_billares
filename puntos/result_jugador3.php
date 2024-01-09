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

if (isset($_POST["idCuenta"])) {
    
    $idCuenta = $_POST["idCuenta"];

    $sentencia = $conexion->prepare("UPDATE puntos SET
    jugador1 = 'Jugador 1',
    jugador2 = 'Jugador 2',
    jugador3 = 'Jugador 3',
    jugador4 = 'Jugador 4',
    limit_jugador1 = 999,
    limit_jugador2 = 999,
    limit_jugador3 = 999,
    limit_jugador4 = 999,
    serie = 0,
    entrada = 0,
    puntos_jugador1 = 0,
    puntos_jugador2 = 0,
    puntos_jugador3 = 0,
    puntos_jugador4 = 0,
    max_serie1 = 0,
    max_serie2 = 0,
    max_serie3 = 0,
    max_serie4 = 0,
    estado_punto = 0
    WHERE id_cuenta=:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $idCuenta);
    $sentencia->execute();
}

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
            background-image: url('../images/fondo.jpg');
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
                <td class="table-cell col-2 td"><h1></h1></td>    
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['jugador1'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['jugador2'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['jugador3'];?></h1></td>
            </tr>
            <tr>
                <?php 
                $valores  = [$lista_puntos['puntos_jugador1'], $lista_puntos['puntos_jugador2'], $lista_puntos['puntos_jugador3']];
                $resultado  = ["Ganador", "Segundo", "Tercero"];
                rsort($valores);?>

                <td class="table-cell col-2 td"><h1 >Resultado</h1></td>

                <?php for ($i = 0; $i < count($valores); $i++) {  
                    if ($valores[$i] == $lista_puntos['puntos_jugador1']){ 
                    ?>
                    <td class="table-cell col-2 td"><h1 ><?php echo $resultado[$i]?></h1></td>
                <?php $i=5;
                    }
                }
                
                for ($i = 0; $i < count($valores); $i++) {  
                    if ($valores[$i] == $lista_puntos['puntos_jugador2']){ 
                    ?>
                    <td class="table-cell col-2 td"><h1 ><?php echo $resultado[$i]?></h1></td>
                <?php $i=5;
                    }
                }?>
                
                <?php for ($i = 0; $i < count($valores); $i++) {  
                    if ($valores[$i] == $lista_puntos['puntos_jugador3']){ 
                    ?>
                    <td class="table-cell col-2 td"><h1 ><?php echo $resultado[$i]?></h1></td>
                <?php $i=5;
                    }
                }?>
                
            </tr>

            <tr>
                <td class="table-cell col-2 td"><h1>Puntuación</h1></td>    
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['puntos_jugador1'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['puntos_jugador2'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['puntos_jugador3'];?></h1></td>
            </tr>

            <tr>
                <td class="table-cell col-2 td"><h1>Entradas</h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['entrada'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['entrada'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['entrada'];?></h1></td>
            </tr>

            <tr>
                <td class="table-cell col-2 td"><h1>Promedio</h1></td>
                <td class="table-cell col-2 td"><h1>
                    <?php if($lista_puntos['entrada'] != 0){ echo intval(($lista_puntos['puntos_jugador1'] * 1000) / $lista_puntos['entrada']);}?>
                </h1></td>
                <td class="table-cell col-2 td"><h1>
                    <?php if($lista_puntos['entrada'] != 0){ echo intval(($lista_puntos['puntos_jugador2'] * 1000) / $lista_puntos['entrada']);}?>
                </h1></td>   
                <td class="table-cell col-2 td"><h1>
                    <?php if($lista_puntos['entrada'] != 0){ echo intval(($lista_puntos['puntos_jugador3'] * 1000) / $lista_puntos['entrada']);}?>
                </h1></td>
            </tr>

            <tr>
                <td class="table-cell col-2 td"><h1>Mayor Serie</h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['max_serie1'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['max_serie2'];?></h1></td>
                <td class="table-cell col-2 td"><h1><?php echo $lista_puntos['max_serie3'];?></h1></td>
            </tr>

            <tr class="table-row">
                <td colspan="5" class="table-cell col-12">
                    <a name="" id="" class="btn btn-secondary"
                    role="button" onclick="Restablecer('<?php echo $txtID;?>'); return false;">Finalizar</a>
                </td>
            </tr>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</body>
<script>

function Restablecer(idCuenta) {
    
    var confirmacion = confirm("¿Estás seguro que deseas finalizar?");
    
    if (confirmacion) {
        $.ajax({
            url: 'result_jugador3.php?txtID=' + idCuenta,
            method: 'POST',
            data: { idCuenta: idCuenta },
            success: function(response) {
                window.location.href = 'index.php?txtID=' + idCuenta;
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr.responseText);
            }
        });
    } else {
        console.log("Operación cancelada por el usuario.");
    }
}


</script>
</html>



