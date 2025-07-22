<?php
define('DB_SERVER', 'localhost'); 
define('DB_USERNAME', 'nathan.carrasco');    
define('DB_PASSWORD', 'Pyro1721');       
define('DB_NAME', 'capital_humano'); 

// Intentar conectar a la base de datos
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if($link === false){
    die("ERROR: No se pudo conectar a la base de datos. " . mysqli_connect_error());
}
?>