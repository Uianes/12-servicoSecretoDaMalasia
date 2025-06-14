<?php
function open_connection() {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'coderace25';
    
    // Cria conexão
    $connection = mysqli_connect($host, $username, $password, $database);
    
    // Verifica conexão
    if (!$connection) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Define Charset
    mysqli_set_charset($connection, "utf8mb4");
    
    return $connection;
}


function close_connection($connection) {
    if ($connection) {
        mysqli_close($connection);
        $connection = null;
    }
}