@echo off
cls
set certDir=mkcert
set /p certDir=local cert directory (default - %certDir%):

mkdir %certDir%

set domain=pohodnik.tk
set /p domain=domain (default - %domain%):


docker run -d -e domain=%domain% --name mkcert -v %certDir%:/root/.local/share/mkcert vishnunair/docker-mkcert
@pause
docker cp mkcert:/root/.local/share/mkcert .
color 0a

cls
echo Your certs in folder ./%certDir%
@pause