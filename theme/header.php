<?php
/* ABADIES SOLUTIONS - top bar

   Page title on the left, global search in the middle, then theme
   toggle, notifications and the user chip on the right. */

$hdrName = isset($_SESSION['DISPLAYNAME']) && $_SESSION['DISPLAYNAME'] !== '' ? $_SESSION['DISPLAYNAME'] : 'User';
$hdrType = isset($_SESSION['TYPE']) && $_SESSION['TYPE'] !== '' ? $_SESSION['TYPE'] : 'Guest';
$hdrInit = strtoupper(substr(trim($hdrName), 0, 1));
if ($hdrInit === '') { $hdrInit = 'U'; }

/* index.php sets $title to "Home", but the sidebar and breadcrumbs both
   call that page the Dashboard, so show one name for it everywhere. */
$hdrTitle = isset($title) ? $title : 'Dashboard';
if ($hdrTitle === 'Home') { $hdrTitle = 'Dashboard'; }
?>
<nav class="main-header navbar navbar-expand">

	<!-- Left: collapse + page title -->
	<ul class="navbar-nav align-items-center">
		<li class="nav-item">
			<a class="nav-link icon-btn" data-widget="pushmenu" data-controlsidebar-side="false" href="#" role="button" title="Toggle menu">
				<i class="fas fa-bars"></i>
			</a>
		</li>
		<li class="nav-item d-none d-md-inline-block ml-1">
			<span class="topbar-title">
				<i class="fas fa-border-all"></i>
				<?php echo htmlspecialchars($hdrTitle); ?>
			</span>
		</li>
	</ul>

	<!-- Middle: search -->
	<div class="mx-auto d-none d-lg-block">
		<div class="topbar-search">
			<i class="fas fa-search"></i>
			<input type="text" id="globalSearch" placeholder="Search for something..." autocomplete="off">
		</div>
	</div>

	<!-- Right -->
	<ul class="navbar-nav ml-auto align-items-center">

		<li class="nav-item">
			<a class="nav-link icon-btn" href="#" id="darkModeToggle" title="Toggle dark mode">
				<i class="fas fa-moon" id="darkModeIcon"></i>
			</a>
		</li>

		<li class="nav-item dropdown">
			<a class="nav-link icon-btn" data-toggle="dropdown" href="#" id="notifBellLink" title="Notifications">
				<i class="far fa-bell"></i>
				<span class="badge badge-danger navbar-badge" id="notifBadge" style="display:none;">0</span>
			</a>
			<div class="dropdown-menu dropdown-menu-right p-0" style="width:340px;">
				<div class="d-flex justify-content-between align-items-center px-3 py-2" style="border-bottom:1px solid var(--line-soft);">
					<span style="font-weight:640; font-size:.88rem;">Notifications</span>
					<a href="#" id="notifMarkAll" style="font-size:.76rem; font-weight:560;">Mark all as read</a>
				</div>
				<div id="notifList" style="max-height:340px; overflow-y:auto; padding:.35rem;">
					<div class="text-center text-muted py-4" id="notifEmpty" style="font-size:.84rem;">No notifications.</div>
				</div>
			</div>
		</li>

		<li class="nav-item dropdown ml-1">
			<a class="nav-link p-0" data-toggle="dropdown" href="#">
				<span class="user-chip">
					<span class="avatar"><?php echo htmlspecialchars($hdrInit); ?></span>
					<span class="d-none d-sm-inline"><?php echo htmlspecialchars($hdrName); ?></span>
					<i class="fas fa-angle-down" style="font-size:.7rem; color:var(--faint);"></i>
				</span>
			</a>
			<div class="dropdown-menu dropdown-menu-right" style="width:250px;">
				<div class="px-3 py-2" style="border-bottom:1px solid var(--line-soft); margin-bottom:.3rem;">
					<div style="font-weight:640; font-size:.9rem; color:var(--ink);"><?php echo htmlspecialchars($hdrName); ?></div>
					<div style="font-size:.78rem; color:var(--muted);"><?php echo htmlspecialchars($hdrType); ?></div>
				</div>
				<a href="<?php echo WEB_ROOT; ?>module/user/" class="dropdown-item">
					<i class="fas fa-user-cog mr-2" style="color:var(--faint); width:16px;"></i> Account Settings
				</a>
				<a href="<?php echo WEB_ROOT; ?>module/about/index.php" class="dropdown-item">
					<i class="fas fa-info-circle mr-2" style="color:var(--faint); width:16px;"></i> About
				</a>
				<div class="dropdown-divider"></div>
				<a href="<?php echo WEB_ROOT; ?>logout.php" id="logoutBtn" class="dropdown-item" style="color:var(--neg);">
					<i class="fas fa-sign-out-alt mr-2" style="width:16px;"></i> Log out
				</a>
			</div>
		</li>
	</ul>
</nav>

<script>
/* Plain JS on purpose: jQuery is loaded near the bottom of template.php,
   so it is not available while this element is being parsed. Anything
   that needs jQuery is inside the DOMContentLoaded handler below, which
   runs after those scripts have executed. */
document.addEventListener('DOMContentLoaded', function () {

	/* ---------------- Theme toggle ---------------- */
	var htmlEl = document.documentElement;
	var toggle = document.getElementById('darkModeToggle');
	var icon   = document.getElementById('darkModeIcon');

	function setIcon(dark) {
		if (!icon) { return; }
		icon.classList.toggle('fa-moon', !dark);
		icon.classList.toggle('fa-sun', dark);
	}

	var isDark = (localStorage.getItem('abadiesTheme') === 'dark');
	htmlEl.classList.toggle('dark-mode-custom', isDark);
	setIcon(isDark);

	if (toggle) {
		toggle.addEventListener('click', function (e) {
			e.preventDefault();
			var nowDark = htmlEl.classList.toggle('dark-mode-custom');
			localStorage.setItem('abadiesTheme', nowDark ? 'dark' : 'light');
			setIcon(nowDark);
			document.dispatchEvent(new CustomEvent('abadies:theme', { detail: { dark: nowDark } }));
		});
	}

	/* Keep this icon in step when the sidebar Appearance row is used. */
	document.addEventListener('abadies:theme', function (ev) {
		setIcon(ev.detail && ev.detail.dark);
	});

	/* ---------------- Global search ----------------
	   Filters whatever DataTable is on the page; on pages without one it
	   jumps to the Student list and carries the term along. */
	var gs = document.getElementById('globalSearch');
	if (gs) {
		gs.addEventListener('keyup', function (e) {
			var term = gs.value;
			if (typeof jQuery !== 'undefined' && jQuery.fn.dataTable) {
				var tables = jQuery.fn.dataTable.tables({ visible: true, api: true });
				if (tables.count() > 0) { tables.search(term).draw(); return; }
			}
			if (e.key === 'Enter' && term !== '') {
				window.location = '<?php echo WEB_ROOT; ?>module/student/index.php?view=list&q=' + encodeURIComponent(term);
			}
		});
	}

	/* ---------------- Logout confirmation ---------------- */
	var logoutBtn = document.getElementById('logoutBtn');
	if (logoutBtn) {
		logoutBtn.addEventListener('click', function (e) {
			e.preventDefault();
			var url = logoutBtn.getAttribute('href');
			if (typeof Swal !== 'undefined') {
				Swal.fire({
					title: 'Log out?',
					text: 'You are about to end your session.',
					icon: 'question',
					showCancelButton: true,
					confirmButtonText: 'Yes, log out',
					cancelButtonText: 'Stay signed in',
					confirmButtonColor: '#101418',
					cancelButtonColor: '#6B7280',
					reverseButtons: true,
					focusCancel: true
				}).then(function (result) {
					if (result.value) {
						Swal.fire({
							title: 'Signing out...',
							allowOutsideClick: false,
							didOpen: function () { Swal.showLoading(); }
						});
						window.location.href = url;
					}
				});
			} else if (confirm('Log out of your session?')) {
				window.location.href = url;
			}
		});
	}

	/* ---------------- Notifications bell ---------------- */
	var notifBadge   = document.getElementById('notifBadge');
	var notifList    = document.getElementById('notifList');
	var notifMarkAll = document.getElementById('notifMarkAll');

	function esc(s) {
		return String(s == null ? '' : s)
			.replace(/&/g, '&amp;').replace(/</g, '&lt;')
			.replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}

	function timeAgo(dateStr) {
		var then = new Date(String(dateStr).replace(' ', 'T'));
		var diffSec = Math.floor((new Date() - then) / 1000);
		if (isNaN(diffSec)) { return ''; }
		if (diffSec < 60) return 'just now';
		if (diffSec < 3600) return Math.floor(diffSec / 60) + 'm ago';
		if (diffSec < 86400) return Math.floor(diffSec / 3600) + 'h ago';
		return Math.floor(diffSec / 86400) + 'd ago';
	}

	function loadNotifications() {
		if (!notifBadge || typeof jQuery === 'undefined') { return; }
		jQuery.getJSON('<?php echo WEB_ROOT; ?>theme/notifications_ajax.php', function (data) {

			if (data.unreadCount > 0) {
				notifBadge.textContent = data.unreadCount;
				notifBadge.style.display = 'inline-block';
			} else {
				notifBadge.style.display = 'none';
			}

			if (!notifList) { return; }
			notifList.innerHTML = '';

			if (!data.notifications || data.notifications.length === 0) {
				notifList.innerHTML = '<div class="text-center text-muted py-4" style="font-size:.84rem;">No notifications.</div>';
				return;
			}

			data.notifications.forEach(function (n) {
				var item = document.createElement('a');
				item.href = n.link ? n.link : '#';
				item.className = 'dropdown-item notif-item';
				item.setAttribute('data-id', n.id);
				item.style.whiteSpace = 'normal';
				if (n.isRead == 0) { item.style.background = 'var(--brand-soft)'; }
				item.innerHTML =
					'<div style="font-weight:620; font-size:.84rem;">' + esc(n.title) + '</div>' +
					'<div class="text-muted" style="font-size:.78rem;">' + esc(n.message) + '</div>' +
					'<div class="text-muted" style="font-size:.7rem; margin-top:.15rem;">' + esc(timeAgo(n.created)) + '</div>';
				notifList.appendChild(item);
			});
		});
	}

	if (notifList) {
		notifList.addEventListener('click', function (e) {
			var item = e.target.closest ? e.target.closest('.notif-item') : null;
			if (!item) { return; }
			if (typeof jQuery !== 'undefined') {
				jQuery.post('<?php echo WEB_ROOT; ?>theme/notifications_ajax.php', { act: 'mark_read', id: item.getAttribute('data-id') });
			}
		});
	}

	if (notifMarkAll) {
		notifMarkAll.addEventListener('click', function (e) {
			e.preventDefault();
			if (typeof jQuery !== 'undefined') {
				jQuery.post('<?php echo WEB_ROOT; ?>theme/notifications_ajax.php', { act: 'mark_all_read' }, loadNotifications);
			}
		});
	}

	if (notifBadge) {
		loadNotifications();
		setInterval(loadNotifications, 30000);
	}
});
</script>
