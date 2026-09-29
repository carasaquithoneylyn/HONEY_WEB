<?php
// ABADIES SOLUTIONS

require_once(__DIR__."/../generic/config.php");
global $mydb;

/* Brand shown on this page. Only these two lines change per build
   (Rojhon / Joshua / Hipanao / Helly), nothing further down. */
$appName   = 'Abadies Solutions';
$developer = 'Abadies';
$appVersion = '3.0.5';

$mydb->setQuery("SHOW TABLES");
$allTablesRaw = $mydb->loadResultList();
$dbNameKey = 'Tables_in_'.DB_NAME;
$allTables = array();
foreach ($allTablesRaw as $t) {
  foreach ($t as $val) { $allTables[] = $val; break; }
}

$moduleLinks = array(
  'Student'   => WEB_ROOT.'module/student/',
  'Course'    => WEB_ROOT.'module/course/',
  'Subject'   => WEB_ROOT.'module/subject/',
  'User Accounts' => WEB_ROOT.'module/user/',
  'User Type' => WEB_ROOT.'module/usertype/',
);
foreach ($GENERIC_TABLES as $tblKey => $tblCfg) {
  $moduleLinks[$tblCfg['title']] = WEB_ROOT.'module/generic/index.php?t='.urlencode($tblKey);
}
?>
<section class="content">
  <div class="container-fluid">

    <div class="row">
      <div class="col-md-5">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile text-center">
            <img src="<?php echo WEB_ROOT; ?>csr-scc.png" class="profile-user-img img-fluid" style="max-width:120px;">
            <h3 class="profile-username mt-3"><?php echo htmlspecialchars($appName); ?></h3>
            <p class="text-muted">Student &amp; Alumni Records Management System</p>

            <!--
              Each row is a flex row: label pinned left, value pinned right.

              The old markup used <b>Label</b> <a class="float-right">Value</a>.
              That could not work here, because the card-body above carries
              text-center. A float pulls the value out of the normal flow, so
              the label was left centering itself inside the leftover space
              instead of sitting at the left edge.

              d-flex + justify-content-between ignores the inherited
              text-center for these rows and pushes the two children to
              opposite ends, which is the alignment that was wanted.
              text-left is added so the label text itself is not centered
              inside its own box.
            -->
            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item d-flex justify-content-between align-items-center text-left">
                <b>Version</b>
                <span class="text-muted"><?php echo htmlspecialchars($appVersion); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center text-left">
                <b>Developer</b>
                <span class="text-muted"><?php echo htmlspecialchars($developer); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center text-left">
                <b>Database</b>
                <span class="text-muted"><?php echo htmlspecialchars(DB_NAME); ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center text-left">
                <b>Tables Connected</b>
                <span class="text-muted"><?php echo count($allTables); ?></span>
              </li>
            </ul>
          </div>
        </div>

        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Built With</h3>
          </div>
          <div class="card-body">
            <ul>
              <li>PHP + PDO / MySQL</li>
              <li>AdminLTE 3 (Bootstrap 4)</li>
              <li>DataTables (server-side paging &amp; search)</li>
              <li>SweetAlert2 for confirmations &amp; notices</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-7">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Modules</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Module</th>
                  <th class="text-right">Open</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($moduleLinks as $label => $url): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($label); ?></td>
                    <td class="text-right">
                      <a href="<?php echo $url; ?>" class="btn btn-outline-primary btn-xs">Open <i class="fas fa-arrow-circle-right"></i></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-outline card-info">
          <div class="card-header">
            <h3 class="card-title">All Database Tables</h3>
          </div>
          <div class="card-body">
            <?php foreach ($allTables as $t): ?>
              <span class="badge badge-info mr-1 mb-1" style="font-size: 95%;"><?php echo htmlspecialchars($t); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>