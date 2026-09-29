<?php
/* Enrollment module - Stage 2: Sectioning.
   Dropdown data shared by the Sectioning and Edit Enrollment modals.
   $mydb is set up by include/initialize.php, which template.php has
   already loaded. */
global $mydb;

$regSchoolYears = array();
$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR, STATUS FROM `tblschoolyear` ORDER BY SCHOOL_YEAR DESC");
foreach ($mydb->loadResultList() as $r) { $regSchoolYears[] = $r; }

$regCourses = array();
$mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME FROM `tblcourses` WHERE STATUS = 'Active' ORDER BY COURSE_CODE ASC");
foreach ($mydb->loadResultList() as $r) { $regCourses[] = $r; }

$regYearLevels = array('1st Year', '2nd Year', '3rd Year', '4th Year');
$regSemesters  = array('1st Semester', '2nd Semester', 'Summer');
$regCategories = array('New', 'Old', 'Transferee', 'Returnee', 'Shiftee');
$regStatuses   = array('Registered', 'Enrolled', 'Dropped', 'Completed');
?>
<style>
  @media print {
    .main-header, .main-sidebar, .content-header, .main-footer,
    #statusFilters, #printEnrollment, td:last-child, th:last-child, .brand-link { display: none !important; }
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
            <h3 class="card-title">Enrollment Records</h3>
            <div class="card-tools">
              <div class="btn-group" id="statusFilters">
                <button type="button" class="btn btn-sm btn-outline-secondary active" data-filter="">All</button>
                <button type="button" class="btn btn-sm btn-outline-warning" data-filter="Registered">Registered</button>
                <button type="button" class="btn btn-sm btn-outline-success" data-filter="Enrolled">Enrolled</button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-filter="Dropped">Dropped</button>
                <button type="button" class="btn btn-sm btn-outline-info" data-filter="Completed">Completed</button>
              </div>
              <button type="button" id="printEnrollment" class="btn btn-sm btn-default ml-2">
                <i class="fa fa-print"></i> Print
              </button>
            </div>
          </div>
          <div class="card-body">
            <table id="tblenrollmentlist" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Name</th>
                  <th>Course</th>
                  <th>School Year</th>
                  <th>Semester</th>
                  <th>Year Level</th>
                  <th>Category</th>
                  <th>Section</th>
                  <th>Date Reserved</th>
                  <th>Date Enrolled</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== STAGE 2: SECTIONING ===================== -->
<div class="modal fade" id="sectioningModal">
  <div class="modal-dialog">
    <form action="controller.php?action=section" method="POST">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title"><i class="fa fa-chalkboard"></i>&nbsp; Sectioning</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="SEC_EID" id="SEC_EID" value="">
          <input type="hidden" name="SEC_COURSE" id="SEC_COURSE" value="">
          <input type="hidden" name="SEC_SY" id="SEC_SY" value="">

          <div class="row mb-3">
            <div class="col-sm-3 text-center">
              <img id="SEC_PICTURE" src="" class="img-circle" style="width:80px; height:80px; object-fit:cover;" alt="Student photo">
            </div>
            <div class="col-sm-9">
              <small class="text-muted d-block">ID No.</small>
              <strong id="SEC_IDNO_TEXT">-</strong>
              <small class="text-muted d-block mt-2">Student Name</small>
              <strong id="SEC_NAME_TEXT">-</strong>
            </div>
          </div>

          <div class="callout callout-info py-2 mb-3">
            <small class="text-muted d-block">Course</small>
            <strong id="SEC_COURSE_TEXT">-</strong>
            <small class="text-muted d-block mt-2">Academic Year / Semester</small>
            <strong id="SEC_TERM_TEXT">-</strong>
          </div>

          <div class="form-group">
            <label for="SEC_SECTION" class="col-form-label col-form-label-sm">Section</label>
            <select class="form-control form-control-sm" name="SEC_SECTION" id="SEC_SECTION" required>
              <option value="">Select course and academic year first</option>
            </select>
          </div>

          <div class="form-group">
            <label for="SEC_DATE" class="col-form-label col-form-label-sm">Date Enrolled</label>
            <input type="date" class="form-control form-control-sm" name="SEC_DATE" id="SEC_DATE">
          </div>

          <div class="form-group">
            <label for="SEC_STATUS" class="col-form-label col-form-label-sm">Status</label>
            <select class="form-control form-control-sm" name="SEC_STATUS" id="SEC_STATUS">
              <?php foreach ($regStatuses as $st) { ?>
              <option value="<?php echo $st; ?>"><?php echo $st; ?></option>
              <?php } ?>
            </select>
          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Sectioning</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-----END of Sectioning Form---->


<!-- ===================== EDIT ENROLLMENT ===================== -->
<div class="modal fade" id="editEnrollmentModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=edit" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Edit Enrollment</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="E_EID" id="E_EID" value="">

          <div class="row mb-3">
            <div class="col-sm-2 text-center">
              <img id="E_PICTURE" src="" class="img-circle" style="width:70px; height:70px; object-fit:cover;" alt="Student photo">
            </div>
            <div class="col-sm-10">
              <small class="text-muted d-block">ID No.</small>
              <strong id="E_IDNO_TEXT">-</strong>
              <small class="text-muted d-block mt-2">Student Name</small>
              <strong id="E_NAME_TEXT">-</strong>
            </div>
          </div>

          <div class="row">

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_SY" class="col-form-label col-form-label-sm">Academic Year</label>
                <select class="form-control form-control-sm" name="E_SY" id="E_SY" required>
                  <option value="">Select Academic Year</option>
                  <?php foreach ($regSchoolYears as $sy) { ?>
                  <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?><?php echo ($sy->STATUS == 'Active') ? ' (Active)' : ''; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_SEMESTER" class="col-form-label col-form-label-sm">Semester</label>
                <select class="form-control form-control-sm" name="E_SEMESTER" id="E_SEMESTER" required>
                  <option value="">Select Semester</option>
                  <?php foreach ($regSemesters as $sem) { ?>
                  <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-12">
              <div class="form-group">
                <label for="E_COURSE" class="col-form-label col-form-label-sm">Course</label>
                <select class="form-control form-control-sm" name="E_COURSE" id="E_COURSE" required>
                  <option value="">Select Course</option>
                  <?php foreach ($regCourses as $c) { ?>
                  <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_YEARLEVEL" class="col-form-label col-form-label-sm">Year Level</label>
                <select class="form-control form-control-sm" name="E_YEARLEVEL" id="E_YEARLEVEL" required>
                  <option value="">Select Year Level</option>
                  <?php foreach ($regYearLevels as $yl) { ?>
                  <option value="<?php echo $yl; ?>"><?php echo $yl; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_SECTION" class="col-form-label col-form-label-sm">Section</label>
                <select class="form-control form-control-sm" name="E_SECTION" id="E_SECTION">
                  <option value="">Select course and academic year first</option>
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_CURRICULUM" class="col-form-label col-form-label-sm">Curriculum Yr</label>
                <input type="text" class="form-control form-control-sm" name="E_CURRICULUM" id="E_CURRICULUM" placeholder="e.g. 2023-2024">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_CATEGORY" class="col-form-label col-form-label-sm">Category</label>
                <select class="form-control form-control-sm" name="E_CATEGORY" id="E_CATEGORY">
                  <?php foreach ($regCategories as $cat) { ?>
                  <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_DATE_RESERVED" class="col-form-label col-form-label-sm">Date Reserved</label>
                <input type="date" class="form-control form-control-sm" name="E_DATE_RESERVED" id="E_DATE_RESERVED">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_DATE_ENROLLED" class="col-form-label col-form-label-sm">Date Enrolled</label>
                <input type="date" class="form-control form-control-sm" name="E_DATE_ENROLLED" id="E_DATE_ENROLLED">
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="E_STATUS" class="col-form-label col-form-label-sm">Status</label>
                <select class="form-control form-control-sm" name="E_STATUS" id="E_STATUS">
                  <?php foreach ($regStatuses as $st) { ?>
                  <option value="<?php echo $st; ?>"><?php echo $st; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>

          </div>

        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save changes</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-----END of Edit Enrollment Form---->