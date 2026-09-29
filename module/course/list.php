<section class="content">

   <!-- ABADIES SOLUTIONS -->

      <div class="container-fluid">
         <?php check_message(); ?>
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">List of Courses</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="tblcourse" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Status</th>
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

<!-----START of Add Form---->
     <div class="modal fade" id="AddNewEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=add" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add New Course</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <!-- /.card-header -->
              <div class="row">

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="COURSE_CODE"  class="col-form-label col-form-label-sm">Course Code</label>
                        <input type="text" class="form-control form-control-sm" name="COURSE_CODE"
                        id="COURSE_CODE" placeholder="e.g. BSIT" required>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="COURSE_NAME"  class="col-form-label col-form-label-sm">Course Name</label>
                        <input type="text" class="form-control form-control-sm" name="COURSE_NAME"
                        id="COURSE_NAME" placeholder="e.g. Bachelor of Science in Information Technology" required>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="COURSE_DESC"  class="col-form-label col-form-label-sm">Description</label>
                        <textarea class="form-control form-control-sm" name="COURSE_DESC"
                        id="COURSE_DESC" placeholder="Short description"></textarea>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="STATUS" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="STATUS" required>
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                        </select>
                      </div>
                    </div>

                  </div>

              <!-- /.card-body -->

            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="save" type="submit">Save changes</button>

            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>

<!-----End of Add Form---->
<!-----Start of edit Form---->
<div class="modal fade" id="editEntry">
        <div class="modal-dialog">
        <form action="controller.php?action=edit" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Modify Course</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">

              <!-- /.card-header -->
              <div class="row">
                    <input type="hidden" name="COURSE_ID" id="COURSE_ID">
                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="COURSE_CODE1"  class="col-form-label col-form-label-sm">Course Code</label>
                        <input type="text" class="form-control form-control-sm" name="COURSE_CODE1"
                        id="COURSE_CODE1" placeholder="e.g. BSIT">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="COURSE_NAME1"  class="col-form-label col-form-label-sm">Course Name</label>
                        <input type="text" class="form-control form-control-sm" name="COURSE_NAME1"
                        id="COURSE_NAME1" placeholder="Course Name">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="COURSE_DESC1"  class="col-form-label col-form-label-sm">Description</label>
                        <textarea class="form-control form-control-sm" name="COURSE_DESC1"
                        id="COURSE_DESC1" placeholder="Short description"></textarea>
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                       <label for="STATUS1" class="col-form-label col-form-label-sm">Status</label>
                        <select class="form-control form-control-sm" name="STATUS1" id="STATUS1">
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                        </select>
                      </div>
                    </div>

                  </div>

              <!-- /.card-body -->

            </div>
            <div class="modal-footer justify-content-between">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                 <button type="submit" class="btn btn-primary" name="edit" type="submit">Save changes</button>

            </div>
          </div>
          </form>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
