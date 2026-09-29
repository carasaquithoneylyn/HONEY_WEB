<?php
/* =====================================================================
   ABADIES SOLUTIONS - dashboard

   Every figure on this page is read live from the database. Nothing is
   hardcoded and nothing is invented: where a table is missing the tile
   degrades to zero rather than taking the page down, and the "vs last
   month" deltas are computed from real dated rows.
   ===================================================================== */

global $mydb;

/* --- small helpers ---------------------------------------------------- */
if (!function_exists('dash_count')) {

  /* COUNT(*) over a whole table, guarded by tableExists(). */
  function dash_count($table, $where = '') {
    global $mydb;
    if (!$mydb->tableExists($table)) { return 0; }
    $mydb->setQuery("SELECT COUNT(*) AS c FROM `".$table."` ".$where);
    $r = $mydb->loadSingleResult();
    return $r ? (int)$r->c : 0;
  }

  /* SUM of a column, guarded the same way. */
  function dash_sum($table, $col, $where = '') {
    global $mydb;
    if (!$mydb->tableExists($table)) { return 0; }
    $mydb->setQuery("SELECT COALESCE(SUM(`".$col."`),0) AS s FROM `".$table."` ".$where);
    $r = $mydb->loadSingleResult();
    return $r ? (float)$r->s : 0;
  }

  /* Percent change between two periods, rendered as the little coloured
     line under each stat. Returns ready-to-print HTML. */
  function dash_delta($now, $before, $noun = 'vs last month') {
    if ($before == 0) {
      if ($now == 0) {
        return '<span class="stat-delta flat"><i class="fas fa-minus"></i> no change</span> '.$noun;
      }
      return '<span class="stat-delta up"><i class="fas fa-arrow-up"></i> new</span> '.$noun;
    }
    $pct = (($now - $before) / $before) * 100;
    $cls = ($pct > 0) ? 'up' : (($pct < 0) ? 'down' : 'flat');
    $ico = ($pct > 0) ? 'fa-arrow-up' : (($pct < 0) ? 'fa-arrow-down' : 'fa-minus');
    $sig = ($pct > 0) ? '+' : '';
    return '<span class="stat-delta '.$cls.'"><i class="fas '.$ico.'"></i> '.$sig.number_format($pct, 1).'%</span> '.$noun;
  }
}

/* --- period boundaries ------------------------------------------------ */
$thisMonth = date('Y-m-01');
$lastMonth = date('Y-m-01', strtotime('-1 month'));

/* --- headline figures ------------------------------------------------- */
$totalStudent  = dash_count('tblstudent');
$totalEmployee = dash_count('tblemployee');
$totalEnroll   = dash_count('tblenrollment');
$totalAlumni   = dash_count('alumni_details');
$totalCourse   = dash_count('tblcourses');
$totalSubject  = dash_count('tblsubjects');
$totalSection  = dash_count('tblsections');
$totalGrades   = dash_count('tblgrades');

$collected     = dash_sum('tblpayments', 'AMOUNT');
$collectedNow  = dash_sum('tblpayments', 'AMOUNT', "WHERE DATE_PAID >= '".$thisMonth."'");
$collectedPrev = dash_sum('tblpayments', 'AMOUNT', "WHERE DATE_PAID >= '".$lastMonth."' AND DATE_PAID < '".$thisMonth."'");

$enrollNow  = dash_count('tblenrollment', "WHERE DATE_RESERVED >= '".$thisMonth."'");
$enrollPrev = dash_count('tblenrollment', "WHERE DATE_RESERVED >= '".$lastMonth."' AND DATE_RESERVED < '".$thisMonth."'");

$activeEnroll = dash_count('tblenrollment', "WHERE STATUS = 'Enrolled'");
$activeStaff  = dash_count('tblemployee', "WHERE IsActive = 1");

/* Share of enrolments that reached "Enrolled", shown as a completion rate. */
$sectionedPct = ($totalEnroll > 0) ? ($activeEnroll / $totalEnroll) * 100 : 0;

/* --- recent enrolments for the table --------------------------------- */
$recent = array();
if ($mydb->tableExists('tblenrollment') && $mydb->tableExists('tblstudent')) {
  $mydb->setQuery("
    SELECT e.ENROLLMENT_ID, e.YEAR_LEVEL, e.SEMESTER, e.STATUS, e.CATEGORY,
           e.DATE_RESERVED, e.DATE_ENROLLED,
           s.IDNO, s.FNAME, s.LNAME,
           c.COURSE_CODE,
           sec.SECTION_NAME,
           COALESCE(p.PAID, 0) AS PAID
    FROM `tblenrollment` e
    LEFT JOIN `tblstudent` s  ON s.S_ID = e.S_ID
    LEFT JOIN `tblcourses` c  ON c.COURSE_ID = e.COURSE_ID
    LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID
    LEFT JOIN (SELECT ENROLLMENT_ID, SUM(AMOUNT) AS PAID FROM `tblpayments` GROUP BY ENROLLMENT_ID) p
           ON p.ENROLLMENT_ID = e.ENROLLMENT_ID
    ORDER BY e.ENROLLMENT_ID DESC
    LIMIT 6");
  $recent = $mydb->loadResultList();
}

/* Status pill class per enrolment state. */
function dash_status_pill($status) {
  $s = strtolower(trim($status));
  if ($s === 'enrolled')  { return 'ok'; }
  if ($s === 'completed') { return 'info'; }
  if ($s === 'dropped')   { return 'bad'; }
  return 'warn'; // Registered / Reserved / anything else pending
}

/* Rotating tile colours so the rows read like the reference layout. */
$tileTones = array('', 't-3', 't-2', 't-4', 't-1', 't-3');
?>

<section class="content">
  <div class="container-fluid px-0">

    <!-- ======================= WELCOME ======================= -->
    <div class="d-flex flex-wrap align-items-start justify-content-between mb-4" style="gap:1rem;">
      <div class="page-intro">
        <h2>Welcome back, <?php echo htmlspecialchars(isset($_SESSION['DISPLAYNAME']) ? $_SESSION['DISPLAYNAME'] : 'there'); ?>!</h2>
        <p>Student and alumni records, enrolment and collections, all in one place.</p>
      </div>
      <div class="d-flex align-items-center" style="gap:.6rem;">
        <span class="date-pill">
          <i class="far fa-calendar"></i> <?php echo date('j F, Y'); ?>
        </span>
        <a href="<?php echo WEB_ROOT; ?>module/student/index.php?view=list" class="btn btn-primary">
          <i class="fas fa-plus mr-1"></i> Add Student
        </a>
      </div>
    </div>

    <!-- ======================= STAT CARDS ======================= -->
    <div class="stat-grid">

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-icon is-brand"><i class="fas fa-user-graduate"></i></span>
          <span class="stat-label">Total Students</span>
        </div>
        <div class="stat-value"><?php echo number_format($totalStudent); ?></div>
        <div class="stat-foot">
          <?php echo $totalAlumni > 0
            ? '<span class="stat-delta up"><i class="fas fa-user-check"></i> '.number_format($totalAlumni).'</span> alumni on record'
            : '<span class="stat-delta flat"><i class="fas fa-minus"></i> no</span> alumni on record'; ?>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-icon is-info"><i class="fas fa-clipboard-list"></i></span>
          <span class="stat-label">Enrolment Records</span>
        </div>
        <div class="stat-value"><?php echo number_format($totalEnroll); ?></div>
        <div class="stat-foot">
          <?php echo dash_delta($enrollNow, $enrollPrev); ?>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-icon is-pos"><i class="fas fa-money-bill-wave"></i></span>
          <span class="stat-label">Total Collections</span>
        </div>
        <div class="stat-value">P<?php echo number_format($collected, 2); ?></div>
        <div class="stat-foot">
          <?php echo dash_delta($collectedNow, $collectedPrev); ?>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-icon is-warn"><i class="fas fa-user-tie"></i></span>
          <span class="stat-label">Employees</span>
        </div>
        <div class="stat-value"><?php echo number_format($totalEmployee); ?></div>
        <div class="stat-foot">
          <?php echo $activeStaff > 0
            ? '<span class="stat-delta up"><i class="fas fa-check-circle"></i> '.number_format($activeStaff).'</span> currently active'
            : '<span class="stat-delta flat"><i class="fas fa-minus"></i> none</span> currently active'; ?>
        </div>
      </div>
    </div>

    <!-- ======================= RECENT ENROLMENTS ======================= -->
    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title">Recent Enrolments</h3>
          <div class="text-muted" style="font-size:.8rem;">
            <?php echo number_format($activeEnroll); ?> of <?php echo number_format($totalEnroll); ?> records sectioned
            (<?php echo number_format($sectionedPct, 1); ?>%)
          </div>
        </div>
        <a href="<?php echo WEB_ROOT; ?>module/enrollment/index.php" class="card-link-quiet">
          View all <i class="fas fa-angle-right"></i>
        </a>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Year &amp; Term</th>
                <th>Section</th>
                <th>Paid</th>
                <th>Date</th>
                <th>Status</th>
                <th class="text-right">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php if (count($recent) === 0) { ?>
              <tr>
                <td colspan="8" class="text-center text-muted py-5">
                  <i class="fas fa-clipboard-list d-block mb-2" style="font-size:1.6rem; opacity:.3;"></i>
                  No enrolment records yet.
                  <div class="mt-2">
                    <a href="<?php echo WEB_ROOT; ?>module/student/index.php?view=list" class="btn btn-secondary btn-sm">
                      Reserve a slot from the Student module
                    </a>
                  </div>
                </td>
              </tr>
            <?php } else {
              $i = 0;
              foreach ($recent as $r) {
                $name = trim(($r->LNAME ? $r->LNAME.', ' : '').$r->FNAME);
                if ($name === '' || $name === ',') { $name = 'Unnamed student'; }
                $initial = strtoupper(substr($r->LNAME ? $r->LNAME : 'S', 0, 1));
                $shown = ($r->STATUS == 'Enrolled' && $r->DATE_ENROLLED) ? $r->DATE_ENROLLED : $r->DATE_RESERVED;
                $tone = $tileTones[$i % count($tileTones)];
                $i++;
            ?>
              <tr>
                <td>
                  <span class="cell-name">
                    <span class="row-tile <?php echo $tone; ?>"><?php echo htmlspecialchars($initial); ?></span>
                    <span>
                      <?php echo htmlspecialchars($name); ?>
                      <?php if ($r->IDNO) { ?>
                        <div class="text-muted" style="font-size:.76rem; font-weight:400;"><?php echo htmlspecialchars($r->IDNO); ?></div>
                      <?php } ?>
                    </span>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($r->COURSE_CODE ? $r->COURSE_CODE : '-'); ?></td>
                <td class="text-muted">
                  <?php echo htmlspecialchars($r->YEAR_LEVEL ? $r->YEAR_LEVEL : '-'); ?>
                  <?php if ($r->SEMESTER) { echo ' &middot; '.htmlspecialchars($r->SEMESTER); } ?>
                </td>
                <td>
                  <?php echo $r->SECTION_NAME
                    ? htmlspecialchars($r->SECTION_NAME)
                    : '<span class="text-muted">Not sectioned</span>'; ?>
                </td>
                <td class="cell-num">
                  <?php echo ($r->PAID > 0)
                    ? 'P'.number_format($r->PAID, 2)
                    : '<span class="text-muted">-</span>'; ?>
                </td>
                <td>
                  <span class="cell-meta">
                    <i class="far fa-clock"></i>
                    <?php echo $shown ? date('j M Y', strtotime($shown)) : 'No date'; ?>
                  </span>
                </td>
                <td>
                  <span class="pill <?php echo dash_status_pill($r->STATUS); ?>">
                    <?php echo htmlspecialchars($r->STATUS); ?>
                  </span>
                </td>
                <td class="text-right">
                  <a href="<?php echo WEB_ROOT; ?>module/enrollment/index.php?view=view&id=<?php echo (int)$r->ENROLLMENT_ID; ?>"
                     class="card-link-quiet">View</a>
                  <span class="text-muted mx-1">&middot;</span>
                  <a href="<?php echo WEB_ROOT; ?>module/cashier/soa.php?eid=<?php echo (int)$r->ENROLLMENT_ID; ?>"
                     target="_blank" class="card-link-quiet">SOA</a>
                </td>
              </tr>
            <?php } } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ======================= QUICK ACCESS ======================= -->
    <div class="d-flex align-items-center justify-content-between mb-3 mt-4">
      <h3 class="card-title" style="font-size:1.05rem;">Quick Access</h3>
      <span class="text-muted" style="font-size:.8rem;">Jump straight into a module</span>
    </div>

    <div class="quick-grid">

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/student/index.php?view=list">
        <div class="qc-art">
          <i class="fas fa-user-graduate"></i>
          <span class="qc-badge"><?php echo number_format($totalStudent); ?></span>
        </div>
        <div class="qc-title">Student Records</div>
        <div class="qc-sub">Profiles, photos and slot reservation</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/enrollment/index.php">
        <div class="qc-art">
          <i class="fas fa-clipboard-check"></i>
          <span class="qc-badge"><?php echo number_format($activeEnroll); ?> active</span>
        </div>
        <div class="qc-title">Enrolment</div>
        <div class="qc-sub">Sectioning, status and history</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/payments/index.php">
        <div class="qc-art">
          <i class="fas fa-file-invoice-dollar"></i>
          <span class="qc-badge">Cashier</span>
        </div>
        <div class="qc-title">Payments &amp; Fees</div>
        <div class="qc-sub">Collect against the assessment</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/employee/index.php">
        <div class="qc-art">
          <i class="fas fa-user-tie"></i>
          <span class="qc-badge"><?php echo number_format($totalEmployee); ?></span>
        </div>
        <div class="qc-title">Employees</div>
        <div class="qc-sub">Staff records and reporting lines</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblgrades">
        <div class="qc-art">
          <i class="fas fa-graduation-cap"></i>
          <span class="qc-badge"><?php echo number_format($totalGrades); ?></span>
        </div>
        <div class="qc-title">Grades</div>
        <div class="qc-sub">Marks recorded under each enrolment</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/subject">
        <div class="qc-art">
          <i class="fas fa-book-open"></i>
          <span class="qc-badge"><?php echo number_format($totalSubject); ?></span>
        </div>
        <div class="qc-title">Subjects</div>
        <div class="qc-sub">Subject catalogue per course</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblsections">
        <div class="qc-art">
          <i class="fas fa-chalkboard"></i>
          <span class="qc-badge"><?php echo number_format($totalSection); ?></span>
        </div>
        <div class="qc-title">Sections</div>
        <div class="qc-sub">Class groupings per year level</div>
      </a>

      <a class="quick-card" href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=alumni_details">
        <div class="qc-art">
          <i class="fas fa-id-card"></i>
          <span class="qc-badge"><?php echo number_format($totalAlumni); ?></span>
        </div>
        <div class="qc-title">Alumni Details</div>
        <div class="qc-sub">Graduates and their records</div>
      </a>
    </div>

  </div>
</section>