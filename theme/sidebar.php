<?php
/* ABADIES SOLUTIONS - sidebar

   Grouped into labelled sections so the menu reads as a product rather
   than one long list. Counts next to a few items come from the database
   so the sidebar reflects the real system, and are wrapped in
   tableExists() so a missing table can never take the menu down. */

global $mydb;

if (!function_exists('sidebar_count')) {
	function sidebar_count($table) {
		global $mydb;
		if (!isset($mydb) || !$mydb->tableExists($table)) { return null; }
		$mydb->setQuery("SELECT 1 FROM `".$table."`");
		return $mydb->num_rows();
	}
	/* Renders a count pill, or nothing when the table is missing/empty. */
	function sidebar_badge($n, $soft = false) {
		if ($n === null || $n <= 0) { return ''; }
		$label = ($n > 99) ? '99+' : $n;
		return '<span class="nav-badge'.($soft ? ' is-soft' : '').'">'.$label.'</span>';
	}
}

$cStudent    = sidebar_count('tblstudent');
$cEmployee   = sidebar_count('tblemployee');
$cEnrollment = sidebar_count('tblenrollment');
$cAlumni     = sidebar_count('alumni_details');
$cCourse     = sidebar_count('tblcourses');
$cSubject    = sidebar_count('tblsubjects');

$isCashier   = ($title=='Cashier' || $title=='Payments' || $title=='Fee Setup');
$isAttend    = ($title=='Attendance');
$isAccounts  = ($title=='User Module' || $title=='User Type');
?>
<nav class="mt-2">
	<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

		<!-- ============================ OVERVIEW ============================ -->
		<li class="nav-item"><div class="nav-section">Overview</div></li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>" class="nav-link <?php echo ($title=='Home') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-th-large"></i>
				<p>Dashboard</p>
			</a>
		</li>

		<!-- ============================= RECORDS ============================ -->
		<li class="nav-item"><div class="nav-section">Records</div></li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/student" class="nav-link <?php echo ($title=='Student Module') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-user-graduate"></i>
				<p>Student</p>
				<?php echo sidebar_badge($cStudent); ?>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/employee/index.php" class="nav-link <?php echo ($title=='Employee') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-user-tie"></i>
				<p>Employee</p>
				<?php echo sidebar_badge($cEmployee); ?>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=alumni_details" class="nav-link <?php echo ($title=='Alumni Details') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-id-card"></i>
				<p>Alumni Details</p>
				<?php echo sidebar_badge($cAlumni, true); ?>
			</a>
		</li>

		<!-- ============================ ACADEMICS =========================== -->
		<li class="nav-item"><div class="nav-section">Academics</div></li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/enrollment/index.php" class="nav-link <?php echo ($title=='Enrollment') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-clipboard-list"></i>
				<p>Enrollment</p>
				<?php echo sidebar_badge($cEnrollment); ?>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/course" class="nav-link <?php echo ($title=='Course Module') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-book"></i>
				<p>Course</p>
				<?php echo sidebar_badge($cCourse, true); ?>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/subject" class="nav-link <?php echo ($title=='Subject Module') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-book-open"></i>
				<p>Subject</p>
				<?php echo sidebar_badge($cSubject, true); ?>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblschoolyear" class="nav-link <?php echo ($title=='School Year') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-calendar-alt"></i>
				<p>School Year</p>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblsections" class="nav-link <?php echo ($title=='Sections') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-chalkboard"></i>
				<p>Sections</p>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblenrollment_details" class="nav-link <?php echo ($title=='Enrollment Details') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-list-alt"></i>
				<p>Enrollment Details</p>
			</a>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblgrades" class="nav-link <?php echo ($title=='Grades') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-graduation-cap"></i>
				<p>Grades</p>
			</a>
		</li>

		<!-- ============================ OPERATIONS ========================== -->
		<li class="nav-item"><div class="nav-section">Operations</div></li>

		<li class="nav-item has-treeview <?php echo $isCashier ? 'menu-open' : ''; ?>">
			<a href="#" class="nav-link <?php echo $isCashier ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-cash-register"></i>
				<p>Cashier <i class="fas fa-angle-left right"></i></p>
			</a>
			<ul class="nav nav-treeview">
				<li class="nav-item">
					<a href="<?php echo WEB_ROOT; ?>module/cashier/index.php" class="nav-link <?php echo ($title=='Cashier') ? 'active' : ''; ?>">
						<i class="nav-icon fas fa-file-invoice-dollar"></i>
						<p>Statement of Account</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo WEB_ROOT; ?>module/payments/index.php" class="nav-link <?php echo ($title=='Payments') ? 'active' : ''; ?>">
						<i class="nav-icon fas fa-money-bill-wave"></i>
						<p>Payments &amp; Fee Setup</p>
					</a>
				</li>
			</ul>
		</li>

		<li class="nav-item has-treeview <?php echo $isAttend ? 'menu-open' : ''; ?>">
			<a href="#" class="nav-link <?php echo $isAttend ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-fingerprint"></i>
				<p>Attendance <i class="fas fa-angle-left right"></i></p>
			</a>
			<ul class="nav nav-treeview">
				<li class="nav-item">
					<a href="<?php echo WEB_ROOT; ?>module/attendance/index.php?view=scan" class="nav-link">
						<i class="nav-icon fas fa-address-card"></i>
						<p>Scan Kiosk</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo WEB_ROOT; ?>module/attendance/index.php" class="nav-link">
						<i class="nav-icon fas fa-list-alt"></i>
						<p>Attendance Report</p>
					</a>
				</li>
			</ul>
		</li>

		<!-- ============================== SYSTEM ============================ -->
		<li class="nav-item"><div class="nav-section">System</div></li>

		<li class="nav-item has-treeview <?php echo $isAccounts ? 'menu-open' : ''; ?>">
			<a href="#" class="nav-link <?php echo $isAccounts ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-cog"></i>
				<p>Account Settings <i class="fas fa-angle-left right"></i></p>
			</a>
			<ul class="nav nav-treeview">
				<li class="nav-item">
					<a href="<?php echo WEB_ROOT; ?>module/user/" class="nav-link <?php echo ($title=='User Module') ? 'active' : ''; ?>">
						<i class="nav-icon fas fa-users"></i>
						<p>Manage User Accounts</p>
					</a>
				</li>
				<li class="nav-item">
					<a href="<?php echo WEB_ROOT; ?>module/usertype/" class="nav-link <?php echo ($title=='User Type') ? 'active' : ''; ?>">
						<i class="nav-icon fas fa-key"></i>
						<p>Manage User Type</p>
					</a>
				</li>
			</ul>
		</li>

		<li class="nav-item">
			<a href="<?php echo WEB_ROOT; ?>module/about/index.php" class="nav-link <?php echo ($title=='About') ? 'active' : ''; ?>">
				<i class="nav-icon fas fa-info-circle"></i>
				<p>About</p>
			</a>
		</li>

		<li class="nav-item">
			<a href="#" class="nav-link" id="sidebarThemeToggle">
				<i class="nav-icon fas fa-adjust" id="sidebarThemeIcon"></i>
				<p>Appearance</p>
				<span class="nav-flame" id="sidebarThemeName">Light</span>
			</a>
		</li>
	</ul>

	<!-- Storage note, styled like the reference's usage card -->
	<div class="sidebar-footer">
		<div class="sidebar-quota">
			<div class="quota-top">
				<i class="fas fa-database"></i> Records stored
			</div>
			<div class="quota-sub">
				<?php
					$stored = (int)$cStudent + (int)$cEmployee + (int)$cEnrollment + (int)$cAlumni;
					echo number_format($stored).' across '.number_format(4).' modules';
				?>
			</div>
			<div class="quota-bar">
				<span style="width: <?php echo max(6, min(100, $stored)); ?>%;"></span>
			</div>
		</div>
	</div>
</nav>

<script>
/* Appearance row mirrors the header toggle, so the theme can be switched
   from either place and both labels stay in step. */
document.addEventListener('DOMContentLoaded', function () {
	var row  = document.getElementById('sidebarThemeToggle');
	var icon = document.getElementById('sidebarThemeIcon');
	var name = document.getElementById('sidebarThemeName');
	if (!row) { return; }

	function paint() {
		var dark = document.documentElement.classList.contains('dark-mode-custom');
		if (name) { name.textContent = dark ? 'Dark' : 'Light'; }
		if (icon) {
			icon.classList.toggle('fa-moon', dark);
			icon.classList.toggle('fa-adjust', !dark);
		}
	}

	row.addEventListener('click', function (e) {
		e.preventDefault();
		var dark = document.documentElement.classList.toggle('dark-mode-custom');
		localStorage.setItem('abadiesTheme', dark ? 'dark' : 'light');
		paint();
		document.dispatchEvent(new CustomEvent('abadies:theme', { detail: { dark: dark } }));
	});

	document.addEventListener('abadies:theme', paint);
	paint();
});
</script>
