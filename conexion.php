<?php
$servidor = "mysql:dbname=crud_db;host:127.0.0.1";
$usuario = "crud";
$password = "crud";

try {
    $pdo = new PDO($servidor, $usuario, $password);
    echo " BASE DE DATOS CONECTADA CORRECTAMENTE. ";

} catch (PDOException $e){
    echo " ERROR EN LA CONEXION!! " .$e->getMessage();
}
?>