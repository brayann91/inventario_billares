<?php
session_start();
$url_base = "http://inventario-billar.net/";
if (!isset($_SESSION['usuario'])) {
    header("Location:" . $url_base . "login.php");
}

$url_actual = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

$url_productos = "inventario-billar.net/secciones/productos/";
$url_usuarios = "inventario-billar.net/secciones/usuarios/";
$url_home = "inventario-billar.net/index.php";

$url_editar = "editar.php";
$url_crear = "crear.php";

if ($_SESSION['id_cargo'] == 2 && str_contains($url_actual, $url_productos)) {
    header("Location:" . $url_base . "login.php");
}

if ($_SESSION['id_cargo'] != 1 && str_contains($url_actual, $url_usuarios)) {
    header("Location:" . $url_base . "login.php");
}

if ($_SESSION['id_cargo'] == 2 && str_contains($url_actual, $url_editar)) {
    header("Location:" . $url_base . "index.php");
}

if ($_SESSION['id_cargo'] == 2 && str_contains($url_actual, $url_crear)) {
    header("Location:" . $url_base . "index.php");
}

?>


<!doctype html>
<html lang="en">

<head>

    <title>Inventario Billar</title>


    <link rel="stylesheet" href="">
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- jQuery CDN -->
    <script
  src="https://code.jquery.com/jquery-3.6.3.min.js"
  integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
  crossorigin="anonymous"></script>

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />
  
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>

   <!-- Bootstrap CSS v5.2.1  -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">

  <!-- Popper.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.9.3/umd/popper.min.js" 
  integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>

  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
  <header>
    <!-- place navbar here -->
  </header>

<nav>
  <ul class="nav nav-pills" id="nav-tab" role="tablist">
    <li class="nav-item">
      <button class="nav-link" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="false">Home</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" id="nav-categorias-tab" data-bs-toggle="tab" data-bs-target="#nav-categorias" type="button" role="tab" aria-controls="nav-categorias" aria-selected="false" >Categorias</button>
    </li>
    <?php if ($_SESSION['id_cargo'] != 2) {?>
      <li class="nav-item">
        <button class="nav-link" id="nav-productos-tab" data-bs-toggle="tab" data-bs-target="#nav-productos" type="button" role="tab" aria-controls="nav-productos" aria-selected="false" >Productos</button>
      </li>
    <?php }?>
      <li class="nav-item">
        <button class="nav-link" id="nav-entradas-tab" data-bs-toggle="tab" data-bs-target="#nav-entradas" type="button" role="tab" aria-controls="nav-entradas" aria-selected="false">Inventario</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="nav-detalle-entradas-tab" data-bs-toggle="tab" data-bs-target="#nav-detalle-entradas" type="button" role="tab" aria-controls="nav-detalle-entradas" aria-selected="false">Entradas Inventario</button>
      </li>
    <li class="nav-item">
      <button class="nav-link" id="nav-cuentas-tab" data-bs-toggle="tab" data-bs-target="#nav-cuentas" type="button" role="tab" aria-controls="nav-cuentas" aria-selected="false">Cuentas</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" id="nav-tiempos-tab" data-bs-toggle="tab" data-bs-target="#nav-tiempos" type="button" role="tab" aria-controls="nav-tiempos" aria-selected="false">Tiempos</button>
    </li>
    <?php if ($_SESSION['id_cargo'] == 1) {?>
      <li class="nav-item">
        <button class="nav-link" id="nav-grupo-sedes-tab" data-bs-toggle="tab" data-bs-target="#nav-grupo-sedes" type="button" role="tab" aria-controls="nav-grupo-sedes" aria-selected="false">Grupo Sedes</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="nav-sedes-tab" data-bs-toggle="tab" data-bs-target="#nav-sedes" type="button" role="tab" aria-controls="nav-sedes" aria-selected="false">Sedes</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="nav-cargo-tab" data-bs-toggle="tab" data-bs-target="#nav-cargo" type="button" role="tab" aria-controls="nav-cargo" aria-selected="false">Roles</button>
      </li>
      <li class="nav-item">
        <button class="nav-link" id="nav-usuarios-tab" data-bs-toggle="tab" data-bs-target="#nav-usuarios" type="button" role="tab" aria-controls="nav-usuarios" aria-selected="false">Usuarios</button>
      </li>
    <?php }?>
    <li class="nav-item">
      <button class="nav-link" id="nav-factura-tab" data-bs-toggle="tab" data-bs-target="#nav-factura" type="button" role="tab" aria-controls="nav-factura" aria-selected="false">Facturas</button>
    </li>
    <?php if ($_SESSION['id_cargo'] == 1) {?>
      <li class="nav-item">
        <button class="nav-link" id="nav-detalle_factura-tab" data-bs-toggle="tab" data-bs-target="#nav-detalle_factura" type="button" role="tab" aria-controls="nav-detalle_factura" aria-selected="false">Detalle Factura</button>
      </li>
    <?php }?>
    <li class="nav-item">
      <button class="nav-link" id="nav-caja-tab" data-bs-toggle="tab" data-bs-target="#nav-caja" type="button" role="tab" aria-controls="nav-caja" aria-selected="false">Registro Caja</button>
    </li>
    <li class="nav-item">
      <button class="nav-link" id="nav-cajas-tab" data-bs-toggle="tab" data-bs-target="#nav-cajas" type="button" role="tab" aria-controls="nav-cajas" aria-selected="false">Caja</button>
    </li>
    <li class="nav-item align-right tab-5">
      <button class="nav-link text-red" id="nav-cerrar-tab" data-bs-toggle="tab" data-bs-target="#nav-cerrar" type="button" role="tab" aria-controls="nav-cerrar" aria-selected="false">Cerrar Sesión</button>
    </li>

    </ul>
</nav>
<div class="tab-content" id="nav-tabContent">
<ul class="navbar-nav me-auto mb-2 mb-lg-0">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0"></div>
  <div class="tab-pane fade" id="nav-categorias" role="tabpanel" aria-labelledby="nav-categorias-tab" tabindex="0"></div>
  <?php if ($_SESSION['id_cargo'] != 2) {?>
    <div class="tab-pane fade" id="nav-productos" role="tabpanel" aria-labelledby="nav-productos-tab" tabindex="0"></div>
  <?php }?>
  <div class="tab-pane fade" id="nav-entradas" role="tabpanel" aria-labelledby="nav-entradas-tab" tabindex="0"></div>
  <div class="tab-pane fade" id="nav-detalle-entradas" role="tabpanel" aria-labelledby="nav-detalle-entradas-tab" tabindex="0"></div>
  <div class="tab-pane fade" id="nav-cuentas" role="tabpanel" aria-labelledby="nav-cuentas-tab" tabindex="0"></div>
  <div class="tab-pane fade" id="nav-tiempos" role="tabpanel" aria-labelledby="nav-tiempos-tab" tabindex="0"></div>
  <?php if ($_SESSION['id_cargo'] == 1) {?>
    <div class="tab-pane fade" id="nav-grupo-sedes" role="tabpanel" aria-labelledby="nav-grupo-sedes-tab" tabindex="0"></div>
    <div class="tab-pane fade" id="nav-sedes" role="tabpanel" aria-labelledby="nav-sedes-tab" tabindex="0"></div>
    <div class="tab-pane fade" id="nav-cargo" role="tabpanel" aria-labelledby="nav-cargo-tab" tabindex="0"></div>
    <div class="tab-pane fade" id="nav-usuarios" role="tabpanel" aria-labelledby="nav-usuarios-tab" tabindex="0"></div>
  <?php }?>
  <div class="tab-pane fade" id="nav-factura" role="tabpanel" aria-labelledby="nav-factura-tab" tabindex="0"></div>
  <?php if ($_SESSION['id_cargo'] == 1) {?>
    <div class="tab-pane fade" id="nav-detalle_factura" role="tabpanel" aria-labelledby="nav-detalle_factura-tab" tabindex="0"></div>
  <?php }?>
  <div class="tab-pane fade" id="nav-caja" role="tabpanel" aria-labelledby="nav-caja-tab" tabindex="0"></div>
  <div class="tab-pane fade" id="nav-cajas" role="tabpanel" aria-labelledby="nav-cajas-tab" tabindex="0"></div>
  <div class="tab-pane fade" id="nav-cerrar" role="tabpanel" aria-labelledby="nav-cerrar-tab" tabindex="0"></div>
  </ul>

</div>

<style>
  nav {
    padding: 15px;
  }
  .nav-pills .align-right {
    margin-left: auto;
  }
  .text-red {
    color: red;
  }
  .nav-item.tab-5 {
    border: 2px solid red;
  }
</style>

<script>

var tabId = "";

   document.getElementById("nav-home-tab").addEventListener("click", function() {
   changeActiveTab("nav-home-tab",'<?php echo $url_base; ?>');});

   document.getElementById("nav-categorias-tab").addEventListener("click", function() {
   changeActiveTab("nav-categorias-tab",'<?php echo $url_base; ?>secciones/categorias');});

   <?php if ($_SESSION['id_cargo'] != 2) {?>
    document.getElementById("nav-productos-tab").addEventListener("click", function() {
    changeActiveTab("nav-productos-tab",'<?php echo $url_base; ?>secciones/productos');});
   <?php }?>

   document.getElementById("nav-entradas-tab").addEventListener("click", function() {
   changeActiveTab("nav-entradas-tab",'<?php echo $url_base; ?>secciones/entradas');});

   document.getElementById("nav-detalle-entradas-tab").addEventListener("click", function() {
   changeActiveTab("nav-detalle-entradas-tab",'<?php echo $url_base; ?>secciones/entradas_inventario');});

   document.getElementById("nav-cuentas-tab").addEventListener("click", function() {
   changeActiveTab("nav-cuentas-tab",'<?php echo $url_base; ?>secciones/cuentas');});

   document.getElementById("nav-tiempos-tab").addEventListener("click", function() {
   changeActiveTab("nav-tiempos-tab",'<?php echo $url_base; ?>secciones/tiempos');});

   <?php if ($_SESSION['id_cargo'] == 1) {?>
    document.getElementById("nav-grupo-sedes-tab").addEventListener("click", function() {
    changeActiveTab("nav-grupo-sedes-tab",'<?php echo $url_base; ?>secciones/grupo_sedes');});

    document.getElementById("nav-sedes-tab").addEventListener("click", function() {
    changeActiveTab("nav-sedes-tab",'<?php echo $url_base; ?>secciones/sedes');});

    document.getElementById("nav-cargo-tab").addEventListener("click", function() {
    changeActiveTab("nav-cargo-tab",'<?php echo $url_base; ?>secciones/cargo');});

    document.getElementById("nav-usuarios-tab").addEventListener("click", function() {
    changeActiveTab("nav-usuarios-tab",'<?php echo $url_base; ?>secciones/usuarios');});
   <?php }?>

   document.getElementById("nav-factura-tab").addEventListener("click", function() {
   changeActiveTab("nav-factura-tab",'<?php echo $url_base; ?>secciones/facturas');});

   <?php if ($_SESSION['id_cargo'] == 1) {?>
    document.getElementById("nav-detalle_factura-tab").addEventListener("click", function() {
    changeActiveTab("nav-detalle_factura-tab",'<?php echo $url_base; ?>secciones/detalle_facturas');});
   <?php }?>

   document.getElementById("nav-caja-tab").addEventListener("click", function() {
   changeActiveTab("nav-caja-tab",'<?php echo $url_base; ?>secciones/cajas');});

   document.getElementById("nav-cajas-tab").addEventListener("click", function() {
   changeActiveTab("nav-cajas-tab",'<?php echo $url_base; ?>secciones/ventas');});

   document.getElementById("nav-cerrar-tab").addEventListener("click", function() {
   changeActiveTab("nav-cerrar-tab",'<?php echo $url_base; ?>cerrar.php');});

function changeActiveTab(Id, urlBase) {

  // Eliminar la clase "active" de todas las pestañas
  var tabs = document.querySelectorAll(".nav-link");

  for (var i = 0; i < tabs.length; i++) {
    tabs[i].classList.remove("active");
  }

  window.location.href = urlBase;

  tabId = Id;
  localStorage.setItem("tabId", tabId);

}

 window.onload = function() {
    tabId = localStorage.getItem("tabId");
    document.getElementById(tabId).classList.add("active");
 }

</script>

</body>

<?php if (isset($_GET['mensaje'])) {?>

    <script> Swal.fire({icon:"success", title:"<?php echo $_GET['mensaje']; ?>"}); </script>

<?php }?>

<?php if (isset($_GET['mensaje_error'])) {?>

<script> Swal.fire({icon:"error", title:"<?php echo $_GET['mensaje_error']; ?>"}); </script>

<?php }?>


  <main class="container">