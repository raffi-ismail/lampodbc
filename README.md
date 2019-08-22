# Ubuntu 18.04, Apache 2 and PHP7.2 with ODBC Drivers

Updated to run on mod_php, instad of php-frpm

Not technically LAMP (no MySQL in this Dockerfile).
Deploy a custom container on Azure App Service which supports ODBC drivers required to connect to Microsoft SQL Servers and Azure SQL Services.

Supports (on Azure via custom container on Web Apps):
* SSH
* Configuration (via environment variables)
* ODBC, sqlsrv functions in PHP

Most configs (PHP, Apache etc) are mostly still defaults. 
DocumentRoot is still at /var/www/html



// Released as copyleft, GPLv3


