<?php
// ST. JOSEPH CATHOLIC SCHOOL OF SAGAY INC. - Course Details

$course = null;

if (isset($_GET['id']) && $_GET['id'] != '') {
    $mydb->setQuery("SELECT * FROM `tblcourses` WHERE `COURSE_ID`='".(int)$_GET['id']."' LIMIT 1");
    $course = $mydb->loadSingleResult();
}

function course_status_badge($status) {
    $s = trim((string)$status);
    $cls = (strcasecmp($s, 'Active') === 0) ? 'success' : 'secondary';
    return '<span class="badge badge-'.$cls.'">'.htmlspecialchars($s).'</span>';
}
?>

<section class="content">
  <div class="container-fluid">

  <?php if (!$course): ?>
    <div class="alert alert-warning">
      No course was selected. Please go back to the <a href="<?php echo WEB_ROOT; ?>module/course/">course list</a> and click the view button of a course.
    </div>

  <?php else: ?>

    <div class="row">

      <!-- Left: identity card -->
      <div class="col-md-5">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile text-center">
            <div class="text-center" style="margin: 14px 0;">
              <i class="fa fa-graduation-cap" style="font-size:64px; color:var(--hark-blue);"></i>
            </div>
            <h3 class="profile-username"><?php echo htmlspecialchars($course->COURSE_CODE); ?></h3>
            <p class="text-muted"><?php echo htmlspecialchars($course->COURSE_NAME); ?></p>
            <p><?php echo course_status_badge($course->STATUS); ?></p>
          </div>
        </div>
      </div>

      <!-- Right: information table -->
      <div class="col-md-7">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Course Information</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <tr>
                <th style="width:30%">Course Code</th>
                <td><?php echo htmlspecialchars($course->COURSE_CODE); ?></td>
              </tr>
              <tr>
                <th>Course Name</th>
                <td><?php echo htmlspecialchars($course->COURSE_NAME); ?></td>
              </tr>
              <tr>
                <th>Description</th>
                <td><?php echo htmlspecialchars($course->COURSE_DESC); ?></td>
              </tr>
              <tr>
                <th>Status</th>
                <td><?php echo course_status_badge($course->STATUS); ?></td>
              </tr>
            </table>
            <a href="<?php echo WEB_ROOT; ?>module/course/" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back to List
            </a>
          </div>
        </div>
      </div>

    </div>
    <!-- /.row -->

  <?php endif; ?>

  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
