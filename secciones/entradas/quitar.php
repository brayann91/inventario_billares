
<?php

include "../../bd.php";

if ($_POST) {

    //recolectamos los datos del metodo POST
    $cantidad = (isset($_POST["cantidad"]) ? $_POST["cantidad"] : "");
    $precio_total = (isset($_POST["precio_total"]) ? $_POST["precio_total"] : "");
    $id_producto = (isset($_POST["producto"]) ? $_POST["producto"] : "");

    //Preparar la insersion de los datos
    $sentencia = $conexion->prepare("SELECT id_producto, cantidad from entradas");
    $sentencia->execute();
    $resultado_consulta = $sentencia->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultado_consulta as $registro) {

        if ($registro['id_producto'] == $id_producto) {
            $valor = $registro['id_producto'];
            $valor_cantidad = $registro['cantidad'];
        } else {
            $valor = 0;
        }
    }

        $sentencia = $conexion->prepare("INSERT INTO entradas(cantidad, precio_total, precio_venta, fecha, id_producto)
    VALUES (-:cantidad, -(SELECT precio_compra FROM productos WHERE id_producto = :producto)*:cantidad, 
    -(SELECT precio FROM productos WHERE id_producto = :producto)*:cantidad, CURRENT_TIMESTAMP(), :producto)");
        $sentencia->bindParam(":cantidad", $cantidad);
        $sentencia->bindParam(":precio_total", $precio_total);
        $sentencia->bindParam(":producto", $id_producto);
        $sentencia->execute();

    $mensaje = "Se desconto la cantidad de productos Exitosamente";
    header("Location:index.php?mensaje=" . $mensaje);
}

$cantidad = (isset($_POST["cantidad"]) ? $_POST["cantidad"] : "");

?>

<?php include "../../templates/header.php";?>

<br/>

   <div class="card">
     <div class="card-header">
         Quitar Entrada de Productos
     </div>
     <div class="card-body">

         <form action="" method="post" enctype="multipart/form-data">

            <?php 
            // Formulario

            $sentencia = $conexion->prepare("SELECT id_categoria, nombre_categoria from categorias WHERE id_sede = " . $_SESSION['id_sede'] . "");
            $sentencia->execute();
            $resultado_categorias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            echo '<label for="id_proovedor" class="form-label">Seleccione la Categoria:</label>';
            echo '<select required class="form-select form-select-sm" name="categoria" id="categoria">';
            echo '<option value="">Seleccione una categoría</option>';
            
            // Generar las opciones del primer select
            foreach ($resultado_categorias as $row_categoria) {
                echo '<option value="'.$row_categoria['id_categoria'].'">'.$row_categoria['nombre_categoria'].'</option>';
            } 
            
            echo '</select>';

            // Generar el segundo select de productos (vacío por defecto)
            echo '</br><label for="id_proovedor" class="form-label">Seleccione el Producto:</label>';
            echo '<select required class="form-select form-select-sm" name="producto" id="producto">';
            echo '<option value="">Seleccione un producto</option>';
            echo '</select>';

            echo '<script>
            var categoria_select = document.getElementById("categoria");
            var producto_select = document.getElementById("producto");

            categoria_select.addEventListener("change", function() {
                // Limpiar el select de productos
                producto_select.innerHTML = \'<option value="">Seleccione un producto</option>\';

                // Cargar los productos correspondientes a la categoría seleccionada
                var id_categoria = categoria_select.value;
                if (id_categoria !== "") {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "includes/getProducto.php?id_categoria=" + id_categoria, true);
                xhr.onreadystatechange = function() {
                    var productos = "";
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        productos = JSON.parse(xhr.responseText);                    
                    for (var i = 0; i < productos.length; i++) {
                        var option = document.createElement("option");
                        option.value = productos[i].id_producto;
                        option.innerHTML = productos[i].nombre_producto;
                        option.setAttribute("data-categoria", productos[i].id_categoria);
                        producto_select.appendChild(option);
                    }
                    }
                };
                xhr.send();
                }
            });
            </script>';

            ?>
            </br>
             <div class="mb-3">
               <label for="cantidad" class="form-label">Cantidad de productos que desea quitar</label>
               <input type="text" style="color: #87CEEB"
                 class="form-control" required name="cantidad" id="cantidad" aria-describedby="helpId" placeholder="0">
             </div>

             <br/>

             <button type="submit" class="btn btn-danger">Quitar Stock?</button>
             <a name="" id="" class="btn btn-secondary" href="index.php" role="button">Cancelar</a>

         </form>


     </div>
   </div>

<?php include "../../templates/footer.php";?>