<?php
/* Cashier workspace - Payments + Fee Setup in one screen.
   $mydb is set up by include/initialize.php (loaded by template.php). */
global $mydb;

$regEnrollments = array();
$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.YEAR_LEVEL, e.SEMESTER, e.COURSE_ID,
    s.IDNO, s.LNAME, s.FNAME, s.MNAME,
    c.COURSE_CODE, sy.SCHOOL_YEAR
  FROM `tblenrollment` e
  JOIN `tblstudent` s ON s.S_ID = e.S_ID
  JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
  JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
  ORDER BY e.ENROLLMENT_ID DESC");
foreach ($mydb->loadResultList() as $r) { $regEnrollments[] = $r; }

$regFeeTypes = array('Tuition', 'Enrollment Fee', 'Entrance Exam', 'Admission Fee');

/* ---- Fee Setup data for the selected year level ---- */
$feeYearLevels = array('1st Year', '2nd Year', '3rd Year', '4th Year');
$feeCats = array(
    'STANDARD'    => 'Standard School Fees',
    'NONSTANDARD' => 'Non-Standard School Fees',
    'OTHER'       => 'Other Fees',
);
$feeYear = isset($_GET['feeyear']) && in_array($_GET['feeyear'], $feeYearLevels) ? $_GET['feeyear'] : '1st Year';

/* School fees (tblfeeitems), grouped by category, for this year level. */
$feeItems = array(); // CATEGORY => list of rows
foreach (array_keys($feeCats) as $c) { $feeItems[$c] = array(); }
if ($mydb->tableExists('tblfeeitems')) {
    $mydb->setQuery("SELECT FEEITEM_ID, CATEGORY, ITEM_NAME, AMOUNT
        FROM `tblfeeitems`
        WHERE YEAR_LEVEL = '".$mydb->escape_value($feeYear)."'
        ORDER BY SORT_ORDER ASC, FEEITEM_ID ASC");
    foreach ($mydb->loadResultList() as $it) {
        if (isset($feeItems[$it->CATEGORY])) { $feeItems[$it->CATEGORY][] = $it; }
    }
}
$feeSetupReady = $mydb->tableExists('tblfeeitems');

/* Tuition per COURSE (tblfeeschedule) for this year level. */
$tuitionCourses = array();
$tuitionReady = $mydb->tableExists('tblfeeschedule');
if ($tuitionReady) {
    $tmap = array();
    $mydb->setQuery("SELECT COURSE_ID, TUITION_FEE FROM `tblfeeschedule`
        WHERE YEAR_LEVEL = '".$mydb->escape_value($feeYear)."'");
    foreach ($mydb->loadResultList() as $t) { $tmap[(int)$t->COURSE_ID] = (float)$t->TUITION_FEE; }

    $mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME FROM `tblcourses` ORDER BY COURSE_CODE ASC");
    foreach ($mydb->loadResultList() as $c) {
        $tuitionCourses[] = array(
            'COURSE_ID'   => (int)$c->COURSE_ID,
            'COURSE_CODE' => $c->COURSE_CODE,
            'COURSE_NAME' => $c->COURSE_NAME,
            'TUITION_FEE' => isset($tmap[(int)$c->COURSE_ID]) ? $tmap[(int)$c->COURSE_ID] : 0.0,
        );
    }
}
?>
<style>
  /* Branded print header - hidden on screen, shown only when printing so the
     Payments report carries the same masthead as the Statement of Account. */
  .pay-print-header { display: none; }

  @media print {
    .main-header, .main-sidebar, .content-header, .main-footer,
    #feeTypeFilters, #printPayments, .card-tools, .brand-link, .nav-tabs,
    #tab-fees,
    #tblpaymentslist_filter, #tblpaymentslist_length,
    #tblpaymentslist_paginate, #tblpaymentslist_info { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }

    html.dark-mode-custom body,
    html.dark-mode-custom .content-wrapper,
    html.dark-mode-custom .card,
    html.dark-mode-custom .card-body,
    html.dark-mode-custom .table,
    body, .content-wrapper, .card, .card-body {
      background: #fff !important; color: #000 !important; box-shadow: none !important;
    }
    .card { border: 0 !important; }
    .pay-print-header { display: block !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    #tblpaymentslist th, #tblpaymentslist td { color: #000 !important; }
    @page { size: A4 portrait; margin: 12mm; }
  }

  .fee-cat-row td { background: #f4f6f9; font-weight: 700; letter-spacing: .3px; }
  html.dark-mode-custom .fee-cat-row td { background: #232a3b; }
</style>

<!-- Print-only masthead (matches module/cashier/soa.php) -->
<div class="pay-print-header" style="border-bottom:3px double #333; padding-bottom:12px; margin-bottom:16px;">
  <div style="display:flex; align-items:center; gap:16px;">
    <img src="<?php echo WEB_ROOT; ?>csr-scc.png" alt="Logo" style="width:64px; height:64px; object-fit:contain;">
    <div style="line-height:1.25;">
      <div style="font-size:20px; font-weight:700; letter-spacing:.5px; color:#1c2f9e;">ABADIES SOLUTIONS</div>
      <div style="font-size:12px; color:#666;">Student and Alumni Records Management System</div>
      <div style="font-size:12px; color:#666;">Office of the Cashier</div>
    </div>
  </div>
  <div style="text-align:center; font-size:16px; font-weight:700; letter-spacing:3px; color:#1c2f9e; margin-top:12px;">PAYMENTS REPORT</div>
  <div style="text-align:center; font-size:11px; color:#666;">Printed on <?php echo date('F d, Y g:i A'); ?></div>
</div>

<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <ul class="nav nav-tabs" id="cashierTabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="tab-pay-link" data-toggle="pill" href="#tab-pay" role="tab">
          <i class="fa fa-money-bill-wave"></i> Payments
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="tab-fees-link" data-toggle="pill" href="#tab-fees" role="tab">
          <i class="fa fa-coins"></i> Fee Setup
        </a>
      </li>
    </ul>

    <div class="tab-content">

      <!-- =================== PAYMENTS TAB =================== -->
      <div class="tab-pane fade show active" id="tab-pay" role="tabpanel">
        <div class="card card-outline card-primary" style="border-top:0;">
          <div class="card-header">
            <h3 class="card-title">Payment Records</h3>
            <div class="card-tools">
              <div class="btn-group" id="feeTypeFilters">
                <button type="button" class="btn btn-sm btn-outline-secondary active" data-filter="">All</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-filter="Tuition">Tuition</button>
                <button type="button" class="btn btn-sm btn-outline-warning" data-filter="Enrollment Fee">Enrollment Fee</button>
                <button type="button" class="btn btn-sm btn-outline-info" data-filter="Entrance Exam">Entrance Exam</button>
                <button type="button" class="btn btn-sm btn-outline-success" data-filter="Admission Fee">Admission Fee</button>
              </div>
              <button type="button" class="btn btn-sm btn-primary ml-2" data-toggle="modal" data-target="#addPaymentModal">
                <i class="fa fa-plus"></i> Add Payment
              </button>
              <button type="button" id="printPayments" class="btn btn-sm btn-default ml-2">
                <i class="fa fa-print"></i> Print
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="tblpaymentslist" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>OR No.</th>
                  <th>Student</th>
                  <th>Course</th>
                  <th>Fee Type</th>
                  <th>Amount</th>
                  <th>Date Paid</th>
                  <th>Received By</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- =================== FEE SETUP TAB =================== -->
      <div class="tab-pane fade" id="tab-fees" role="tabpanel">
        <div class="card card-outline card-primary" style="border-top:0;">
          <div class="card-header">
            <h3 class="card-title">Fee Setup <small class="text-muted">(drives the Statement of Account)</small></h3>
            <div class="card-tools">
              <form method="get" action="index.php" class="form-inline" id="feeYearForm">
                <input type="hidden" name="tab" value="fees">
                <label class="mr-2 mb-0 small">Year Level</label>
                <select name="feeyear" class="form-control form-control-sm" onchange="document.getElementById('feeYearForm').submit();">
                  <?php foreach ($feeYearLevels as $yl) { ?>
                  <option value="<?php echo $yl; ?>" <?php echo ($yl == $feeYear) ? 'selected' : ''; ?>><?php echo $yl; ?></option>
                  <?php } ?>
                </select>
              </form>
            </div>
          </div>
          <div class="card-body">

            <?php if (!$feeSetupReady) { ?>
              <div class="alert alert-warning mb-0">
                The <code>tblfeeitems</code> table was not found. Import <code>database/abadies_db.sql</code> first.
              </div>
            <?php } else { ?>

            <p class="text-muted small">
              Editing these amounts updates the assessment shown on the Statement of Account and the
              balance in Add Payment for <strong><?php echo htmlspecialchars($feeYear); ?></strong>.
            </p>

            <!-- ===== Tuition Fee per Course (tblfeeschedule) ===== -->
            <h6 class="font-weight-bold"><i class="fa fa-graduation-cap"></i> Tuition Fee per Course &mdash; <?php echo htmlspecialchars($feeYear); ?></h6>
            <?php if (!$tuitionReady) { ?>
              <div class="alert alert-warning">The <code>tblfeeschedule</code> table was not found.</div>
            <?php } elseif (count($tuitionCourses) === 0) { ?>
              <div class="alert alert-info">No courses found. Add courses first.</div>
            <?php } else { ?>
            <form method="post" action="controller.php?action=savetuition">
              <input type="hidden" name="feeyear" value="<?php echo htmlspecialchars($feeYear); ?>">
              <table class="table table-bordered table-sm mb-2" style="max-width:720px;">
                <thead>
                  <tr><th style="width:60%;">Course</th><th style="width:40%;">Tuition Fee</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($tuitionCourses as $c) { ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($c['COURSE_CODE']); ?></strong> &ndash; <?php echo htmlspecialchars($c['COURSE_NAME']); ?></td>
                    <td>
                      <input type="number" step="0.01" min="0" name="TUI[<?php echo $c['COURSE_ID']; ?>]"
                             class="form-control form-control-sm text-right"
                             value="<?php echo number_format($c['TUITION_FEE'], 2, '.', ''); ?>">
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
              <button type="submit" class="btn btn-primary btn-sm mb-3"><i class="fa fa-save"></i> Save Tuition</button>
            </form>
            <?php } ?>

            <hr>

            <!-- ===== School Fees (tblfeeitems) ===== -->
            <h6 class="font-weight-bold"><i class="fa fa-list"></i> School Fees &mdash; <?php echo htmlspecialchars($feeYear); ?></h6>

            <!-- Save amounts -->
            <form method="post" action="controller.php?action=savefees">
              <input type="hidden" name="feeyear" value="<?php echo htmlspecialchars($feeYear); ?>">
              <table class="table table-bordered table-sm mb-2" style="max-width:720px;">
                <thead>
                  <tr><th style="width:60%;">Item</th><th style="width:25%;">Amount</th><th style="width:15%;">&nbsp;</th></tr>
                </thead>
                <tbody>
                  <?php $grand = 0.0; foreach ($feeCats as $ck => $clabel) {
                    if (count($feeItems[$ck]) === 0) { continue; } ?>
                    <tr class="fee-cat-row"><td colspan="3"><?php echo $clabel; ?></td></tr>
                    <?php foreach ($feeItems[$ck] as $it) { $grand += (float)$it->AMOUNT; ?>
                    <tr>
                      <td><?php echo htmlspecialchars($it->ITEM_NAME); ?></td>
                      <td>
                        <input type="number" step="0.01" min="0" name="AMT[<?php echo (int)$it->FEEITEM_ID; ?>]"
                               class="form-control form-control-sm text-right"
                               value="<?php echo number_format((float)$it->AMOUNT, 2, '.', ''); ?>">
                      </td>
                      <td class="text-center">
                        <a href="controller.php?action=deletefee&id=<?php echo (int)$it->FEEITEM_ID; ?>&feeyear=<?php echo urlencode($feeYear); ?>"
                           class="btn btn-danger btn-xs deleteFee" title="Delete"><span class="fa fa-trash"></span></a>
                      </td>
                    </tr>
                    <?php } ?>
                  <?php } ?>
                  <tr>
                    <td class="text-right font-weight-bold">TOTAL SCHOOL FEES</td>
                    <td class="text-right font-weight-bold">&#8369; <?php echo number_format($grand, 2); ?></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
              <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save Amounts</button>
            </form>

            <hr>

            <!-- Add new fee item -->
            <form method="post" action="controller.php?action=addfee" class="form-inline">
              <input type="hidden" name="feeyear" value="<?php echo htmlspecialchars($feeYear); ?>">
              <select name="F_CATEGORY" class="form-control form-control-sm mr-2 mb-1" required>
                <?php foreach ($feeCats as $ck => $clabel) { ?>
                <option value="<?php echo $ck; ?>"><?php echo $clabel; ?></option>
                <?php } ?>
              </select>
              <input type="text" name="F_ITEM_NAME" class="form-control form-control-sm mr-2 mb-1" placeholder="Item name" required>
              <input type="number" step="0.01" min="0" name="F_AMOUNT" class="form-control form-control-sm mr-2 mb-1" placeholder="Amount" value="0.00" required>
              <button type="submit" class="btn btn-success btn-sm mb-1"><i class="fa fa-plus"></i> Add Item</button>
            </form>

            <?php } ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===================== ADD PAYMENT ===================== -->
<div class="modal fade" id="addPaymentModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=add" method="POST">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title"><i class="fa fa-money-bill-wave"></i>&nbsp; Add Payment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

          <div class="form-group">
            <label for="P_ENROLLMENT" class="col-form-label col-form-label-sm">Student / Enrollment Record</label>
            <select class="form-control form-control-sm select2" name="P_ENROLLMENT" id="P_ENROLLMENT" style="width:100%" required>
              <option value="">Select a student's enrollment record</option>
              <?php foreach ($regEnrollments as $e) { ?>
              <option value="<?php echo $e->ENROLLMENT_ID; ?>">
                <?php echo htmlspecialchars($e->IDNO.' - '.$e->LNAME.', '.$e->FNAME.' '.$e->MNAME.' ('.$e->COURSE_CODE.' - '.$e->YEAR_LEVEL.' - '.$e->SCHOOL_YEAR.' '.$e->SEMESTER.')'); ?>
              </option>
              <?php } ?>
            </select>
          </div>

          <div class="callout callout-info py-2 mb-3" id="P_INFO_PANEL" style="display:none;">
            <div class="row">
              <div class="col-sm-3">
                <small class="text-muted d-block">Course / Year Level</small>
                <strong id="P_INFO_COURSE">-</strong>
              </div>
              <div class="col-sm-3">
                <small class="text-muted d-block">Total Assessment</small>
                <strong id="P_INFO_ASSESSMENT">-</strong>
              </div>
              <div class="col-sm-3">
                <small class="text-muted d-block">Total Paid</small>
                <strong id="P_INFO_PAID">-</strong>
              </div>
              <div class="col-sm-3">
                <small class="text-muted d-block">Balance Due</small>
                <strong id="P_INFO_BALANCE" class="text-danger">-</strong>
              </div>
            </div>
            <div class="text-right mt-1">
              <a href="#" id="P_INFO_SOA" target="_blank" class="small"><i class="fa fa-file-invoice-dollar"></i> View full Statement of Account</a>
            </div>
          </div>

          <div class="row">

            <div class="col-sm-6">
              <div class="form-group">
                <label for="P_FEE_TYPE" class="col-form-label col-form-label-sm">Fee Type</label>
                <select class="form-control form-control-sm" name="P_FEE_TYPE" id="P_FEE_TYPE" required>
                  <?php foreach ($regFeeTypes as $ft) { ?>
                  <option value="<?php echo $ft; ?>"><?php echo $ft; ?></option>
                  <?php } ?>
                </select>
                <small class="form-text text-muted" id="P_ENTRANCE_NOTE" style="display:none;">
                  Entrance Exam only applies to 1st Year students.
                </small>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="P_AMOUNT" class="col-form-label col-form-label-sm">Amount</label>
                <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="P_AMOUNT" id="P_AMOUNT" required>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="P_OR_NUMBER" class="col-form-label col-form-label-sm">OR Number</label>
                <input type="text" class="form-control form-control-sm" name="P_OR_NUMBER" id="P_OR_NUMBER" placeholder="Official Receipt No." required>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="P_DATE_PAID" class="col-form-label col-form-label-sm">Date Paid</label>
                <input type="date" class="form-control form-control-sm" name="P_DATE_PAID" id="P_DATE_PAID" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label for="P_REMARKS" class="col-form-label col-form-label-sm">Remarks</label>
                <input type="text" class="form-control form-control-sm" name="P_REMARKS" id="P_REMARKS" placeholder="Optional">
              </div>
            </div>

          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Payment</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-----END of Add Payment Form---->
