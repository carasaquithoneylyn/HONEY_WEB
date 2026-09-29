<?php
/* Dropdown data for the Register Slot modal. $mydb is set up by
   include/initialize.php, which template.php has already loaded.

   NOTE: there is no Section field here on purpose. Sectioning is the
   SECOND stage and happens on the Enrollment screen. */
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
?>
 <section class="content">
      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">
          
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Students</h3>
              </div>

              <!-- /.card-header -->
              <div class="card-body">
                <table id="tblstudent" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>#</th>
                    <!-- /`LNAME`, `FNAME`, `MNAME`, `SEX`, `BDAY`-->
                    <th>LNAME</th>
                    <th>FNAME</th>
                    <th>MNAME</th>
                    <th>SEX</th>
                      <th>BDAY</th>

                   
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  
                  </tbody>
                  <tfoot>
                  
                  </tfoot>
                </table>
                  <div class="btn-group">
          
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddNewEntry">Add New</button>
                 
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>


<div class="modal fade" id="AddNewEntry">
        <div class="modal-dialog modal-lg">
        <form action="controller.php?action=add" enctype="multipart/form-data" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add New Student</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <div class="row">

                <!-- Photo -->
                <div class="col-md-3 text-center">
                  <div class="form-group">
                    <img id="img-upload" src="<?php echo WEB_ROOT; ?>module/student/image/default.png"
                         class="img-circle" style="width:100px; height:100px; object-fit:cover;" alt="Student photo">
                  </div>
                  <div class="form-group">
                    <span class="btn btn-default btn-file btn-sm btn-block">
                      Browse
                      <input type="file" name="PHOTO" id="imgInp" accept="image/*">
                    </span>
                  </div>
                </div>

                <!-- Fields -->
                <div class="col-md-9">
                  <ul class="nav nav-pills mb-2">
                    <li class="nav-item"><a class="nav-link active" href="#addProfile" data-toggle="tab">Profile Info</a></li>
                    <li class="nav-item"><a class="nav-link" href="#addContact" data-toggle="tab">Contact Info</a></li>
                  </ul>

                  <div class="tab-content">

                    <div class="active tab-pane" id="addProfile">
                      <div class="row">

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="IDNO" class="col-form-label col-form-label-sm">Student ID Number</label>
                            <input type="text" class="form-control form-control-sm" name="IDNO"
                            id="IDNO" placeholder="Enter Student ID Number" required>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="LRN_NO" class="col-form-label col-form-label-sm">LRN No.</label>
                            <input type="text" class="form-control form-control-sm" name="LRN_NO"
                            id="LRN_NO" placeholder="Enter LRN Number">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="FNAME" class="col-form-label col-form-label-sm">First Name</label>
                            <input type="text" class="form-control form-control-sm" name="FNAME"
                            id="FNAME" placeholder="Enter First N." required>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="MNAME" class="col-form-label col-form-label-sm">Middle Name</label>
                            <input type="text" class="form-control form-control-sm" name="MNAME"
                            id="MNAME" placeholder="Enter Middle">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="LNAME" class="col-form-label col-form-label-sm">Last Name</label>
                            <input type="text" class="form-control form-control-sm" name="LNAME"
                            id="LNAME" placeholder="Enter Last N." required>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="SEX" class="col-form-label col-form-label-sm">Gender</label>
                            <select class="form-control form-control-sm" name="SEX" id="SEX" required>
                              <option value="">Select Ge...</option>
                              <option value="Male">Male</option>
                              <option value="Female">Female</option>
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="BDAY" class="col-form-label col-form-label-sm">Birthday</label>
                            <input type="date" class="form-control form-control-sm" name="BDAY"
                            id="BDAY" required>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="AGE" class="col-form-label col-form-label-sm">Age</label>
                            <input type="number" min="0" class="form-control form-control-sm" name="AGE"
                            id="AGE" placeholder="Enter Age">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="BPLACE" class="col-form-label col-form-label-sm">Birth Place</label>
                            <input type="text" class="form-control form-control-sm" name="BPLACE"
                            id="BPLACE" placeholder="Enter Birth Place">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="NATIONALITY" class="col-form-label col-form-label-sm">Nationality</label>
                            <input type="text" class="form-control form-control-sm" name="NATIONALITY"
                            id="NATIONALITY" placeholder="Enter Nationality">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="RELIGION" class="col-form-label col-form-label-sm">Religion</label>
                            <input type="text" class="form-control form-control-sm" name="RELIGION"
                            id="RELIGION" placeholder="Enter Religion">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="COURSE_ID" class="col-form-label col-form-label-sm">Course</label>
                            <select class="form-control form-control-sm" name="COURSE_ID" id="COURSE_ID">
                              <option value="">Select Course</option>
                              <?php foreach ($regCourses as $c) { ?>
                              <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="STATUS" class="col-form-label col-form-label-sm">Status</label>
                            <select class="form-control form-control-sm" name="STATUS" id="STATUS">
                              <option value="Active">Active</option>
                              <option value="Inactive">Inactive</option>
                            </select>
                          </div>
                        </div>

                      </div>
                    </div>
                    <!-- /.tab-pane addProfile -->

                    <div class="tab-pane" id="addContact">
                      <div class="row">

                        <div class="col-sm-12">
                          <div class="form-group">
                            <label for="CONTACT_NO" class="col-form-label col-form-label-sm">Contact Number</label>
                            <input type="text" class="form-control form-control-sm" name="CONTACT_NO"
                            id="CONTACT_NO" placeholder="Enter Contact Number">
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="form-group">
                            <label for="EMAIL" class="col-form-label col-form-label-sm">Email</label>
                            <input type="email" class="form-control form-control-sm" name="EMAIL"
                            id="EMAIL" placeholder="Enter Email">
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="form-group">
                            <label for="HOME_ADD" class="col-form-label col-form-label-sm">Home Address</label>
                            <input type="text" class="form-control form-control-sm" name="HOME_ADD"
                            id="HOME_ADD" placeholder="Enter Home Address">
                          </div>
                        </div>

                      </div>
                    </div>
                    <!-- /.tab-pane addContact -->

                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- /.col-md-9 -->

              </div>
              <!-- /.row -->

            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="save">Save changes</button>
             
            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
<!-----END of Add Form---->


   <div class="modal fade" id="editEntry">
        <div class="modal-dialog modal-lg">
        <form action="controller.php?action=edit" enctype="multipart/form-data" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Edit Student</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <!-- carries S_ID of the row being edited. Hidden: it is not
                   something the user should ever see or type into. -->
              <input type="hidden" name="UID" id="UID" value="">

              <div class="row">

                <!-- Photo -->
                <div class="col-md-3 text-center">
                  <div class="form-group">
                    <img id="img-upload1" src="<?php echo WEB_ROOT; ?>module/student/image/default.png"
                         class="img-circle" style="width:100px; height:100px; object-fit:cover;" alt="Student photo">
                  </div>
                  <div class="form-group">
                    <span class="btn btn-default btn-file btn-sm btn-block">
                      Browse
                      <input type="file" name="PHOTO" id="imgInp1" accept="image/*">
                    </span>
                  </div>
                </div>

                <!-- Fields -->
                <div class="col-md-9">
                  <ul class="nav nav-pills mb-2">
                    <li class="nav-item"><a class="nav-link active" href="#editProfile" data-toggle="tab">Profile Info</a></li>
                    <li class="nav-item"><a class="nav-link" href="#editContact" data-toggle="tab">Contact Info</a></li>
                  </ul>

                  <div class="tab-content">

                    <div class="active tab-pane" id="editProfile">
                      <div class="row">

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="IDNO1" class="col-form-label col-form-label-sm">Student ID Number</label>
                            <input type="text" class="form-control form-control-sm" name="IDNO1"
                            id="IDNO1" placeholder="Enter Student ID Number" required>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="LRN_NO1" class="col-form-label col-form-label-sm">LRN No.</label>
                            <input type="text" class="form-control form-control-sm" name="LRN_NO1"
                            id="LRN_NO1" placeholder="Enter LRN Number">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="FNAME1" class="col-form-label col-form-label-sm">First Name</label>
                            <input type="text" class="form-control form-control-sm" name="FNAME1"
                            id="FNAME1" placeholder="Enter First N." required>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="MNAME1" class="col-form-label col-form-label-sm">Middle Name</label>
                            <input type="text" class="form-control form-control-sm" name="MNAME1"
                            id="MNAME1" placeholder="Enter Middle">
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="LNAME1" class="col-form-label col-form-label-sm">Last Name</label>
                            <input type="text" class="form-control form-control-sm" name="LNAME1"
                            id="LNAME1" placeholder="Enter Last N." required>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="SEX1" class="col-form-label col-form-label-sm">Gender</label>
                            <select class="form-control form-control-sm" name="SEX1" id="SEX1" required>
                              <option value="">Select Ge...</option>
                              <option value="Male">Male</option>
                              <option value="Female">Female</option>
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="BDAY1" class="col-form-label col-form-label-sm">Birthday</label>
                            <input type="date" class="form-control form-control-sm" name="BDAY1"
                            id="BDAY1" required>
                          </div>
                        </div>

                        <div class="col-sm-4">
                          <div class="form-group">
                            <label for="AGE1" class="col-form-label col-form-label-sm">Age</label>
                            <input type="number" min="0" class="form-control form-control-sm" name="AGE1"
                            id="AGE1" placeholder="Enter Age">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="BPLACE1" class="col-form-label col-form-label-sm">Birth Place</label>
                            <input type="text" class="form-control form-control-sm" name="BPLACE1"
                            id="BPLACE1" placeholder="Enter Birth Place">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="NATIONALITY1" class="col-form-label col-form-label-sm">Nationality</label>
                            <input type="text" class="form-control form-control-sm" name="NATIONALITY1"
                            id="NATIONALITY1" placeholder="Enter Nationality">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="RELIGION1" class="col-form-label col-form-label-sm">Religion</label>
                            <input type="text" class="form-control form-control-sm" name="RELIGION1"
                            id="RELIGION1" placeholder="Enter Religion">
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="COURSE_ID1" class="col-form-label col-form-label-sm">Course</label>
                            <select class="form-control form-control-sm" name="COURSE_ID1" id="COURSE_ID1">
                              <option value="">Select Course</option>
                              <?php foreach ($regCourses as $c) { ?>
                              <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>

                        <div class="col-sm-6">
                          <div class="form-group">
                            <label for="STATUS1" class="col-form-label col-form-label-sm">Status</label>
                            <select class="form-control form-control-sm" name="STATUS1" id="STATUS1">
                              <option value="Active">Active</option>
                              <option value="Inactive">Inactive</option>
                            </select>
                          </div>
                        </div>

                      </div>
                    </div>
                    <!-- /.tab-pane editProfile -->

                    <div class="tab-pane" id="editContact">
                      <div class="row">

                        <div class="col-sm-12">
                          <div class="form-group">
                            <label for="CONTACT_NO1" class="col-form-label col-form-label-sm">Contact Number</label>
                            <input type="text" class="form-control form-control-sm" name="CONTACT_NO1"
                            id="CONTACT_NO1" placeholder="Enter Contact Number">
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="form-group">
                            <label for="EMAIL1" class="col-form-label col-form-label-sm">Email</label>
                            <input type="email" class="form-control form-control-sm" name="EMAIL1"
                            id="EMAIL1" placeholder="Enter Email">
                          </div>
                        </div>

                        <div class="col-sm-12">
                          <div class="form-group">
                            <label for="HOME_ADD1" class="col-form-label col-form-label-sm">Home Address</label>
                            <input type="text" class="form-control form-control-sm" name="HOME_ADD1"
                            id="HOME_ADD1" placeholder="Enter Home Address">
                          </div>
                        </div>

                      </div>
                    </div>
                    <!-- /.tab-pane editContact -->

                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- /.col-md-9 -->

              </div>
              <!-- /.row -->

            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="edit">Save changes</button>
             
            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
<!-----END of Edit Form---->


<!-----STAGE 1: REGISTER SLOT---->
<!--
     Opened by the green Reg button in the Action column.

     This creates the enrollment record with STATUS = Registered. It does
     NOT assign a section and does NOT set the enrollment date, because
     at this point in the flow neither is known yet. Both are filled in
     later from the Enrollment screen (Sectioning).
-->
   <div class="modal fade" id="registerEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=register" method="POST">
          <div class="modal-content">
            <div class="modal-header bg-success">
              <h4 class="modal-title"><i class="fa fa-clipboard-check"></i> &nbsp;Register Enrollment Slot</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <input type="hidden" name="R_SID" id="R_SID" value="">

              <!-- Who is being registered. Read only: the student comes from
                   whichever row's button was clicked, not from typing. -->
              <div class="callout callout-info py-2 mb-3">
                <div class="row">
                  <div class="col-sm-5">
                    <small class="text-muted d-block">ID No.</small>
                    <strong id="R_IDNO_TEXT">-</strong>
                  </div>
                  <div class="col-sm-7">
                    <small class="text-muted d-block">Student Name</small>
                    <strong id="R_NAME_TEXT">-</strong>
                  </div>
                </div>
              </div>

              <div class="row">

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="R_SY" class="col-form-label col-form-label-sm">Academic Year</label>
                    <select class="form-control form-control-sm" name="R_SY" id="R_SY" required>
                      <option value="">Select Academic Year</option>
                      <?php foreach ($regSchoolYears as $sy) { ?>
                      <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?><?php echo ($sy->STATUS == 'Active') ? ' (Active)' : ''; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="R_SEMESTER" class="col-form-label col-form-label-sm">Semester</label>
                    <select class="form-control form-control-sm" name="R_SEMESTER" id="R_SEMESTER" required>
                      <option value="">Select Semester</option>
                      <?php foreach ($regSemesters as $sem) { ?>
                      <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="R_COURSE" class="col-form-label col-form-label-sm">Course</label>
                    <select class="form-control form-control-sm" name="R_COURSE" id="R_COURSE" required>
                      <option value="">Select Course</option>
                      <?php foreach ($regCourses as $c) { ?>
                      <option value="<?php echo $c->COURSE_ID; ?>"><?php echo htmlspecialchars($c->COURSE_CODE.' - '.$c->COURSE_NAME); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="R_YEARLEVEL" class="col-form-label col-form-label-sm">Year Level</label>
                    <select class="form-control form-control-sm" name="R_YEARLEVEL" id="R_YEARLEVEL" required>
                      <option value="">Select Year Level</option>
                      <?php foreach ($regYearLevels as $yl) { ?>
                      <option value="<?php echo $yl; ?>"><?php echo $yl; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="R_CURRICULUM" class="col-form-label col-form-label-sm">Curriculum Yr</label>
                    <input type="text" class="form-control form-control-sm" name="R_CURRICULUM"
                           id="R_CURRICULUM" placeholder="e.g. 2023-2024">
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="R_CATEGORY" class="col-form-label col-form-label-sm">Category</label>
                    <select class="form-control form-control-sm" name="R_CATEGORY" id="R_CATEGORY" required>
                      <?php foreach ($regCategories as $cat) { ?>
                      <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="R_DATE_RESERVED" class="col-form-label col-form-label-sm">Date Registered</label>
                    <input type="date" class="form-control form-control-sm" name="R_DATE_RESERVED"
                           id="R_DATE_RESERVED" value="<?php echo date('Y-m-d'); ?>" required>
                  </div>
                </div>

              </div>

              <div class="callout callout-warning py-2 mb-0">
                <small>
                  Section and Date Enrolled are assigned later, from
                  <strong>Enrollment &gt; Sectioning</strong>. This step only
                  registers the slot.
                </small>
              </div>

            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success" name="register"><i class="fa fa-save"></i> Register</button>
            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
<!-----END of Register Form---->




<?php


?>