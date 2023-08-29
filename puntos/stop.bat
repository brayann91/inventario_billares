@echo off
    taskkill /IM ffmpeg.exe /F
        
    rem Borra los archivos .ts
    del /Q "..\ffmpeg\Cam1\*.ts"
        
    rem Borra el archivo .m3u8 en la ruta ..\ffmpeg\
    del /Q "..\ffmpeg\Cam1\stream.m3u8"