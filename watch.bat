@echo off
set currentDir=%cd%
set destDir=%1
set patternRegex=^.env$^|^.htaccess$^|.*\.(php^|js^|html^|css^|scss^|mjs^|txt^|json^|png^|jpg^|jpeg^|gif^|webp^|sql^|onnx^)$
pwat.exe "%currentDir%" "%destDir%" "%patternRegex%"
pause
