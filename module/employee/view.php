<?php
/* Employee module - single-record profile view. $mydb is ready (initialize.php via template). */
global $mydb;

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$emp = null;
$mydb->setQuery("SELECT e.*, m.FirstName AS MgrFirst, m.LastName AS MgrLast
	FROM `tblemployee` e
	LEFT JOIN `tblemployee` m ON m.EmployeeID = e.ManagerID
	WHERE e.EmployeeID = '".$id."' LIMIT 1");
foreach ($mydb->loadResultList() as $r) { $emp = $r; }

if ($emp === null) {
	echo '<section class="content"><div class="container-fluid"><div class="alert alert-danger">Employee record not found.</div>
	<a href="index.php" class="btn btn-primary">Back to List</a></div></section>';
	return;
}

$fullName = trim($emp->FirstName.' '.($emp->MiddleName ? $emp->MiddleName.' ' : '').$emp->LastName.' '.$emp->Suffix);
$photo    = (!empty($emp->PhotoPath)) ? WEB_ROOT.$emp->PhotoPath :
	'data:image/svg+xml;utf8,'.rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="%23ddd"/><circle cx="50" cy="38" r="18" fill="%23aaa"/><ellipse cx="50" cy="88" rx="30" ry="22" fill="%23aaa"/></svg>');
$manager  = ($emp->MgrLast) ? htmlspecialchars($emp->MgrLast.', '.$emp->MgrFirst) : '-';
?>
<section class="content">
  <div class="container-fluid">
    <div class="row">

      <!-- LEFT: photo card -->
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile text-center">
            <img class="profile-user-img img-fluid img-circle"
                 src="<?php echo $photo; ?>"
                 style="width:120px;height:120px;object-fit:cover;">
            <h3 class="profile-username text-center mt-3"><?php echo htmlspecialchars(strtoupper($fullName)); ?></h3>
            <p class="text-muted text-center">Employee ID: <?php echo htmlspecialchars($emp->EmployeeID); ?></p>

            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Job Title</b> <span class="float-right"><?php echo htmlspecialchars($emp->JobTitle ?: '-'); ?></span>
              </li>
              <li class="list-group-item">
                <b>Status</b>
                <span class="float-right">
                  <?php if ($emp->IsActive == 1) { ?>
                    <span class="badge badge-success">Active</span>
                  <?php } else { ?>
                    <span class="badge badge-secondary">Inactive</span>
                  <?php } ?>
                </span>
              </li>
            </ul>

            <a href="index.php" class="btn btn-primary btn-block"><i class="fa fa-arrow-left"></i> Back to List</a>
          </div>
        </div>
      </div>

      <!-- RIGHT: details -->
      <div class="col-md-8">
        <div class="card card-primary card-outline">
          <div class="card-header p-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Profile Info</a></li>
              <li class="nav-item"><a class="nav-link" href="#contact" data-toggle="tab">Contact Info</a></li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content">

              <div class="active tab-pane" id="profile">
                <table class="table table-bordered">
                  <tr><th style="width:30%">First Name</th><td><?php echo htmlspecialchars($emp->FirstName); ?></td></tr>
                  <tr><th>Middle Name</th><td><?php echo htmlspecialchars($emp->MiddleName ?: '-'); ?></td></tr>
                  <tr><th>Last Name</th><td><?php echo htmlspecialchars($emp->LastName); ?></td></tr>
                  <tr><th>Suffix</th><td><?php echo htmlspecialchars($emp->Suffix ?: '-'); ?></td></tr>
                  <tr><th>Date of Birth</th><td><?php echo ($emp->DateOfBirth && $emp->DateOfBirth != '0000-00-00') ? htmlspecialchars(substr($emp->DateOfBirth,0,10)) : '-'; ?></td></tr>
                  <tr><th>Department</th><td><?php echo htmlspecialchars($emp->Department ?: '-'); ?></td></tr>
                  <tr><th>Manager</th><td><?php echo $manager; ?></td></tr>
                  <tr><th>Hire Date</th><td><?php echo htmlspecialchars(substr($emp->HireDate,0,10)); ?></td></tr>
                  <tr><th>Salary</th><td><?php echo ($emp->Salary !== null) ? number_format($emp->Salary, 2) : '-'; ?></td></tr>
                </table>
              </div>

              <div class="tab-pane" id="contact">
                <table class="table table-bordered">
                  <tr><th style="width:30%">Email</th><td><?php echo htmlspecialchars($emp->Email); ?></td></tr>
                  <tr><th>Phone</th><td><?php echo htmlspecialchars($emp->Phone ?: '-'); ?></td></tr>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>