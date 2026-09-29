<?php
// Enrollment module
/* Entry point for the Enrollment module. This file also holds the
   JavaScript that runs the Sectioning (Stage 2) and Edit Enrollment
   modals in list.php - it fetches the record's data (including the
   student's photo) before opening each modal. */
require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
$title  = "Enrollment";
$header = $view;
$content = 'list.php';

require_once("../../theme/template.php");
?>

<script type="text/javascript">
var enrollTable;
var currentStatusFilter = '';

$(document).ready(function() {

  enrollTable = $('#tblenrollmentlist').DataTable({
    "processing": true,
    "serverSide": true,
    "scrollX": true,
    "order": [],
    "ajax": {
      url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
      type: "POST",
      data: function (d) {
        /* Sent with every draw so the filter chips survive paging,
           sorting and searching. */
        d.status_filter = currentStatusFilter;
      }
    },
    "columnDefs": [
      { "orderable": false, "targets": [12] }
    ]
  });

  $('#statusFilters button').on('click', function(){
    $('#statusFilters button').removeClass('active');
    $(this).addClass('active');
    currentStatusFilter = $(this).data('filter');
    enrollTable.ajax.reload();
  });

  $('#printEnrollment').on('click', function () {
    window.print();
  });

});

/* Fills a section dropdown for a given course + academic year. Used by
   both the Sectioning modal and the Edit modal. */
function loadSections(targetId, courseId, syId, preselect, allowBlank) {
  var $sel = $('#' + targetId);

  if (!courseId || !syId) {
    $sel.html('<option value="">Select course and academic year first</option>');
    return;
  }

  $sel.html('<option value="">Loading...</option>');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'sections', COURSE_ID: courseId, SY_ID: syId },
    dataType: "json",
    success: function(rows) {
      var html = allowBlank
        ? '<option value="">Not sectioned yet</option>'
        : '<option value="">Select Section</option>';

      if (!rows || rows.length === 0) {
        $sel.html('<option value="">No section for this course and academic year</option>');
        return;
      }
      $.each(rows, function(i, r){
        html += '<option value="' + r.SECTION_ID + '">' + r.YEAR_LEVEL + ' - ' + r.SECTION_NAME + '</option>';
      });
      $sel.html(html);
      if (preselect) { $sel.val(preselect); }
    },
    error: function() {
      $sel.html('<option value="">Could not load sections</option>');
    }
  });
}

/* ---------------- STAGE 2: SECTIONING ---------------- */
$(document).on('click', '.doSectioning', function(){
  var eid = $(this).attr('EID');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'row', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      $('#SEC_EID').val(d.ENROLLMENT_ID);
      $('#SEC_COURSE').val(d.COURSE_ID);
      $('#SEC_SY').val(d.SY_ID);

      $('#SEC_IDNO_TEXT').text(d.IDNO || '-');
      $('#SEC_NAME_TEXT').text(d.FULLNAME || '-');
      $('#SEC_COURSE_TEXT').text(d.COURSE_TEXT || '-');
      $('#SEC_TERM_TEXT').text((d.SCHOOL_YEAR || '') + ' / ' + (d.SEMESTER || ''));

      // Student photo, same fallback logic as the Student module.
      $('#SEC_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/default.png');

      /* Already enrolled records keep their existing date, otherwise
         default to today. */
      $('#SEC_DATE').val(d.DATE_ENROLLED ? d.DATE_ENROLLED : '<?php echo date('Y-m-d'); ?>');
      $('#SEC_STATUS').val(d.STATUS === 'Registered' ? 'Enrolled' : d.STATUS);

      loadSections('SEC_SECTION', d.COURSE_ID, d.SY_ID, d.SECTION_ID, false);

      $('#sectioningModal').modal('show');
    },
    error: function() { alert('Could not load that enrollment record.'); }
  });
});

/* ---------------- EDIT ---------------- */
$(document).on('click', '.editEnrollment', function(){
  var eid = $(this).attr('EID');

  $.ajax({
    url: "<?php echo WEB_ROOT; ?>module/enrollment/ajax.php",
    method: "POST",
    data: { act: 'row', ENROLLMENT_ID: eid },
    dataType: "json",
    success: function(d) {
      $('#E_EID').val(d.ENROLLMENT_ID);
      $('#E_IDNO_TEXT').text(d.IDNO || '-');
      $('#E_NAME_TEXT').text(d.FULLNAME || '-');

      // Student photo, same fallback logic as the Student module.
      $('#E_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/default.png');

      $('#E_SY').val(d.SY_ID);
      $('#E_SEMESTER').val(d.SEMESTER);
      $('#E_COURSE').val(d.COURSE_ID);
      $('#E_YEARLEVEL').val(d.YEAR_LEVEL);
      $('#E_CURRICULUM').val(d.CURRICULUM_YR || '');
      $('#E_CATEGORY').val(d.CATEGORY);
      $('#E_STATUS').val(d.STATUS);
      $('#E_DATE_RESERVED').val(d.DATE_RESERVED || '');
      $('#E_DATE_ENROLLED').val(d.DATE_ENROLLED || '');

      loadSections('E_SECTION', d.COURSE_ID, d.SY_ID, d.SECTION_ID, true);

      $('#editEnrollmentModal').modal('show');
    },
    error: function() { alert('Could not load that enrollment record.'); }
  });
});

/* Changing course or academic year in Edit invalidates the section list. */
$(document).on('change', '#E_COURSE, #E_SY', function(){
  loadSections('E_SECTION', $('#E_COURSE').val(), $('#E_SY').val(), '', true);
});
</script>