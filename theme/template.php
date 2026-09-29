<?php

//ABADIES SOLUTIONS

 if (!isset($_SESSION['UID'])){
      redirect(WEB_ROOT."login.php");
 //   header("Location: login.php");

     }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo htmlspecialchars(isset($title) ? $title : 'Dashboard'); ?> &middot; ABADIES SOLUTIONS</title>
    <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Apply saved dark-mode preference before paint, so there is no flash of the wrong theme -->
  <script>
    if (localStorage.getItem('abadiesTheme') === 'dark') {
      document.documentElement.classList.add('dark-mode-custom');
    }
  </script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/jqvmap/jqvmap.min.css">
   <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
   <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/toastr/toastr.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>dist/css/adminlte.min.css">
  <!-- Custom theme (premium palette + dark mode). Cache-busted with filemtime
       so a browser that cached an older version of this file (a common
       issue with static assets) always fetches the current one. -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>custom.css?v=<?php echo @filemtime(dirname(__DIR__).'/custom.css'); ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Inter, the UI face for the premium theme -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <!--Insert Header Here-->
 <?php require_once("header.php") ; ?> 
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar">
    <!-- Brand Logo -->
    <a href="<?php echo  WEB_ROOT;?>" class="brand-link">
      <img src="<?php echo  WEB_ROOT;?>csr-scc.png" class="brand-image-fixed" alt="Logo"
           style="width:30px; height:30px; object-fit:contain;">
      <span class="brand-text">ABADIES</span>
    </a>

    <!-- Sidebar -->
  <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      

      <!-- Sidebar Menu -->
   <?php require_once("sidebar.php") ; ?> 
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header)
         The dashboard draws its own welcome block, so this bar is skipped
         there to avoid two stacked headings saying the same thing. -->
    <?php if ($title != 'Home') { ?>
    <div class="content-header">
      <div class="container-fluid px-0">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
          <div>
            <h1 class="m-0"><?php echo htmlspecialchars($title); ?></h1>
          </div>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo WEB_ROOT; ?>">Dashboard</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($title); ?></li>
          </ol>
        </div>
      </div>
    </div>
    <?php } ?>
    <!-- /.content-header -->
     
       <!-- Main content -->
      <?php require_once $content; ?> 
    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer no-print">
    <span>&copy; <?php echo date('Y'); ?> <a href="<?php echo WEB_ROOT; ?>">ABADIES SOLUTIONS</a>. All rights reserved.</span>
    <div class="float-right d-none d-sm-inline-block">
      Version 3.0.5
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="<?php echo  WEB_ROOT;?>plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo  WEB_ROOT;?>plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?php echo  WEB_ROOT;?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo  WEB_ROOT;?>plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="<?php echo  WEB_ROOT;?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo  WEB_ROOT;?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo  WEB_ROOT;?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo  WEB_ROOT;?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo  WEB_ROOT;?>plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo  WEB_ROOT;?>plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?php echo  WEB_ROOT;?>plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?php echo  WEB_ROOT;?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo  WEB_ROOT;?>plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo  WEB_ROOT;?>plugins/moment/moment.min.js"></script>
<script src="<?php echo  WEB_ROOT;?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo  WEB_ROOT;?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- InputMask -->
<script src="<?php echo  WEB_ROOT;?>plugins/moment/moment.min.js"></script>
<script src="<?php echo  WEB_ROOT;?>plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<!-- date-range-picker -->
<script src="<?php echo  WEB_ROOT;?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- Summernote -->
<script src="<?php echo  WEB_ROOT;?>plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?php echo  WEB_ROOT;?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- bs-custom-file-input -->
<script src="<?php echo  WEB_ROOT;?>plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo  WEB_ROOT;?>dist/js/adminlte.js"></script>
<!-- SweetAlert2 -->
<script src="<?php echo  WEB_ROOT;?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE's dashboard.js and demo.js are deliberately NOT loaded.
     dashboard.js wires up demo charts and widgets this project does not
     have, and demo.js injects AdminLTE's floating "theme settings" panel,
     which fights the theme in custom.css and the toggle in header.php. -->
<script type="text/javascript">
// $(document).ready(function () {
 
//   $('[data-widget="pushmenu"]').PushMenu('toggle');

//   bsCustomFileInput.init();
// });
</script>
<script>
  $(function () {
   /* $("#example1").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });*/

    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })
     $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
  });
  
</script>



<!-- AdminLTE's demo toast/notification block was removed here.
     It bound SweetAlert and Toastr "Lorem ipsum" demos to classes this
     project does not use, and its $('.swalDefaultSuccess').load(...) call
     used the jQuery .load() event method removed in jQuery 3, throwing
     "e.indexOf is not a function" on every page load. -->

<!-- A duplicate copy of the notifications bell script used to live here.
     theme/header.php already owns the bell, and this copy re-prefixed
     WEB_ROOT onto links that are already root-relative, producing dead
     URLs like /abadies-class//abadies-class/module/employee/index.php.
     Removed so there is exactly one renderer. -->
</body>
</html>