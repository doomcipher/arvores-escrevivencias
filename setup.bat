@echo off
setlocal

set "XAMPP_DIR=C:\xampp"
set "APACHE_CONF=%XAMPP_DIR%\apache\conf\httpd.conf"
set "NEW_DOCROOT=C:/xampp/htdocs/arvores-escrevivencias/public"

echo [1/3] Composer install...
cd /d "%~dp0"
call composer install
if errorlevel 1 (
  echo Falhou o composer install.
  pause
  exit /b 1
)

echo [2/3] Backup do httpd.conf...
copy "%APACHE_CONF%" "%APACHE_CONF%.bak" >nul

echo [3/3] Atualizando DocumentRoot e Directory correspondente...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0set-docroot.ps1" -ApacheConf "%APACHE_CONF%" -NewDocRoot "%NEW_DOCROOT%"
if errorlevel 1 (
  echo ERRO: falhou ao alterar o httpd.conf (tente rodar como Administrador)
  pause
  exit /b 1
)

echo Pronto reinicie o Apache no XAMPP Control Panel
pause
