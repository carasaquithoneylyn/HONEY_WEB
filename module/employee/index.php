<?php
/* =====================================================================
   Employee module - router only.

   The list table, the Add / Edit modal and all of their JavaScript now
   live together in list.php. Previously the table markup was in
   list.php while .DataTable() was initialised from here, so if the two
   files were ever out of step the table rendered as bare column headers
   with no rows, no search box and no pager. Keeping this file free of
   page scripts removes that failure mode.
   ===================================================================== */

require_once("../../include/initialize.php");

$view = isset($_GET['view']) ? $_GET['view'] : '';

if ($view === 'view') {
  $title   = "Employee Profile";
  $content = 'view.php';
} else {
  $title   = "Employee";
  $content = 'list.php';
}

require_once("../../theme/template.php");
?>