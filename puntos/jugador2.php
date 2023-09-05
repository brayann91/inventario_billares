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

$sentencia = $conexion->prepare("SELECT * FROM cuentas WHERE id_cuenta=:id_cuenta");
$sentencia->bindParam(":id_cuenta", $txtID);
$sentencia->execute();
$lista_cuenta = $sentencia->fetch(PDO::FETCH_LAZY);

if (isset($_POST["idCuenta1"])) {
    $valorSuma = (isset($_POST["valorSuma"]) ? $_POST["valorSuma"] : "");

    $sentencia = $conexion->prepare("UPDATE puntos SET
    puntos_jugador1 = (puntos_jugador1 + :valorSuma)
    WHERE id_punto=:id_punto");
    $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
    $sentencia->bindParam(":valorSuma", $valorSuma);
    $sentencia->execute();
}

if (isset($_POST["idCuenta2"])) {
    $valorSuma = (isset($_POST["valorSuma"]) ? $_POST["valorSuma"] : "");

    $sentencia = $conexion->prepare("UPDATE puntos SET
    puntos_jugador2 = (puntos_jugador2 + :valorSuma)
    WHERE id_punto=:id_punto");
    $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
    $sentencia->bindParam(":valorSuma", $valorSuma);
    $sentencia->execute();
}

if (isset($_POST["idCuenta_entrada"])) {
    $valorSuma = (isset($_POST["valorSuma"]) ? $_POST["valorSuma"] : "");

    $sentencia = $conexion->prepare("UPDATE puntos SET
    entrada = (entrada + :valorSuma)
    WHERE id_punto=:id_punto");
    $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
    $sentencia->bindParam(":valorSuma", $valorSuma);
    $sentencia->execute();
}

if (isset($_POST["conteoSerie"])) {

    $conteoSerie = (isset($_POST["conteoSerie"]) ? $_POST["conteoSerie"] : "");
    $jugador = (isset($_POST["jugador"]) ? $_POST["jugador"] : "");

    if($jugador == "jugador1"){
        if($conteoSerie > $lista_puntos['max_serie1']){
            $sentencia = $conexion->prepare("UPDATE puntos SET
            max_serie1=:jugador1
            WHERE id_punto=:id_punto");
            $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
            $sentencia->bindParam(":jugador1", $conteoSerie);
            $sentencia->execute();
        }
    }

    if($jugador == "jugador2"){
        if($conteoSerie > $lista_puntos['max_serie2']){
            $sentencia = $conexion->prepare("UPDATE puntos SET
            max_serie2=:jugador2
            WHERE id_punto=:id_punto");
            $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
            $sentencia->bindParam(":jugador2", $conteoSerie);
            $sentencia->execute();
        }
    }

    $sentencia = $conexion->prepare("UPDATE puntos SET
    serie=:conteoSerie
    WHERE id_punto=:id_punto");
    $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
    $sentencia->bindParam(":conteoSerie", $conteoSerie);
    $sentencia->execute();
}

if (isset($_POST["valorEntrada"])) {
    $sentencia = $conexion->prepare("UPDATE puntos SET
    entrada = entrada + 1
    WHERE id_punto=:id_punto");
    $sentencia->bindParam(":id_punto", $lista_puntos['id_punto']);
    $sentencia->execute();
}

if (isset($_POST["idCuentaDetener"])) {

    $comando = (isset($_POST["comando"]) ? $_POST["comando"] : "");
    $estado_video = (isset($_POST["estado_video"]) ? $_POST["estado_video"] : "");

    $sentencia = $conexion->prepare("UPDATE cuentas SET
    estado_video=:estado_video
    WHERE id_cuenta=:id_cuenta");
    $sentencia->bindParam(":id_cuenta", $txtID);
    $sentencia->bindParam(":estado_video", $estado_video);
    $sentencia->execute();

    //if($comando == "stop.bat"){

    // $contenido = '@echo off
    // taskkill /IM ffmpeg.exe /F
        
    // rem Borra los archivos .ts
    // del /Q "' . $lista_cuenta['cam'] . '\*.ts"
        
    // rem Borra el archivo .m3u8 en la ruta ..\ffmpeg\
    // del /Q "' . $lista_cuenta['cam'] . '\stream.m3u8"';

    // }else{
    //     $contenido = "@echo off
    //     ffmpeg -v verbose -i " . $lista_cuenta['url'] . " -vf scale=1920:1080  -vcodec libx264 -r 25 -b:v 1000000 -crf 31 -acodec aac  -sc_threshold 0 -f hls  -hls_time 5  -segment_time 5 -hls_list_size 5 " . $lista_cuenta['cam'] . "\stream.m3u8";
    // }
    
    // file_put_contents("../libs/ffmpeg/bin/" . $comando, $contenido);

    // exec($comando, $output, $retorno);

    // if ($retorno !== 0) {
    //     echo "Error al ejecutar el archivo Batch.";
    //     echo "Código de retorno: " . $retorno;
    // } else {
    //     echo "Archivo Batch ejecutado con éxito.";
    // }

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
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script type="text/javascript" src="../jsmpeg.min.js"></script>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100vh;
            background-image: url('../images/fondo.jpg');
            background-size: cover;
            background-position: center;
            user-select: none;
            font-family: "Rajdhani";
            text-shadow: 0 0 15px rgba(255, 0, 0, 0.6);
        }
        .cronometro-texto {
            display: flex;
            justify-content: center;
            align-items: top;
            height: 100%;
            font-size: 5vw;
        }
        .with-background {
            position: relative;
            background-size: 100% 100%;
            background-position: center;
            text-align: center;
            height: auto;
        }
        .image-container {
            position: relative;
        }

        .centered-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
            font-size: 50px;
            color: white;
        }
        .table {
            color: white;
        }
        .image-container {
            position: relative;
        }
        .image-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18vw;
        }
        .text-center{
            font-size: 30px;
        }
        .click{
            font-size: 35px;
            color: white;
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* Proporción 16:9, puedes ajustarlo según la relación de aspecto de tu video */
            height: 0;
            overflow: hidden;
            max-width: 100%; /* Ajusta esto según el ancho máximo que desees para el video */
        }

        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .canvas{
            width: 100%
        }

    </style>
</head>
<body>
        <div class="container-fluid vh-100 d-flex justify-content-center align-items-top p-0">
            <div class="row w-100">
                <table class="table table-borderless">
                    <tr style="max-height: 30px;">
                        <td rowspan="6" colspan="4" class="col-3">
                            <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', 1); ConteoSerie(1, 'jugador1', '<?php echo $txtID;?>'); 
                            ConteoEntrada(1, '<?php echo $txtID;?>');">
                            <div class="image-container">
                            <img src="../images/bola_blanca.png" class="img-fluid" alt="Bola Blanca" id="bola-blanca">
                            <p class="image-text" id="valor1" style="color: black;"><?php echo $lista_puntos['puntos_jugador1']?></p>
                        </div>
                            </a>
                        </td>
                        <td colspan="3" class="col-6 text-center">
                            <?php echo $lista_tiempos['nombre_cuenta'];?>
                            <a href="result_jugador2.php?txtID=<?php echo $txtID;?>">
                                <img src="../images/end3.png" alt="end">
                            </a>
                        </td>

                        <td rowspan="6" colspan="4" class="col-3">
                            <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', 1); ConteoSerie(1, 'jugador2', '<?php echo $txtID;?>'); 
                            ConteoEntrada(1, '<?php echo $txtID;?>');">
                            <div class="image-container">
                                <img src="../images/bola_amarilla.png" class="img-fluid" alt="Bola Amarilla" id="bola-amarilla">
                                <p class="image-text" id="valor2" style="color: black;"><?php echo $lista_puntos['puntos_jugador2']?></p>
                            </div>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="col-6">
                            <div class="cronometro-texto" id="cronometro">00 : 00 : 00</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-1" rowspan="3"><img class="img-fluid" src="../images/jugadorr.png"></td>
                        <td class="col-4 text-center">Serie</td>
                        <td class="col-1" rowspan="3"><img class="img-fluid" src="../images/jugadorr.png"></td>
                    </tr>
                    <tr>
                        <td class="col-4 text-center"><p id="valorSerie"><?php echo $lista_puntos['serie']?></p></td>
                    </tr>
                    <tr>
                        <td class="col-4 text-center">Entrada</td>
                    </tr>
                    <tr>
                        <td class="col-1 text-center"><?php echo $lista_puntos['jugador1']?></td>
                        <td class="col-4 text-center">
                            <img src="../images/atrasar2.png" alt="Imagen 1" onclick="retrocederVideo(30)">
                            <img src="../images/atrasar1.png" alt="Imagen 2" onclick="retrocederVideo(15)"> -
                            <img src="../images/menosentrada3.png" alt="Menos Entrada" onclick="Entrada('<?php echo $txtID;?>', -1)">
                            <a id="entrada"><?php echo $lista_puntos['entrada']?></a>
                            <img src="../images/masentrada3.png" alt="Mas Entrada" onclick="Entrada('<?php echo $txtID;?>', 1)"> -
                            <?php
                                if($lista_cuenta['estado_video'] == 0){
                                    ?> <img src="../images/play2.png" id="play" alt="Imagen 3" onclick="Stream('<?php echo $txtID;?>', 'newcam.bat', '1')"> <?php
                                }else{
                                    ?> <img src="../images/detener.png" id="stop" alt="Imagen 4" onclick="Stream('<?php echo $txtID;?>', 'stop.bat', '0')"> <?php
                                }
                            ?>
                            <img src="../images/live.png" alt="Imagen 4" onclick="enVivo()">
                        </td>
                        <td class="col-1 text-center"><?php echo $lista_puntos['jugador2']?></td>
                    </tr>


                    <tr>
                        <td colspan="1" class="col-1 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', -1);">
                                <img class="img-fluid" src="../images/menos.png">
                                <p class="centered-text" style="color: black;">-1</p>
                            </div>
                        </td>
                        <td colspan="2" class="col-2 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', 2);">
                                <img class="img-fluid" src="../images/mas26.png">
                                <p class="centered-text">+2</p>
                            </div>
                        </td>
                        <td colspan="5" rowspan="3" class="col-6">
                            <div class="video-container">
                                <canvas id="canvas"></canvas>
                            </div>
                        </td>

                        <td colspan="1" class="col-1 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', -1);">
                                <img class="img-fluid" src="../images/menos.png">
                                <p class="centered-text" style="color: black;">-1</p>
                            </div>
                        </td>
                        <td colspan="2" class="col-2 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', 2);">
                                <img class="img-fluid" src="../images/mas26.png">
                                <p class="centered-text">+2</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="col-2 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', 3);">
                                <img class="img-fluid" src="../images/mas3.png">
                                <p class="centered-text">+3</p>
                            </div>
                        </td>
                        <td colspan="1" class="col-1 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', 4);">
                                <img class="img-fluid" src="../images/mas4.png">
                                <p class="centered-text">+4</p>
                            </div>
                        </td>
                        <td colspan="2" class="col-2 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', 3);">
                                <img class="img-fluid" src="../images/mas3.png">
                                <p class="centered-text">+3</p>
                            </div>
                        </td>
                        <td colspan="1" class="col-1 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', 4);">
                                <img class="img-fluid" src="../images/mas4.png">
                                <p class="centered-text">+4</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" class="col-1 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', 5);">
                                <img class="img-fluid" src="../images/mas5.png">
                                <p class="centered-text">+5</p>
                            </div>
                        </td>
                        <td colspan="2" class="col-2 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor1('<?php echo $txtID;?>', 6);">
                                <img class="img-fluid" src="../images/mas26.png">
                                <p class="centered-text">+6</p>
                            </div>
                        </td>
                        <td colspan="1" class="col-1 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', 5);">
                                <img class="img-fluid" src="../images/mas5.png">
                                <p class="centered-text">+5</p>
                            </div>
                        </td>
                        <td colspan="2" class="col-2 with-background">
                            <div class="image-container">
                                <a class="table-button" onclick="incrementarValor2('<?php echo $txtID;?>', 6);">
                                <img class="img-fluid" src="../images/mas26.png">
                                <p class="centered-text">+6</p>
                            </div>
                        </td>   
                    </tr>
                </table>
            </div>
        </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>

        
        var jugadorGlobal = "";
        var conteoSerie = 0;
        var maxSerie1 = 0;
        var maxSerie2 = 0;

        var bolaAmarilla = document.getElementById('bola-amarilla');
        var bolaRoja = document.getElementById('bola-roja');

        $(document).ready(function() {
            if(<?php echo $lista_tiempos['estado_tiempo'] ?> == 1){
                var fechaBaseDeDatos = new Date("<?php echo $lista_tiempos['fecha_inicio']; ?>").getTime();

                function actualizarCronometro() {
                    var fechaActual = new Date().getTime();
                    var tiempoTranscurrido = fechaActual - fechaBaseDeDatos;

                    var horas = Math.floor(tiempoTranscurrido / (1000 * 60 * 60));
                    tiempoTranscurrido %= (1000 * 60 * 60);
                    var minutos = Math.floor(tiempoTranscurrido / (1000 * 60));
                    tiempoTranscurrido %= (1000 * 60);
                    var segundos = Math.floor(tiempoTranscurrido / 1000);

                    var tiempoFormateado = (horas < 10 ? "0" : "") + horas + " : " + (minutos < 10 ? "0" : "") + minutos + " : " + (segundos < 10 ? "0" : "") + segundos;

                    $("#cronometro").text(tiempoFormateado);
                }
                setInterval(actualizarCronometro, 1000);
            }
        });

        function incrementarValor1(idCuenta1, valorSuma) {
            let valorElement = document.getElementById("valor1");
            let valor = parseInt(valorElement.innerText);
            valor = valor + valorSuma;
            limite1 = <?php echo $lista_puntos['limit_jugador1']; ?>;

            valorElement.innerText = valor;
            $.ajax({
                url: 'jugador2.php?txtID=' + idCuenta1,
                method: 'POST',
                data: { idCuenta1: idCuenta1, valorSuma: valorSuma },
                success: function(response) {
                
                },
                    error: function(xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                }
            });

            if (valor >= limite1) {
                $.ajax({
                    url: 'result_jugador2.php?txtID=' + idCuenta1,
                    method: 'POST',
                    data: { limite1: limite1 },
                    success: function(response) {
                        window.location.href = 'result_jugador2.php?txtID=' + idCuenta1;
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log(xhr.responseText);
                    }
                });
            }
        }

        function incrementarValor2(idCuenta2, valorSuma) {
            let valorElement = document.getElementById("valor2");
            let valor = parseInt(valorElement.innerText);
            valor = valor + valorSuma;
            limite2 = <?php echo $lista_puntos['limit_jugador2']; ?>;

            valorElement.innerText = valor;
            $.ajax({
                url: 'jugador2.php?txtID=' + idCuenta2,
                method: 'POST',
                data: { idCuenta2: idCuenta2, valorSuma: valorSuma },
                success: function(response) {
                
                },
                    error: function(xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                }
            });

            if (valor >= limite2) {
                $.ajax({
                    url: 'result_jugador2.php?txtID=' + idCuenta2,
                    method: 'POST',
                    data: { limite2: limite2 },
                    success: function(response) {
                        window.location.href = 'result_jugador2.php?txtID=' + idCuenta2;
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log(xhr.responseText);
                    }
                });
            }
        }

        function Entrada(idCuenta_entrada, valorSuma) {
            let valorElement = document.getElementById("entrada");
            let valor = parseInt(valorElement.innerText);
            valor = valor + valorSuma;

            valorElement.innerText = valor;
            $.ajax({
                url: 'jugador2.php?txtID=' + idCuenta_entrada,
                method: 'POST',
                data: { idCuenta_entrada: idCuenta_entrada, valorSuma: valorSuma },
                success: function(response) {
                
                },
                    error: function(xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                }
            });
        }

        function ConteoSerie(conteo, jugador, idCuenta) {

            var valorSerieElement = document.getElementById("valorSerie");

            if (jugadorGlobal != jugador) {
                conteoSerie = 0;
                jugadorGlobal = jugador;
                valorSerieElement.textContent = conteoSerie;
            }

            if (jugador == "jugador1") {
                jugadorGlobal = jugador;
                conteoSerie = conteoSerie + conteo;
                if (conteoSerie >= 0) {
                    valorSerieElement.textContent = conteoSerie;
                }
                if (conteoSerie >= maxSerie1) {
                    maxSerie1 = conteoSerie;
                }
            }

            if (jugador == "jugador2") {
                jugadorGlobal = jugador;
                conteoSerie = conteoSerie + conteo;
                if (conteoSerie >= 0) {
                    valorSerieElement.textContent = conteoSerie;
                }
                if (conteoSerie >= maxSerie2) {
                    maxSerie2 = conteoSerie;
                }
            }
            $.ajax({
                url: 'jugador2.php?txtID=' + idCuenta,
                method: 'POST',
                data: { conteoSerie: conteoSerie, jugador: jugador },
                success: function(response) {
                
                },
                    error: function(xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                }
            });
        }

        function ConteoEntrada(conteo, idCuenta) {
            serie = parseInt(conteoSerie);
            if (conteo == serie){
                var valorElement = document.getElementById("entrada");
                var valorEntrada = parseInt(valorElement.innerText);
                valorEntrada++;
                valorElement.textContent = valorEntrada;
            }
            $.ajax({
                url: 'jugador2.php?txtID=' + idCuenta,
                method: 'POST',
                data: { valorEntrada: valorEntrada},
                success: function(response) {
                
                },
                    error: function(xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                }
            });
        }
        
        if(Hls.isSupported()){
			var video = document.getElementById('video');
			var hls = new Hls();
			hls.attachMedia(video);
			hls.on(Hls.Events.MEDIA_ATTACHED, function (){
				console.log("video and hls.js are now bound together !");
                var url = "../ffmpeg/" + "<?php echo substr($lista_cuenta['cam'], -4);?>" + "/stream.m3u8" ;
				hls.loadSource(url);
				hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
					console.log("manifest loaded, found " + data.levels.length + " quiality level");
				});
			});
		}

        function Stream(idCuentaDetener, comando, estado_video) {
            
            var texto = "";
            var texto2 = "";

            if(estado_video == "1"){
                texto = "iniciar";
                texto2 = "inicio";
            }else{
                texto = "detener";
                texto2 = "detuvo";
            }

            var confirmacion = confirm("¿Desea " + texto + " la transmisión?");

            if (confirmacion) {
                $.ajax({
                    url: 'jugador2.php?txtID=' + idCuentaDetener,
                    method: 'POST',
                    data: { idCuentaDetener: idCuentaDetener, comando: comando, estado_video: estado_video },
                    success: function(response) {
                        setTimeout(function(){
                            if(estado_video == "1"){
                                fetch("http://localhost:7000/start-stream?port=9000&url=rtsp://888888:888888@192.168.1.38:554?channel=1");
                            }else{
                                fetch("http://localhost:7000/stop-stream?port=9000");
                            }
                            alert("Se " + texto2 + " la transmisión");
                            location.reload();                        
                        }, 1500);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log(xhr.responseText);
                    }
                });
            } else {
                alert("La transmisión no se " + texto2 + ".");
            }
        }

        function enVivo() {
            var video = document.getElementById('video');
            video.currentTime = video.duration -5;
            if (video.paused) {
                video.play();
            }
        }

        function retrocederVideo(tiempo) {
            var video = document.getElementById('video');

            var nuevoTiempo = video.currentTime - tiempo;

            if (nuevoTiempo < 0) {
                nuevoTiempo = 0;
            }

            video.currentTime = nuevoTiempo;

            if (video.paused) {
                video.play();
            }
        }

        function adelantarVideo() {
            
            var video = document.getElementById('video');

            var nuevoTiempo = video.currentTime + 15;

            if (nuevoTiempo > video.duration) {
                nuevoTiempo = video.duration;
            }

            video.currentTime = nuevoTiempo;

            if (video.paused) {
                video.play();
            }
        }

    </script>

    <script type="text/javascript">

        player = new JSMpeg.Player('ws://localhost:9000', {
            canvas: document.getElementById('canvas') // Canvas should be a canvas DOM element
        });

    </script>

</body>
</html>
