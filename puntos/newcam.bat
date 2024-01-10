@echo off
         ffmpeg -f gdigrab -framerate 30 -i desktop -c:v libx264 -preset ultrafast -tune zerolatency -c:a aac -strict experimental -f hls ..\ffmpeg\Cam1\stream.m3u8