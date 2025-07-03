function loadItems(){
    $.ajax({
        url: 'listarProductos.php',
        method: 'GET',
        success: function (response){
            $('#contenidoCrud').html(response);
            
        }
    });
}



$(document).ready(function(){
    //Cargar la tabla del CRUD
    loadItems();

    $('#agregar').on('click',function(e){
        e.preventDefault();
        const formData = $('#formulario').serialize();
        $.ajax({
            url: 'agregar.php',
            method: 'POST',
            data: formData,
            success: function (response){
                $('#mensajes').html(response);
                loadItems();
                $('#exampleModal').modal('hide');
            }
        });
    })

    $('#actualizar').click(function(){
        console.log("Actualizar");
    })

    $('#cancelar').click(function(){
        console.log("Cancelar");
    })
    
})