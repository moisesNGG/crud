
<?php

// Conectar la Base de Datos
include("./conexion.php");


$sentencia2 = $pdo->prepare("SELECT * FROM items WHERE 1"); //Sentencia SQL para seleccionar todos los productos
$sentencia2->execute();
$lista_items = $sentencia2->fetchAll(PDO::FETCH_ASSOC); // Obtener un array de items desde la base de datos
?>

<table class="table table-striped">
                <thead>
                    <th> ID </th>
                    <th> NOMBRE </th>
                    <th> DESCRIPCION </th>
                    <th> Creado el </th>
                    <th> Opciones </th>
                </thead>
                <!-- Script PHP que muestra los campos: id, name, description y created_at de la tabla items.  -->
                <?php foreach ($lista_items as $items) { ?>
                    <tr>
                        <td> <?php echo $items['id'] ?> </td>
                        <td> <?php echo $items['name'] ?> </td>
                        <td> <?php echo $items['description'] ?> </td>
                        <td> <?php echo $items['created_at'] ?> </td>
                        <td>
                            <!-- Formulario para editar cada producto -->
                            <form action="" method="post">
                                <input type="hidden" name="text_id" value="<?php echo $items['id']; ?>">
                                <input type="hidden" name="text_nombreProducto" value="<?php echo $items['name']; ?>">
                                <input type="hidden" name="text_descripcion" value="<?php echo $items['description']; ?>">
                                <input type="hidden" name="text_created_at" value="<?php echo $items['created_at']; ?>">
                                <!-- Colocar el boton "Editar" y "Borrar" junto a cada elemento de la lista -->
                                <input id="editar" type="button" value="Editar" class="btn btn-success " name="accion">
                                <!-- Confirmacion antes de borrar un elemento -->
                                <button value="boton_borrar" onclick=" return confirmar_borrado('¿Realmente deseas borrar el registro?'); " class="btn btn-danger" type="submit" name="accion"> Borrar </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>