<?php
/* Attendance Report - one row per active staff account for the selected
   date, including staff who never scanned at all that day (Absent).
   $mydb is set up by include/initialize.php, which template.php has
   already loaded. */
global $mydb;

$reportDate = (isset($_GET['date']) && $_GET['date'] != '') ? $_GET['date'] : date('Y-m-d');

/* Same schedule constant used by ajax.php's scan handler. */
define('ATT_AM_TIME_IN_LATEST', '08:15:00'); // 8:00 AM + 15 min grace

$mydb->setQuery("SELECT u.UID, u.DISPLAYNAME, u.USERNAME, 
    a.AM_TIME_IN, a.AM_TIME_OUT, a.PM_TIME_IN, a.PM_TIME_OUT
  FROM `tblusers` u
  LEFT JOIN `tblattendance` a ON a.UID = u.UID AND a.ATTENDANCE_DATE = '".$mydb->escape_value($reportDate)."'
  WHERE u.STATUSACTIVE = 1
  ORDER BY u.DISPLAYNAME ASC");
$rows = $mydb->loadResultList();

/* Same logic as ajax.php's scan handler, kept in sync manually since this
   file renders server-side rather than through a shared function. */
function attendance_row_status($r) {
  if ($r->AM_TIME_IN === null) {
    return array('label' => 'Absent', 'class' => 'danger');
  }
  $late = ($r->AM_TIME_IN > ATT_AM_TIME_IN_LATEST);
  $incomplete = ($r->AM_TIME_OUT === null || $r->PM_TIME_IN === null || $r->PM_TIME_OUT === null);

  if ($incomplete && $late)  { return array('label' => 'Late & Incomplete', 'class' => 'warning'); }
  if ($incomplete)           { return array('label' => 'Incomplete', 'class' => 'secondary'); }
  if ($late)                 { return array('label' => 'Late', 'class' => 'warning'); }
  return array('label' => 'Present', 'class' => 'success');
}

function attendance_fmt_time($t) {
  if ($t === null) { return '<span class="text-muted">-</span>'; }
  return date('g:i A', strtotime($t));
}
?>
<style>
  @media print {
    .main-header, .main-sidebar, .content-header, .main-footer,
    .card-tools, #printAttendance, #attDatePicker, .brand-link { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
  }
</style>

<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Attendance Report</h3>
            <div class="card-tools">
              <input type="date" id="attDatePicker" class="form-control form-control-sm d-inline-block" style="width:auto;" value="<?php echo htmlspecialchars($reportDate); ?>">
              <button type="button" id="printAttendance" class="btn btn-sm btn-primary ml-2">
                <i class="fa fa-print"></i> Print
              </button>
              <a href="<?php echo WEB_ROOT; ?>module/attendance/index.php?view=scan" class="btn btn-sm btn-success ml-2">
                <i class="fa fa-id-card"></i> Open Scan Kiosk
              </a>
            </div>
          </div>
          <div class="card-body">
            <p class="mb-3">Showing attendance for <strong><?php echo date('F j, Y', strtotime($reportDate)); ?></strong></p>
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>AM Time In</th>
                  <th>AM Time Out</th>
                  <th>PM Time In</th>
                  <th>PM Time Out</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; foreach ($rows as $r): $st = attendance_row_status($r); ?>
                <tr>
                  <td><?php echo $i++; ?></td>
                  <td><?php echo htmlspecialchars($r->DISPLAYNAME); ?></td>
                  <td><?php echo attendance_fmt_time($r->AM_TIME_IN); ?></td>
                  <td><?php echo attendance_fmt_time($r->AM_TIME_OUT); ?></td>
                  <td><?php echo attendance_fmt_time($r->PM_TIME_IN); ?></td>
                  <td><?php echo attendance_fmt_time($r->PM_TIME_OUT); ?></td>
                  <td><span class="badge badge-<?php echo $st['class']; ?>"><?php echo $st['label']; ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($rows) === 0): ?>
                <tr><td colspan="7" class="text-center text-muted">No active staff accounts found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>