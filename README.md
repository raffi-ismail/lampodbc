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

The additional Code-Server binary is included if you want to run the Node-JS version of Visual Code in a browser. But since Azure App Services does not support ports other than HTTP 80 and HTTP 443, this could work if you ran Code-Server on one port, and Apache on the other.

Alternatively you could deploy two containers. One running Code-Server, and this one. On Azure, you would deploy these as two staging slots or two App Services.


// Released as copyleft, GPLv3
