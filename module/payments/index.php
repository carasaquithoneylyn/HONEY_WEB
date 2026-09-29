<?php
// Payments module
require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title  = "Payments";
$header = $view;
$content = 'list.php';

require_once("../../theme/template.php");
?>

<script type="text/javascript">
var paymentsTable;
var currentFeeTypeFilter = '';

/* Fixed amounts, per the school's fee policy - not editable in the form. */
var FIXED_AMOUNTS = {
  'Entrance Exam': 150,
  'Admission Fee': 30
};

$(document).ready(function() {

  paymentsTable = $('#tblpaymentslist').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "order": [],
    "ajax": {
      url: "<?php echo WEB_ROOT; ?>module/payments/ajax.php",
      type: "POST",
      data: function (d) {
        d.fee_type_filter = currentFeeTypeFilter;
      }
    },
    "columnDefs": [
      { "orderable": false, "targets": [8] }
    ]
  });

  $('#feeTypeFilters button').on('click', function(){
    $('#feeTypeFilters button').removeClass('active');
    $(this).addClass('active');
    currentFeeTypeFilter = $(this).data('filter');
    paymentsTable.ajax.reload();
  });

  $('#printPayments').on('click', function () {
    /* Open the SOA-formatted payments report; carry the active fee filter. */
    var url = "<?php echo WEB_ROOT; ?>module/payments/print.php";
    if (currentFeeTypeFilter) { url += "?fee_type=" + encodeURIComponent(currentFeeTypeFilter); }
    window.open(url, "_blank");
  });

  /* Open the Fee Setup tab when the URL asks for it (after a save/add/delete
     the controller redirects back with ?tab=fees). */
  try {
    var params = new URLSearchParams(window.location.search);
    if (params.get('tab') === 'fees') {
      $('#tab-fees-link').tab('show');
    }
  } catch (e) {}

  /* Confirm before deleting a fee item. */
  $(document).on('click', '.deleteFee', function (e) {
    if (!confirm('Delete this fee item?')) { e.preventDefault(); }
  });

  /* Reset the form each time the modal opens, so a previous entry never
     bleeds into the next one. */
  $('#addPaymentModal').on('show.bs.modal', function () {
    $('#P_ENROLLMENT').val('').trigger('change');
    $('#P_INFO_PANEL').hide();
    $('#P_FEE_TYPE').val('Enrollment Fee').trigger('change');
    $('#P_AMOUNT').val('').prop('readonly', false);
    $('#P_OR_NUMBER').val('');
    $('#P_REMARKS').val('');
  });

});

/* Loads course/year level/tuition/balance for the selected enrollment
   record, and enables or disables Entrance Exam based on year level. */
$(document).on('change', '#P_ENROLLMENT', function(){
  var eid = $(this).val();
  var $entranceOption = $('#P_FEE_TYPE option[value="Entrance Exam"]');

  if (!eid) {
    $('#P_INFO_PANEL').hide();
    $entranceOption.prop('disabled', false);
    $('#P_ENTRANCE_NOTE').hide();
    return;
  }

  $('#P_INFO_SOA').attr('href', "<?php echo WEB_ROOT; ?>module/cashier/soa.php?eid=" + eid);

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/payments/ajax.php",
    method: "POST",
    data: { act: 'balance', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      $('#P_INFO_COURSE').text((d.COURSE_TEXT || '-') + ' / ' + (d.YEAR_LEVEL || '-'));
      $('#P_INFO_ASSESSMENT').text('\u20b1' + Number(d.ASSESSMENT).toFixed(2));
      $('#P_INFO_PAID').text('\u20b1' + Number(d.TOTAL_PAID).toFixed(2));
      $('#P_INFO_BALANCE').text('\u20b1' + Number(d.BALANCE).toFixed(2));
      $('#P_INFO_PANEL').show();

      var isFirstYear = (d.YEAR_LEVEL === '1st Year');
      $entranceOption.prop('disabled', !isFirstYear);
      $('#P_ENTRANCE_NOTE').toggle(!isFirstYear);

      // If Entrance Exam was selected but this student is not 1st year,
      // fall back to Enrollment Fee rather than silently submit a
      // disabled option.
      if (!isFirstYear && $('#P_FEE_TYPE').val() === 'Entrance Exam') {
        $('#P_FEE_TYPE').val('Enrollment Fee').trigger('change');
      }
    },
    error: function() {
      alert('Could not load that enrollment record.');
    }
  });
});

/* Entrance Exam and Admission Fee are fixed amounts - lock the field.
   Enrollment Fee is variable, tied to the tuition balance shown above. */
$(document).on('change', '#P_FEE_TYPE', function(){
  var feeType = $(this).val();
  var $amount = $('#P_AMOUNT');

  if (FIXED_AMOUNTS.hasOwnProperty(feeType)) {
    $amount.val(FIXED_AMOUNTS[feeType]).prop('readonly', true);
  } else {
    $amount.prop('readonly', false);
    if (FIXED_AMOUNTS.hasOwnProperty($amount.data('lastFeeType'))) {
      $amount.val('');
    }
  }
  $amount.data('lastFeeType', feeType);
});

$(document).on('click', '.deletePayment', function(){
  if (!confirm('Delete this payment record?')) { return; }
  window.location = "<?php echo WEB_ROOT; ?>module/payments/controller.php?action=delete&id=" + $(this).attr('PID');
});
</script>