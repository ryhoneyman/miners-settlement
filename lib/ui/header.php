<!DOCTYPE html>
<html lang="en">
<head>
<?php
//header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
//header("Pragma: no-cache"); // HTTP 1.0.
//header("Expires: 0"); // Proxies.
?>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

  <title><?php print 'Miners Tools: '.$main->title(); ?></title>
<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="/assets/alte/current/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="/assets/alte/current/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="/assets/alte/current/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="/assets/alte/current/dist/js/adminlte.js"></script>
<!-- Toastr -->
<script src="/assets/alte/current/plugins/toastr/toastr.min.js" type="text/javascript"></script>
<!-- Select2 -->
<script src="/assets/alte/current/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<!-- local js -->
<script src="/assets/js/ms.js?v=20230527001" type="text/javascript"></script>

<!-- fontawesome -->
<link rel="stylesheet" href="/assets/fa/css/all.min.css">
<!-- local css -->
<link rel="stylesheet" href="/assets/css/ms.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="/assets/alte/current/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="/assets/alte/current/plugins/toastr/toastr.min.css">
<!-- Select2 -->
<link rel="stylesheet" href="/assets/alte/current/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/assets/alte/current/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<!-- Bootstrap4 Duallistbox -->
<link rel="stylesheet" href="/assets/alte/current/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="/assets/alte/current/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/alte/current/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="/assets/alte/current/dist/css/adminlte.min.css">
<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="dark-mode hold-transition sidebar-mini sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
<?php include("ui/navbar.php"); ?>

<!-- Main Sidebar Container -->
<?php include("ui/sidebar.php"); ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <div class="content-header">
      <div class="container-fluid">
         <div class="row mb-2">
            <div class="col-12">
               <h1 class="m-0 text-dark"><?php print $main->title(); ?></h1>
               <hr>
            </div><!-- /.col -->
         </div> <!-- /.row -->
      </div><!-- /.container-fluid -->
   </div><!-- /.container-header -->

 <!-- Main content -->
<section class="content">
  <div class="container-fluid">

