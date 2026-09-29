<?php
/* Employee module - HR records. $mydb is ready (initialize.php via template). */
global $mydb;

/* Managers dropdown = every existing employee. */
$managers = array();
$mydb->setQuery("SELECT EmployeeID, FirstName, LastName FROM `tblemployee` ORDER BY LastName ASC, FirstName ASC");
foreach ($mydb->loadResultList() as $m) { $managers[] = $m; }
?>
<section class="content">
  <div class="container-fluid px-0">
    <?php check_message(); ?>

    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="card-title"><i class="fa fa-user-tie mr-1"></i> Employees</h3>
          <div class="text-muted" style="font-size:.8rem;">Staff records, reporting lines and status</div>
        </div>
        <div class="d-flex align-items-center" style="gap:.5rem;">
          <div class="btn-group-pill" id="statusFilters">
            <button type="button" class="btn active" data-filter="">All</button>
            <button type="button" class="btn" data-filter="1">Active</button>
            <button type="button" class="btn" data-filter="0">Inactive</button>
          </div>
          <button type="button" class="btn btn-primary btn-sm" id="btnAddEmployee">
            <i class="fa fa-plus mr-1"></i> Add Employee
          </button>
          <button type="button" id="printEmployees" class="btn btn-secondary btn-sm">
            <i class="fa fa-print mr-1"></i> Print
          </button>
        </div>
      </div>

      <div class="card-body p-0">
        <table id="tblemployeelist" class="table table-hover" style="width:100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>First Name</th>
              <th>Middle Name</th>
              <th>Last Name</th>
              <th>Suffix</th>
              <th>Email</th>
              <th>Job Title</th>
              <th>Department</th>
              <th>Manager</th>
              <th>Hire Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- ===================== ADD / EDIT EMPLOYEE ===================== -->
<div class="modal fade" id="employeeModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <form action="controller.php?action=save" method="POST" enctype="multipart/form-data" id="employeeForm" novalidate>
      <input type="hidden" name="E_PHOTO_OLD" id="E_PHOTO_OLD" value="">
      <input type="hidden" name="EMP_ID" id="EMP_ID" value="0">

      <div class="modal-content">

        <div class="modal-header">
          <div>
            <h4 class="modal-title" id="employeeModalTitle">Add Employee</h4>
            <div class="text-muted" style="font-size:.8rem;">Fields marked with an asterisk are required</div>
          </div>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body emp-landscape">

          <!-- ---------- LEFT: photo ---------- -->
          <aside class="emp-photo-row">
            <img id="E_PHOTO_PREVIEW" class="emp-photo"
                 src="data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20viewBox%3D%270%200%20100%20100%27%3E%3Crect%20width%3D%27100%27%20height%3D%27100%27%20fill%3D%27%23eceef1%27%2F%3E%3Ccircle%20cx%3D%2750%27%20cy%3D%2738%27%20r%3D%2716%27%20fill%3D%27%23b9bfc7%27%2F%3E%3Cellipse%20cx%3D%2750%27%20cy%3D%2786%27%20rx%3D%2728%27%20ry%3D%2720%27%20fill%3D%27%23b9bfc7%27%2F%3E%3C%2Fsvg%3E"
                 alt="">
            <div class="emp-photo-meta">
              <div class="emp-photo-title">Employee photo</div>
              <div class="emp-photo-hint">JPG, PNG, GIF or WEBP. Up to 2MB. Optional.</div>
              <div class="emp-photo-actions">
                <label class="btn btn-secondary btn-sm mb-0" for="E_PHOTO">
                  <i class="fa fa-upload mr-1"></i> Choose photo
                </label>
                <button type="button" class="btn btn-secondary btn-sm" id="E_PHOTO_CLEAR" style="display:none;">
                  Remove
                </button>
                <span class="emp-photo-name text-muted" id="E_PHOTO_NAME">No file chosen</span>
              </div>
              <input type="file" name="E_PHOTO" id="E_PHOTO" accept="image/jpeg,image/png,image/gif,image/webp" class="emp-photo-input">
            </div>
          </aside>

          <!-- ---------- RIGHT: all the fields ---------- -->
          <div class="emp-fields">

          <!-- ---------- Personal ---------- -->
          <div class="form-section">
            <div class="form-section-title">Personal details</div>
            <div class="form-grid">
              <div class="fg" style="grid-column: span 3;">
                <label for="E_FNAME">First Name <span class="req">*</span></label>
                <input type="text" class="form-control" name="E_FNAME" id="E_FNAME" placeholder="Juan" required>
              </div>
              <div class="fg" style="grid-column: span 3;">
                <label for="E_MNAME">Middle Name</label>
                <input type="text" class="form-control" name="E_MNAME" id="E_MNAME" placeholder="Santos">
              </div>
              <div class="fg" style="grid-column: span 3;">
                <label for="E_LNAME">Last Name <span class="req">*</span></label>
                <input type="text" class="form-control" name="E_LNAME" id="E_LNAME" placeholder="Dela Cruz" required>
              </div>

              <div class="fg" style="grid-column: span 3;">
                <label for="E_SUFFIX">Suffix</label>
                <select class="form-control custom-select" name="E_SUFFIX" id="E_SUFFIX">
                  <option value="">None</option>
                  <option value="Jr.">Jr.</option>
                  <option value="Sr.">Sr.</option>
                  <option value="II">II</option>
                  <option value="III">III</option>
                  <option value="IV">IV</option>
                  <option value="V">V</option>
                </select>
              </div>
              <div class="fg" style="grid-column: span 4;">
                <label for="E_DOB">Date of Birth</label>
                <input type="date" class="form-control" name="E_DOB" id="E_DOB">
              </div>
            </div>
          </div>

          <!-- ---------- Contact ---------- -->
          <div class="form-section">
            <div class="form-section-title">Contact</div>
            <div class="form-grid">
              <div class="fg" style="grid-column: span 7;">
                <label for="E_EMAIL">Email <span class="req">*</span></label>
                <input type="email" class="form-control" name="E_EMAIL" id="E_EMAIL" placeholder="name@example.com" required>
                <div class="fg-help">Must be unique. It identifies the record.</div>
              </div>
              <div class="fg" style="grid-column: span 5;">
                <label for="E_PHONE">Phone</label>
                <input type="text" class="form-control" name="E_PHONE" id="E_PHONE" placeholder="09XX XXX XXXX">
              </div>
            </div>
          </div>

          <!-- ---------- Employment ---------- -->
          <div class="form-section">
            <div class="form-section-title">Employment</div>
            <div class="form-grid">
              <div class="fg" style="grid-column: span 4;">
                <label for="E_JOBTITLE">Job Title</label>
                <input type="text" class="form-control" name="E_JOBTITLE" id="E_JOBTITLE" placeholder="Instructor">
              </div>
              <div class="fg" style="grid-column: span 4;">
                <label for="E_DEPARTMENT">Department</label>
                <input type="text" class="form-control" name="E_DEPARTMENT" id="E_DEPARTMENT" placeholder="College of Computer Studies">
              </div>

              <div class="fg" style="grid-column: span 4;">
                <label for="E_HIRE">Hire Date <span class="req">*</span></label>
                <input type="date" class="form-control" name="E_HIRE" id="E_HIRE" required>
              </div>
              <div class="fg" style="grid-column: span 4;">
                <label for="E_SALARY">Salary</label>
                <div class="input-with-prefix">
                  <span class="ip">P</span>
                  <input type="number" step="0.01" min="0" class="form-control" name="E_SALARY" id="E_SALARY" placeholder="0.00">
                </div>
              </div>
              <div class="fg" style="grid-column: span 4;">
                <label for="E_ISACTIVE">Status</label>
                <select class="form-control custom-select" name="E_ISACTIVE" id="E_ISACTIVE">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>

              <div class="fg" style="grid-column: span 4;">
                <label for="E_MANAGER">Reports to</label>
                <select class="form-control custom-select" name="E_MANAGER" id="E_MANAGER">
                  <option value="">None</option>
                  <?php foreach ($managers as $m) { ?>
                  <option value="<?php echo (int)$m->EmployeeID; ?>"><?php echo htmlspecialchars($m->LastName.', '.$m->FirstName); ?></option>
                  <?php } ?>
                </select>
                <div class="fg-help">An employee cannot be set as their own manager.</div>
              </div>
            </div>
          </div>

          </div><!-- /.emp-fields -->
        </div>

        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btnSaveEmployee">
            <i class="fa fa-save mr-1"></i> Save Employee
          </button>
        </div>

      </div>
    </form>
  </div>
</div>

<style>
/* ---------------------------------------------------------------
   Form styling scoped to this modal. The old markup put Suffix in a
   Bootstrap col-sm-1, which squeezed the select so narrow that only
   one character of the selected option was visible. The layout is a
   12 column CSS grid now, so every control gets a sensible width.
   --------------------------------------------------------------- */
#employeeModal .modal-body { padding: 1.25rem 1.35rem 1.5rem; }

/* Landscape: a wide dialog, photo pinned left, fields filling the right.
   This keeps the form short and horizontal instead of one tall column. */
#employeeModal .modal-dialog { max-width: 1120px; }
#employeeModal .modal-body.emp-landscape {
  display: grid;
  grid-template-columns: 236px minmax(0, 1fr);
  gap: 1.5rem;
  align-items: start;
}
#employeeModal .emp-fields { min-width: 0; }

/* Photo panel (left column, stacked) */
#employeeModal .emp-photo-row {
  display: flex; flex-direction: column; align-items: center;
  text-align: center; gap: .75rem;
  padding: 1.35rem 1rem;
  border: 1px solid var(--line);
  border-radius: var(--radius);
  background: var(--surface-2);
  position: sticky; top: 0;
}
#employeeModal .emp-photo {
  width: 108px; height: 108px; flex: none;
  object-fit: cover; border-radius: 50%;
  border: 3px solid var(--surface);
  box-shadow: 0 0 0 1px var(--line);
  background: var(--surface-sunk);
}
#employeeModal .emp-photo-meta { min-width: 0; width: 100%; }
#employeeModal .emp-photo-title { font-weight: 640; font-size: .9rem; }
#employeeModal .emp-photo-hint { font-size: .75rem; color: var(--muted); margin-top: .2rem; line-height: 1.45; }
#employeeModal .emp-photo-actions {
  display: flex; flex-direction: column; align-items: stretch; gap: .4rem;
  margin-top: .85rem;
}
#employeeModal .emp-photo-actions .btn { cursor: pointer; }
#employeeModal .emp-photo-name {
  font-size: .75rem; display: block; width: 100%;
  overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  margin-top: .15rem;
}
/* The real file input is hidden; the label above triggers it. */
#employeeModal .emp-photo-input {
  position: absolute; width: 1px; height: 1px;
  opacity: 0; overflow: hidden; clip: rect(0 0 0 0);
}

/* Sections */
#employeeModal .form-section + .form-section { margin-top: 1.15rem; }
#employeeModal .form-section-title {
  font-size: .7rem; font-weight: 660;
  letter-spacing: .09em; text-transform: uppercase;
  color: var(--faint);
  padding-bottom: .5rem; margin-bottom: .9rem;
  border-bottom: 1px solid var(--line-soft);
}

/* 12 column grid */
#employeeModal .form-grid {
  display: grid; grid-template-columns: repeat(12, minmax(0, 1fr));
  gap: .9rem 1rem;
}
#employeeModal .fg { min-width: 0; }
#employeeModal .fg label {
  display: block; font-size: .8rem; font-weight: 580;
  color: var(--ink-2); margin-bottom: .35rem;
}
#employeeModal .fg .req { color: var(--neg); font-weight: 700; }
#employeeModal .fg-help { font-size: .74rem; color: var(--faint); margin-top: .3rem; }
#employeeModal .form-control, #employeeModal .custom-select { width: 100%; }

/* Peso prefix on the salary field */
#employeeModal .input-with-prefix { position: relative; }
#employeeModal .input-with-prefix .ip {
  position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
  color: var(--muted); font-size: .85rem; font-weight: 560; pointer-events: none;
}
#employeeModal .input-with-prefix .form-control { padding-left: 1.85rem; }

/* Inline validation */
#employeeModal .form-control.is-invalid, #employeeModal .custom-select.is-invalid {
  border-color: var(--neg);
  box-shadow: 0 0 0 3px rgba(220,38,38,.10);
}
#employeeModal .fg-error {
  display: none; font-size: .74rem; color: var(--neg); margin-top: .3rem;
}
#employeeModal .fg.has-error .fg-error { display: block; }
#employeeModal .fg.has-error .fg-help { display: none; }

/* Below the landscape breakpoint the photo panel goes back on top and
   turns horizontal again, so the modal still works on a laptop or phone. */
@media (max-width: 991px) {
  #employeeModal .modal-body.emp-landscape { grid-template-columns: 1fr; gap: 1.15rem; }
  #employeeModal .emp-photo-row {
    flex-direction: row; text-align: left; position: static;
    padding: 1rem 1.1rem; gap: 1.1rem;
  }
  #employeeModal .emp-photo { width: 74px; height: 74px; border-width: 2px; }
  #employeeModal .emp-photo-actions { flex-direction: row; align-items: center; flex-wrap: wrap; }
  #employeeModal .emp-photo-name { width: auto; max-width: 220px; }
}
@media (max-width: 720px) {
  #employeeModal .form-grid > .fg { grid-column: span 12 !important; }
  #employeeModal .emp-photo-row { flex-direction: column; text-align: center; }
  #employeeModal .emp-photo-actions { justify-content: center; }
}

/* ---------------------------------------------------------------
   List table: keep the View / Edit / Delete buttons on one line.
   The Action column is narrow, so without this they wrap and each
   row grows to three button heights.
   --------------------------------------------------------------- */
#tblemployeelist td:last-child,
#tblemployeelist th:last-child { white-space: nowrap; text-align: right; }
#tblemployeelist td:last-child a { display: inline-block; }
#tblemployeelist td:last-child .btn { margin-left: 3px; }
</style>

<script>
/* This file is included in the MIDDLE of theme/template.php, while jQuery
   and SweetAlert2 are loaded near the bottom of it. Running $(...) here
   directly threw "$ is not defined" and left the whole form inert, so the
   setup is deferred until those libraries exist. */
(function bootEmployeeForm() {
  if (typeof window.jQuery === 'undefined'
   || typeof jQuery.fn.DataTable === 'undefined') {
    return window.setTimeout(bootEmployeeForm, 40);
  }
  jQuery(function ($) {

  var WEB_ROOT = '<?php echo WEB_ROOT; ?>';

  /* ---------------------------------------------------------------
     The list table is built HERE, in the same file as the markup.

     It used to be initialised from module/employee/index.php, which
     runs after this file is included. If that file was out of step
     the table rendered as bare headers with no rows, no search box
     and no pager, because .DataTable() never ran at all. Keeping the
     table and its setup together removes that failure mode.
     --------------------------------------------------------------- */
  var currentStatusFilter = '';
  var employeeTable = null;

  if ($('#tblemployeelist').length && !$.fn.DataTable.isDataTable('#tblemployeelist')) {
    employeeTable = $('#tblemployeelist').DataTable({
      processing: true,
      serverSide: true,
      scrollX: true,
      order: [],
      ajax: {
        url: WEB_ROOT + 'module/employee/ajax.php',
        type: 'POST',
        data: function (d) { d.status_filter = currentStatusFilter; },
        error: function (xhr) {
          $('#tblemployeelist tbody').html(
            '<tr><td colspan="12" class="text-center text-muted py-5">' +
            'Could not load employees (server said ' + xhr.status + ').<br>' +
            '<small>Check that module/employee/ajax.php is in place.</small>' +
            '</td></tr>'
          );
        }
      },
      columnDefs: [{ orderable: false, targets: [11] }]
    });
    window.employeeTable = employeeTable;
  }

  /* Status filter chips */
  $('#statusFilters').on('click', 'button', function () {
    $('#statusFilters button').removeClass('active');
    $(this).addClass('active');
    currentStatusFilter = $(this).data('filter');
    if (employeeTable) { employeeTable.ajax.reload(); }
  });

  /* Print, carrying the active filter */
  $('#printEmployees').on('click', function () {
    var url = WEB_ROOT + 'module/employee/print.php';
    if (currentStatusFilter === '1' || currentStatusFilter === '0') {
      url += '?status=' + currentStatusFilter;
    }
    window.open(url, '_blank');
  });

  /* ---------- Photo picker ---------- */
  var DEFAULT_PHOTO = $('#E_PHOTO_PREVIEW').attr('src');

  $('#E_PHOTO').on('change', function () {
    var file = this.files && this.files[0];
    if (!file) { resetPhoto(); return; }

    if (file.size > 2 * 1024 * 1024) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon:'warning', title:'Photo too large',
                    text:'Please choose an image smaller than 2MB.',
                    confirmButtonColor:'#101418' });
      } else {
        alert('Photo too large. Please choose an image smaller than 2MB.');
      }
      this.value = '';
      resetPhoto();
      return;
    }

    $('#E_PHOTO_NAME').text(file.name);
    $('#E_PHOTO_CLEAR').show();

    var reader = new FileReader();
    reader.onload = function (e) { $('#E_PHOTO_PREVIEW').attr('src', e.target.result); };
    reader.readAsDataURL(file);
  });

  $('#E_PHOTO_CLEAR').on('click', function () {
    $('#E_PHOTO').val('');
    resetPhoto();
  });

  function resetPhoto(src) {
    $('#E_PHOTO_PREVIEW').attr('src', src || DEFAULT_PHOTO);
    $('#E_PHOTO_NAME').text('No file chosen');
    $('#E_PHOTO_CLEAR').hide();
  }

  /* ---------- Validation ---------- */
  function fieldGroup(el) { return $(el).closest('.fg'); }

  function setError(el, msg) {
    var g = fieldGroup(el);
    $(el).addClass('is-invalid');
    g.addClass('has-error');
    if (g.find('.fg-error').length === 0) { g.append('<div class="fg-error"></div>'); }
    g.find('.fg-error').text(msg);
  }

  function clearError(el) {
    $(el).removeClass('is-invalid');
    fieldGroup(el).removeClass('has-error');
  }

  $('#employeeForm').on('input change', '.form-control, .custom-select', function () {
    if ($(this).hasClass('is-invalid')) { clearError(this); }
  });

  $('#employeeForm').on('submit', function (e) {
    var ok = true, firstBad = null;

    function require(sel, msg) {
      var el = $(sel);
      if ($.trim(el.val()) === '') {
        setError(el, msg); ok = false;
        if (!firstBad) { firstBad = el; }
      } else { clearError(el); }
    }

    require('#E_FNAME', 'First name is required.');
    require('#E_LNAME', 'Last name is required.');
    require('#E_HIRE',  'Hire date is required.');

    var email = $('#E_EMAIL');
    var ev = $.trim(email.val());
    if (ev === '') {
      setError(email, 'Email is required.'); ok = false;
      if (!firstBad) { firstBad = email; }
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ev)) {
      setError(email, 'Enter a valid email address.'); ok = false;
      if (!firstBad) { firstBad = email; }
    } else { clearError(email); }

    /* An employee cannot manage themselves. */
    var id = parseInt($('#EMP_ID').val(), 10) || 0;
    var mgr = parseInt($('#E_MANAGER').val(), 10) || 0;
    if (id > 0 && mgr === id) {
      setError($('#E_MANAGER'), 'An employee cannot report to themselves.');
      ok = false;
      if (!firstBad) { firstBad = $('#E_MANAGER'); }
    }

    if (!ok) {
      e.preventDefault();
      if (firstBad) { firstBad.focus(); }
      return false;
    }

    $('#btnSaveEmployee')
      .prop('disabled', true)
      .html('<i class="fa fa-circle-notch fa-spin mr-1"></i> Saving...');
  });

  /* ---------- Open for ADD ---------- */
  $('#btnAddEmployee').on('click', function () {
    $('#employeeForm')[0].reset();
    $('#EMP_ID').val(0);
    $('#E_PHOTO_OLD').val('');
    $('#employeeModalTitle').text('Add Employee');
    $('#employeeForm .form-control, #employeeForm .custom-select').each(function () { clearError(this); });
    resetPhoto();
    $('#employeeModal').modal('show');
  });

  /* ---------- Open for EDIT ---------- */
  $(document).on('click', '.editEmployee', function () {
    var id = $(this).attr('EID');

    $.ajax({
      url: WEB_ROOT + 'module/employee/ajax.php',
      method: 'POST',
      data: { act: 'row', EmployeeID: id },
      dataType: 'json',
      success: function (d) {
        if (!d || !d.EmployeeID) {
          alert('Could not load that employee record.');
          return;
        }

        $('#employeeModalTitle').text('Edit Employee');
        $('#EMP_ID').val(d.EmployeeID);
        $('#E_FNAME').val(d.FirstName || '');
        $('#E_MNAME').val(d.MiddleName || '');
        $('#E_LNAME').val(d.LastName || '');
        $('#E_SUFFIX').val(d.Suffix || '');
        $('#E_EMAIL').val(d.Email || '');
        $('#E_PHONE').val(d.Phone || '');
        $('#E_DOB').val(d.DateOfBirth || '');
        $('#E_HIRE').val(d.HireDate || '');
        $('#E_JOBTITLE').val(d.JobTitle || '');
        $('#E_DEPARTMENT').val(d.Department || '');
        $('#E_SALARY').val(d.Salary || '');
        $('#E_ISACTIVE').val(String(d.IsActive));

        /* Nobody may be offered as their own manager. */
        $('#E_MANAGER option').prop('disabled', false);
        $('#E_MANAGER option[value="' + d.EmployeeID + '"]').prop('disabled', true);
        $('#E_MANAGER').val(d.ManagerID || '');

        /* Carry the current photo through the edit. Without this the form
           posts an empty E_PHOTO_OLD and saving would clear PhotoPath. */
        $('#E_PHOTO').val('');
        $('#E_PHOTO_OLD').val(d.PhotoPath || '');
        resetPhoto(d.PhotoURL || null);
        if (d.PhotoURL) { $('#E_PHOTO_NAME').text('Current photo'); }

        /* Clear validation state left over from a previous open. */
        $('#employeeForm .form-control, #employeeForm .custom-select').each(function () { clearError(this); });

        $('#employeeModal').modal('show');
      },
      error: function () { alert('Could not load that employee record.'); }
    });
  });

  /* Re-enable the button if the modal is closed mid-save. */
  $('#employeeModal').on('hidden.bs.modal', function () {
    $('#btnSaveEmployee').prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Save Employee');
  });

  /* Kept for compatibility with any older copy of index.php that still
     has its own edit handler and calls this. */
  window.employeeResetPhoto = resetPhoto;

  });
})();
</script>