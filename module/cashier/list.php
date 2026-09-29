<?php
/* Cashier module - list of enrollment records to bill.
   $mydb is already available (include/initialize.php, loaded by template.php).
   Each row opens the printable Statement of Account (soa.php). */
global $mydb;

$rows = array();
$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.YEAR_LEVEL, e.SEMESTER, e.STATUS,
      s.IDNO, s.LNAME, s.FNAME, s.MNAME,
      c.COURSE_CODE, sy.SCHOOL_YEAR
    FROM `tblenrollment` e
    JOIN `tblstudent`    s  ON s.S_ID      = e.S_ID
    JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
    JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
    ORDER BY e.ENROLLMENT_ID DESC");
foreach ($mydb->loadResultList() as $r) { $rows[] = $r; }
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title"><i class="fa fa-file-invoice-dollar"></i>&nbsp; Statement of Account</h3>
            <div class="card-tools">
              <span class="text-muted small">Pick a student's enrolment record to print its Statement of Account.</span>
            </div>
          </div>
          <div class="card-body">
            <table id="tblcashierlist" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>ID No.</th>
                  <th>Student</th>
                  <th>Course</th>
                  <th>School Year</th>
                  <th>Sem / Year Level</th>
                  <th>Status</th>
                  <th class="text-center">Statement</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $r) {
                  $statusClass = 'secondary';
                  if ($r->STATUS == 'Enrolled')   { $statusClass = 'success'; }
                  if ($r->STATUS == 'Registered') { $statusClass = 'warning'; }
                  if ($r->STATUS == 'Dropped')    { $statusClass = 'danger'; }
                  if ($r->STATUS == 'Completed')  { $statusClass = 'info'; }
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($r->IDNO); ?></td>
                  <td><?php echo htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)); ?></td>
                  <td><?php echo htmlspecialchars($r->COURSE_CODE); ?></td>
                  <td><?php echo htmlspecialchars($r->SCHOOL_YEAR); ?></td>
                  <td><?php echo htmlspecialchars($r->SEMESTER.' / '.$r->YEAR_LEVEL); ?></td>
                  <td><span class="badge badge-<?php echo $statusClass; ?>"><?php echo htmlspecialchars($r->STATUS); ?></span></td>
                  <td class="text-center">
                    <button type="button" EID="<?php echo $r->ENROLLMENT_ID; ?>" class="btn btn-primary btn-xs viewSOA" title="Statement of Account">
                      <span class="fa fa-print"></span> View / Print
                    </button>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
