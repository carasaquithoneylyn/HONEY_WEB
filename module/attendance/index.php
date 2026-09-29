<?php
// Attendance module (RFID)
require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title  = "Attendance";
$header = $view;
$content = ($view == 'scan') ? 'scan.php' : 'list.php';

require_once("../../theme/template.php");
?>

<?php if ($view == 'scan') { ?>
<script type="text/javascript">
/* -------------------------------------------------------------------
   RFID KIOSK SCANNING

   Standard USB RFID readers act as a keyboard: they "type" the card
   number then press Enter. So this just keeps a hidden input always
   focused, waits for Enter, and sends whatever was typed - along with
   whichever action button is selected - to the scan endpoint.
   ------------------------------------------------------------------- */
$(document).ready(function () {

  var $input = $('#rfidInput');
  var currentAction = 'AM_TIME_IN';
  var actionLabels = {
    'AM_TIME_IN':  'AM Time In',
    'AM_TIME_OUT': 'AM Time Out',
    'PM_TIME_IN':  'PM Time In',
    'PM_TIME_OUT': 'PM Time Out'
  };
  var defaultPhoto = '<?php echo WEB_ROOT; ?>module/user/images/default.png';

  function refocus() {
    $input.val('').focus();
  }
  refocus();

  // Clicking anywhere (except the action buttons themselves) should not
  // lose focus on the hidden input, since the reader only "types" into
  // whatever currently has focus.
  $(document).on('click', function (e) {
    if (!$(e.target).hasClass('action-btn')) {
      refocus();
    }
  });

  $('.action-btn').on('click', function () {
    $('.action-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass('active btn-primary');
    currentAction = $(this).data('action');
    $('#selectedActionLabel').text(actionLabels[currentAction]);
    refocus();
  });
  $('.action-btn[data-action="AM_TIME_IN"]').trigger('click');

  $input.on('keypress', function (e) {
    if (e.which === 13) { // Enter
      e.preventDefault();
      var scanned = $input.val().trim();
      if (scanned === '') { return; }
      submitScan(scanned);
    }
  });

  function playSound(granted) {
    var el = document.getElementById(granted ? 'soundGranted' : 'soundDenied');
    if (!el) { return; }
    el.volume = 1.0;
    el.currentTime = 0;
    el.play().catch(function () { /* autoplay may need a user gesture first - ignored */ });
  }

  function submitScan(rfid) {
    $('#scanFeedback').removeClass('alert-success alert-danger').addClass('alert-secondary').text('Reading card...');

    $.ajax({
      url: "<?php echo WEB_ROOT; ?>module/attendance/ajax.php",
      method: "POST",
      data: { act: 'scan', RFID_NUMBER: rfid, ACTION: currentAction },
      dataType: "json",
      success: function (d) {
        playSound(d.success);

        if (d.success) {
          $('#scanFeedback')
            .removeClass('alert-secondary alert-danger').addClass('alert-success')
            .html('<strong>' + d.action_label + '</strong> recorded at ' + d.time + '<br>' + d.message);

          $('#dashPhoto').attr('src', d.photo_url || defaultPhoto);
          $('#dashName').text(d.name);
          $('#dashUsername').text(d.username || '-');
          $('#dashRole').text(d.role || '-');
          $('#dashStatus').text('Active');
          $('#scanDashboard').removeClass('d-none');
        } else {
          $('#scanFeedback')
            .removeClass('alert-secondary alert-success').addClass('alert-danger')
            .text(d.message);
          $('#scanDashboard').addClass('d-none');
        }
        refocus();
      },
      error: function () {
        playSound(false);
        $('#scanFeedback')
          .removeClass('alert-secondary alert-success').addClass('alert-danger')
          .text('Could not reach the server. Try again.');
        refocus();
      }
    });
  }

  // Live clock, so staff can see the reference time next to the schedule.
  function tickClock() {
    var now = new Date();
    $('#kioskClock').text(now.toLocaleTimeString());
  }
  tickClock();
  setInterval(tickClock, 1000);

});
</script>
<?php } else { ?>
<script type="text/javascript">
$(document).ready(function () {
  $('#attDatePicker').on('change', function () {
    window.location = "<?php echo WEB_ROOT; ?>module/attendance/index.php?date=" + $(this).val();
  });

  $('#printAttendance').on('click', function () {
    window.print();
  });
});
</script>
<?php } ?>