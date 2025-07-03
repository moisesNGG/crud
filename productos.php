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

$mostrar_modal = false;

//Switch para manejar las diferentes acciones que se toman en el formulario
switch ($accion) {
    // Insertar los datos a mi Base de Datos
    case "boton_agregar":
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
            break;
        }
        // Si no hay errores, se prepara y ejecuta la sentencia SQL para insertar el item a la base de datos
        $sentencia = $pdo->prepare("INSERT INTO items (name, description) VALUES (:name, :description)");
        $sentencia->bindParam(":name", $text_nombreProducto);
        $sentencia->bindParam(":description", $text_descripcion);
        $sentencia->execute();
        header('Location: index.php');
        break;

    //Actualizar los datos de Nombre o Producto de un registro
    case "boton_actualizar":
        // Prepara la sentencia SQL para actualizar los datos
        $sentencia = $pdo->prepare("UPDATE items SET name = :name, description = :description WHERE id = :id");
        $sentencia->bindParam(":name", $text_nombreProducto);
        $sentencia->bindParam(":description", $text_descripcion); 
        $sentencia->bindParam(":id", $text_id); 
        $sentencia->execute();
        header('Location: index.php');
        break;

    //Borrar un registro de la BD
    case "boton_borrar":
        // Prepara la sentencia SQL para borrar el producto por su ID
        $sentencia = $pdo->prepare("DELETE FROM items WHERE id = :id");
        $sentencia->bindParam(":id", $text_id); // Vincula el ID del producto a borrar
        $sentencia->execute();
        header('Location: index.php');
        break;

     // Caso para cancelar una acción
    case "boton_cancelar":
        header('Location: index.php'); // Redirige a la página principal
        break;

    // Caso para activar la visualización del modal de edición
    case "Editar":
        $mostrar_modal= true;
        break; 

 

}





?>