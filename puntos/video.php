<?php

require '../libs/vendor/autoload.php'; // Include the composer autoloader


use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;

// Crear una instancia de FFMpeg
$ffmpeg = FFMpeg::create();

// Abrir el flujo RTSP (reemplaza 'rtsp://tu_url_del_video_rtsp' con la URL real)
$video = $ffmpeg->open('rtsp://888888:888888@192.168.1.38:554?channel=1');

// Establecer el tipo de contenido del encabezado (cambia si el flujo no es MP4)
header('Content-Type: video/mp4');

// Transmitir el video en tiempo real (en este ejemplo, solo se muestra el primer cuadro)
echo $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0))->getData();

error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Puedes agregar una lógica para controlar la reproducción o un límite de tiempo aquí
// while (condición_para_seguir_reproduciendo) {
//     echo $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0))->getData();
// }

// Cierra la transmisión cuando sea necesario
// fclose($video);
?>
