@echo off
echo Configurando Gmail para Laravel...

echo.
echo # GMAIL CONFIGURATION >> .env
echo MAIL_MAILER=smtp >> .env
echo MAIL_HOST=smtp.gmail.com >> .env
echo MAIL_PORT=587 >> .env
echo MAIL_USERNAME=221217@ids.upchiapas.edu.mx >> .env
echo MAIL_PASSWORD=TU_CONTRASEÑA_DE_APLICACION_AQUI >> .env
echo MAIL_ENCRYPTION=tls >> .env
echo MAIL_FROM_ADDRESS=221217@ids.upchiapas.edu.mx >> .env
echo MAIL_FROM_NAME="Sistema de Usuarios" >> .env

echo.
echo ¡Configuracion de Gmail agregada!
echo.
echo IMPORTANTE: Cambia TU_CONTRASEÑA_DE_APLICACION_AQUI por tu contraseña real de Gmail
echo.

