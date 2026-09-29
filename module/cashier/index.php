<?php
// Cashier module - Statement of Account
require_once("../../include/initialize.php");

$title   = "Cashier";
$content = 'list.php';

require_once("../../theme/template.php");
?>

<script type="text/javascript">
$(document).ready(function () {

  /* Small, in-page DataTable. The enrollment list for a class-project
     database is short, so client-side paging is enough and keeps the
     module to a single request. */
  $('#tblcashierlist').DataTable({
    "responsive": true,
    "autoWidth": false,
    "order": [[0, "desc"]],
    "columnDefs": [
      { "orderable": false, "targets": [6] }
    ]
  });

  /* Open the Statement of Account for the chosen record in a new tab so
     the cashier keeps the list open while printing. */
  $(document).on('click', '.viewSOA', function () {
    var eid = $(this).attr('EID');
    if (!eid) { return; }
    window.open("<?php echo WEB_ROOT; ?>module/cashier/soa.php?eid=" + eid, "_blank");
  });

});
</script>
