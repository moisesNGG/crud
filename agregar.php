<?php

// Primero se guardan las variables que ingresa el usuario mediante el metodo $_POST
$text_nombreProducto = (isset($_POST["text_nombreProducto"])) ? $_POST["text_nombreProducto"] : "";
$text_descripcion = (isset($_POST["text_descripcion"])) ? $_POST["text_descripcion"] : "";
$accion = (isset($_POST["accion"])) ? $_POST["accion"] : "";
$text_id = (isset($_POST["text_id"]))? $_POST["text_id"]: "";

// Conectar la Base de Datos
include("./conexion.php");

//Array de Errores para validar las entradas
$errores = array();

        //Se verifican que los campos no estén vacíos
        if($text_nombreProducto==""){
            $errores['Nombre_Producto'] = "Ingresa el nombre del producto";
        }
        if($text_descripcion==""){
            $errores['Descripcion_Producto'] = "Ingresa la Descripcion";
        }
        // Si hay errores, se muestra el modal para que el usuario corrija la entrada
        if(count($errores)>0){
            $mostrar_modal = true;
            //DEVOLVER MENSAJE ERROR
            print ("<p> Error</p>");
            
        }
        else{
        // Si no hay errores, se prepara y ejecuta la sentencia SQL para insertar el item a la base de datos
        $sentencia = $pdo->prepare("INSERT INTO items (name, description) VALUES (:name, :description)");
        $sentencia->bindParam(":name", $text_nombreProducto);
        $sentencia->bindParam(":description", $text_descripcion);
        $sentencia->execute();
        print ("<p> Item insertado</p>");
        }
    
?>