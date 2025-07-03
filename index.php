<?php
require 'productos.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> EJERCICIO CRUD SIMPLE </title> 
    <!-- Cargar Bootstrap para los estilos y componentes -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="ajax.js"></script>
</head>

<body>
 
    <div class="card bg-light shadow mb-4 border-primary">
        <div class="card-body">
            <h3 class="card-title"> Bienvenido al Sistema </h3>
            <h5 class="card-subtitle mb-2 text-muted">Ingresa los Items:</h5>
        </div>
    </div>
    <div id="mensajes" >

    </div>
    <div class="container">
        <!-- Formulario HTML para añadir nuevos items a la tabla -->
        <form id="formulario" action="" method="post" enctype="multipart/form-data">

            <!-- Modal para Agregar/Editar items  -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> ITEMS </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Input oculto para almacenar el ID del producto -->
                        <input type="hidden" name="text_id" required value="<?php echo $text_id; ?>" id="text_id" require="">

                            <!-- Campos del Formulario para Nombre y Descripcion -->

                            <label for=""> Nombre del Producto: </label>
                            <input type="text" class="form-control <?php echo (isset($errores['Nombre_Producto']))? "is-invalid":""; ?>"  name="text_nombreProducto" required value="<?php echo $text_nombreProducto; ?>" id="text_nombreProducto" require="">
                            <!-- Valida la entrada del nombre del producto -->
                            <div class="invalid-feedback">
                                <?php echo (isset($errores['Nombre_Producto']))? $errores['Nombre_Producto']:"" ; ?>
                            </div>
                            <br>
                            
                            <label for=""> Descripcion: </label>
                            <input type="text" class="form-control <?php echo (isset($errores['Descripcion_Producto']))? "is-invalid":""; ?>" name="text_descripcion" required value="<?php echo $text_descripcion; ?>" id="text_descripcion" require="">
                            <!-- Valida la entrada de la descripcion del producto -->
                            <div class="invalid-feedback">
                                <?php echo (isset($errores['Descripcion_Producto']))? $errores['Descripcion_Producto']:"" ; ?>
                            </div>
                            <br>
                    
                    </div>

                    

                    <div class="modal-footer">
                            <!-- Si se hace click en el boton Editar, se crea un formulario HTML para editar, precargando los valores originales -->
                            <?php if ($accion == "Editar") { ?>
                                <button id="actualizar" type="button"  class="btn btn-primary "> Actualizar </button>
                                <button id="cancelar" class="btn btn-danger " type="button"> Cancelar </button>
                            <?php } else { ?>
                                <!-- Se muestra la opcion para agregar -->
                                <button id="agregar"  class="btn btn-primary " type="button"> Agregar </button>      
                            <?php } ?>
                    </div>
                    </div>
                </div>
            </div>

           <!-- Button trigger modal -->
           <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"> AGREGAR ITEM +  </button>

        </form>

        <!-- Tabla HTML de Items con su informacion -->
        <div class="row" id="contenidoCrud">
            

        </div>
        <?php if ($mostrar_modal) { ?>
            <script> 
                $('#exampleModal').modal('show');
            </script>
        <?php } ?> 
        <script>
            function confirmar_borrado(Mensaje){
                return (confirm(Mensaje))? true:false;
            }
        </script>
    </div>
</body>
</html>
