<html>
<head>
    <title>Linux, Apache and PHP with ODBC</title>
</head>
<body>
    <div style="text-align:center">
        <h1>It Works!</h1>
        You have successfully deployed this container running 
        <p><i><?php echo $_SERVER['SERVER_SIGNATURE'];?></i></p>
        <p>About <a target="_blank" href="https://www.php.net/manual/en/book.sqlsrv.php">Microsoft SQL Server Driver for PHP</a></p>
        <p>Connecting PHP applications to Azure SQL <a target="_blank" href="https://docs.microsoft.com/en-us/azure/sql-database/sql-database-connect-query-php">Quickstart Guide</a></p>
        <p>The Dockerfile, <a target="_blank" href="https://github.com/raffi-ismail/lampodbc">raffi-ismail/lampodbc</a> on GitHub</p>
        <p>The container, <a target="_blank" href="https://hub.docker.com/r/chubbycat/lampodbc">chubbycat/lampodbc</a> on DockerHub</p>
    </div>
    <?php phpinfo()?>
</body>
</html>