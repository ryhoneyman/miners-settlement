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

  <title><?php echo 'Miners Tools: '.$title.' '.$subtitle; ?></title>
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
<!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/a73a4c549e.js" crossorigin="anonymous"></script>
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

<body class="hold-transition sidebar-mini sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
<?php include("ui/navbar.php"); ?>

  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php include("ui/sidebar.php"); ?>

  <!-- Content Wrapper. Contains page content -->
<?php include("ui/titlebar.php"); ?>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

