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

$sentencia = $conexion->prepare("SELECT * FROM puntos WHERE id_cuenta=:id_cuenta ORDER BY id_punto DESC LIMIT 1");
$sentencia->bindParam(":id_cuenta", $txtID);
$sentencia->execute();
$lista_puntos = $sentencia->fetch(PDO::FETCH_LAZY);

if($_POST){
    $txtID = (isset($_GET['txtID'])) ? $_GET['txtID'] : "";
    $puntos_jugador1 = (isset($_POST["puntos_jugador1"]) ? $_POST["puntos_jugador1"] : "");
    $puntos_jugador2 = (isset($_POST["puntos_jugador2"]) ? $_POST["puntos_jugador2"] : "");
    $nombre_jugador1 = (isset($_POST["nombre_jugador1"]) ? $_POST["nombre_jugador1"] : "");
    $nombre_jugador2 = (isset($_POST["nombre_jugador2"]) ? $_POST["nombre_jugador2"] : "");

    if($puntos_jugador1 == ""){
        $puntos_jugador1 = 999;
    }
    if($puntos_jugador2 == ""){
        $puntos_jugador2 = 999;
    }
    if($nombre_jugador1 == ""){
        $nombre_jugador1 = "Jugador 1";
    }
    if($nombre_jugador2 == ""){
        $nombre_jugador2 = "Jugador 2";
    }

    $sentencia = $conexion->prepare("UPDATE puntos SET
    jugador1=:jugador1,
    jugador2=:jugador2,
    limit_jugador1=:limit_jugador1,
    limit_jugador2=:limit_jugador2
    WHERE id_punto=:id_punto");

    //Asignando los valores que vienen del metodo POST ( los que vienen del formulario)
    $sentencia->bindParam(":jugador1", $nombre_jugador1);
    $sentencia->bindParam(":jugador2", $nombre_jugador2);
    $sentencia->bindParam(":limit_jugador1", $puntos_jugador1);
    $sentencia->bindParam(":limit_jugador2", $puntos_jugador2);
    $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
    $sentencia->execute();

    header("Location:jugador2.php?txtID=".$txtID);
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
            background-image: url('../images/fondo.jpg'); /* Cambia la ruta por la ubicación de tu imagen */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
        }

        .table-fill {
            width: 100%;
            height: 100%;
            text-align: center;
            border-collapse: collapse;
            display: table;
            color: white;
        }

        .table-row {
            display: table-row;
        }

        .table-cell {
            display: table-cell;
            width: 25%; /* Ajusta el ancho de las celdas según tu diseño */
            vertical-align: middle;
            padding: 0;
        }

        .align-center-top {
            text-align: center;
            vertical-align: top;
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
        a.btn {
            display: inline-block;
            padding: 10px 20px; 
            font-size: 16px; 
            text-decoration: none;
            color: #FFFFFF;
            border: 1px solid #E74C3C;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
        }
        #espacio {
            width: 20%;
        }
        
    </style>
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center p-0">
        <table class="table table-borderless table-fill m-0">
            <tr class="table-row">
                <td colspan="4" class="table-cell"><h1 id="cuenta"><?php echo $lista_tiempos['nombre_cuenta'];?></h1></td>
            </tr>
            <tr class="table-row">
                <td colspan="4" class="table-cell"><h1>¿A cuantos puntos?</h1></td>
            </tr> 
            <tr class="table-row">
                <td colspan="2" class="table-cell">
                <div class="mb-3 d-flex justify-content-center">
                    <input type="number" class="form-control form-control-lg w-25 text-center" name="puntos_jugador1" id="puntos_jugador1" 
                    aria-describedby="helpId" placeholder="999" min="0" max="999" maxlength="3" inputmode="numeric" onclick="cambiarEstadoTeclado('puntos_jugador1')">
                </div>
                </td>

                <td colspan="2" class="table-cell">
                <div class="mb-3 d-flex justify-content-center">
                    <input type="number" class="form-control form-control-lg w-25 text-center" name="puntos_jugador2" id="puntos_jugador2" 
                    aria-describedby="helpId" placeholder="999" maxlength="3" inputmode="numeric" onclick="cambiarEstadoTeclado('puntos_jugador2')">
                </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <a name="uno" id="uno" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '1')"> 1 </a>
                    <a name="dos" id="dos" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '2')"> 2 </a>
                    <a name="tres" id="tres" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '3')"> 3 </a>
                    <a name="cuatro" id="cuatro" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '4')"> 4 </a>
                    <a name="cinco" id="cinco" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '5')"> 5 </a>
                </td>

            </tr>
            <tr>
                <td colspan="4">  
                    <a name="seis" id="seis" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '6')"> 6 </a>
                    <a name="siete" id="siete" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '7')"> 7 </a>
                    <a name="ocho" id="ocho" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '8')"> 8 </a>
                    <a name="nueve" id="nueve" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, '9')"> 9 </a>
                    <a name="borrador_jugador1" id="borrador_jugador1" class="btn btn-secondary" role="button" onclick="borrarTexto(estado_teclado)"> ← </a>
                </td>
            </tr>
            <tr class="table-row">
                <td colspan="2" class="table-cell align-center-top">
                    <h3>Nombre jugador 1:</h3>
                </td>
                <td colspan="2" class="table-cell align-center-top">
                    <h3>Nombre jugador 2:</h3>
                </td>
            </tr>  
            <tr class="table-row">
                <td colspan="2" class="table-cell">
                <div class="mb-3 d-flex justify-content-center">
                    <input type="text" class="form-control form-control-lg w-50 text-center" name="nombre_jugador1" id="nombre_jugador1" 
                    aria-describedby="helpId" placeholder="Pablo" maxlength="20" inputmode="text" onclick="cambiarEstadoTeclado('nombre_jugador1')">
                </div>
                </td>
                <td colspan="2" class="table-cell">
                <div class="mb-3 d-flex justify-content-center">
                    <input type="text" class="form-control form-control-lg w-50 text-center" name="nombre_jugador2" id="nombre_jugador2" 
                    aria-describedby="helpId" placeholder="Pedro" maxlength="20" inputmode="text" onclick="cambiarEstadoTeclado('nombre_jugador2')">
                </div>
                </td>
            </tr>

            <tr>
                <td colspan="4">  
                    <a name="q" id="q" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'Q')"> Q </a>
                    <a name="w" id="w" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'W')"> W </a>
                    <a name="e" id="e" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'E')"> E </a>
                    <a name="r" id="r" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'R')"> R </a>
                    <a name="t" id="t" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'T')"> T </a>
                    <a name="y" id="y" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'Y')"> Y </a>
                    <a name="u" id="u" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'U')"> U </a>
                    <a name="i" id="i" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'I')"> I </a>
                    <a name="o" id="o" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'O')"> O </a>
                    <a name="p" id="p" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'P')"> P </a>
                </td>
            </tr>

            <tr>
                <td colspan="4">  
                    <a name="a" id="a" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'A')"> A </a>
                    <a name="s" id="s" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'S')"> S </a>
                    <a name="d" id="d" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'D')"> D </a>
                    <a name="f" id="f" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'F')"> F </a>
                    <a name="g" id="g" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'G')"> G </a>
                    <a name="h" id="h" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'H')"> H </a>
                    <a name="j" id="j" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'J')"> J </a>
                    <a name="k" id="k" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'K')"> K </a>
                    <a name="l" id="l" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'L')"> L </a>
                    <a name="ñ" id="ñ" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'Ñ')"> Ñ </a>
                </td>
            </tr>

            <tr>
                <td colspan="4">  
                    <a name="z" id="z" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'Z')"> Z </a>
                    <a name="x" id="x" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'X')"> X </a>
                    <a name="c" id="c" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'C')"> C </a>
                    <a name="v" id="v" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'V')"> V </a>
                    <a name="b" id="b" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'B')"> B </a>
                    <a name="n" id="n" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'N')"> N </a>
                    <a name="m" id="m" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, 'M')"> M </a>
                    <a name="borrador_jugador1" id="borrador_jugador1" class="btn btn-secondary" role="button" onclick="borrarTexto(estado_teclado)"> ← </a>
                </td>
            </tr>

            <tr>
                <td colspan="4">  
                    <a name="espacio" id="espacio" class="btn btn-secondary" role="button" onclick="agregarTexto(estado_teclado, ' ')"> __________ </a>
                </td>
            </tr>

            <tr class="table-row">
                <td colspan="4" class="table-cell">
                <button type="submit" class="btn btn-success">Continuar</button>
                <a name="" id="" class="btn btn-secondary" href="index.php?txtID=<?php echo $txtID;?>" role="button">Cancelar</a>
                </td>
            </tr>
        </table>
    </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>

    var estado_teclado = "puntos_jugador1";

    function agregarTexto(elemento, valor) {
        var miInput = document.getElementById(elemento);

        if (miInput.value.trim() === "") {
            miInput.value = valor;
        } else {
            miInput.value += valor;
        }
    }

    function borrarTexto(elemento) {
        var miInput = document.getElementById(elemento);

        if (miInput.value.length > 0) {
            miInput.value = miInput.value.slice(0, -1);
        }
    }

    function cambiarEstadoTeclado(input) {
        
        estado_teclado = input;

        console.log("Estado del teclado: " + estado_teclado);
    }
    

</script>

</body>
</html>



