<?php
/* =====================================================================
   ABADIES SOLUTIONS - Employee Report (printable)

   Standalone document in the same format as the Statement of Account
   (module/cashier/soa.php): same masthead, typography, bordered table,
   summary, signatures and print rules. Lists employees with the details
   relevant to an HR directory.

   Prints all employees, or only Active / Inactive when ?status=1 / 0.
   ===================================================================== */
require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID'])) { redirect(WEB_ROOT."login.php"); exit; }

$status = isset($_GET['status']) && ($_GET['status'] === '1' || $_GET['status'] === '0') ? $_GET['status'] : '';

$rows       = array();
$activeCnt  = 0;
$inactiveCnt = 0;

if ($mydb->tableExists('tblemployee')) {
	$where = ($status !== '') ? " WHERE e.IsActive = '".intval($status)."' " : "";
	$mydb->setQuery("SELECT e.EmployeeID, e.FirstName, e.LastName, e.Email, e.Phone,
			e.JobTitle, e.Department, e.HireDate, e.IsActive,
			m.FirstName AS MgrFirst, m.LastName AS MgrLast
		FROM `tblemployee` e
		LEFT JOIN `tblemployee` m ON m.EmployeeID = e.ManagerID
		".$where."
		ORDER BY e.LastName ASC, e.FirstName ASC");
	foreach ($mydb->loadResultList() as $r) {
		$rows[] = $r;
		if ($r->IsActive == 1) { $activeCnt++; } else { $inactiveCnt++; }
	}
}

function dash($v) { return ($v === null || $v === '') ? '<span style="color:#999;">-</span>' : htmlspecialchars($v); }

$preparedBy = isset($_SESSION['DISPLAYNAME']) ? $_SESSION['DISPLAYNAME'] : 'HR';
$statusLabel = ($status === '1') ? 'Active employees' : (($status === '0') ? 'Inactive employees' : 'All employees');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Employee Report<?php echo $status !== '' ? ' - '.($status==='1'?'Active':'Inactive') : ''; ?></title>
  <style>
    :root { --ink:#1a1a1a; --line:#333; --muted:#666; --accent:#c31f5e; --brand:#1c2f9e; }
    * { box-sizing: border-box; }
    body {
      font-family: "Segoe UI", Arial, Helvetica, sans-serif;
      color: var(--ink); background: #e9ecef; margin: 0; padding: 24px;
    }
    .sheet {
      background: #fff; width: 900px; max-width: 100%; margin: 0 auto;
      padding: 34px 40px 28px; box-shadow: 0 2px 14px rgba(0,0,0,.15);
    }
    .head { display: flex; align-items: center; gap: 16px; border-bottom: 3px double var(--line); padding-bottom: 14px; }
    .head img { width: 66px; height: 66px; object-fit: contain; }
    .head .org { line-height: 1.25; }
    .head .org .name { font-size: 20px; font-weight: 700; letter-spacing: .5px; color: var(--brand); }
    .head .org .sub  { font-size: 12px; color: var(--muted); }
    .doc-title { text-align: center; font-size: 17px; font-weight: 700; letter-spacing: 3px; margin: 16px 0 4px; color: var(--brand); }
    .doc-sub   { text-align: center; font-size: 12px; color: var(--muted); margin-bottom: 18px; }

    table.soa { width: 100%; border-collapse: collapse; font-size: 12px; }
    table.soa th, table.soa td { border: 1px solid var(--line); padding: 5px 8px; vertical-align: top; }
    table.soa thead th { background: #f1f1f1; text-align: left; letter-spacing: .3px; }
    table.soa td.cn { text-align: center; width: 34px; }
    tr.grand td { font-weight: 700; color: var(--accent); border-top: 2px solid var(--line); }

    .summary { width: 340px; margin-left: auto; margin-top: 18px; border-collapse: collapse; font-size: 13px; }
    .summary td { padding: 5px 10px; border: 1px solid var(--line); }
    .summary td.k { color: var(--muted); }
    .summary td.v { text-align: right; font-weight: 700; }
    .summary tr.bal td { color: var(--accent); font-size: 15px; }

    .signs { display: flex; justify-content: space-between; margin-top: 46px; font-size: 12px; }
    .signs .box { width: 46%; text-align: center; }
    .signs .line { border-top: 1px solid var(--line); padding-top: 4px; }
    .foot-note { margin-top: 22px; font-size: 11px; color: var(--muted); text-align: center; }

    .toolbar { width: 900px; max-width: 100%; margin: 0 auto 16px; display: flex; gap: 10px; justify-content: flex-end; }
    .btn { border: 0; border-radius: 4px; padding: 9px 16px; font-size: 13px; cursor: pointer; color: #fff; text-decoration: none; display: inline-block; }
    .btn-print { background: #1c2f9e; }
    .btn-back  { background: #6c757d; }
    .empty { text-align:center; padding:40px 20px; color:var(--muted); }

    @media print {
      body { background: #fff; padding: 0; }
      .toolbar { display: none !important; }
      .sheet { box-shadow: none; width: auto; padding: 0 6mm; }
      @page { size: A4 landscape; margin: 10mm; }
    }
  </style>
</head>
<body>

<div class="toolbar">
  <a href="<?php echo WEB_ROOT; ?>module/employee/index.php" class="btn btn-back">&larr; Back</a>
  <a href="#" onclick="window.print();return false;" class="btn btn-print">&#128424; Print</a>
</div>

<div class="sheet">

  <div class="head">
    <img src="<?php echo WEB_ROOT; ?>csr-scc.png" alt="Logo">
    <div class="org">
      <div class="name">ABADIES SOLUTIONS</div>
      <div class="sub">Student and Alumni Records Management System</div>
      <div class="sub">Human Resource Office</div>
    </div>
  </div>

  <div class="doc-title">EMPLOYEE REPORT</div>
  <div class="doc-sub"><?php echo htmlspecialchars($statusLabel); ?> &middot; Printed on <?php echo date('F d, Y g:i A'); ?></div>

  <?php if (count($rows) === 0) { ?>
    <div class="empty">No employee records to show.</div>
  <?php } else { ?>

  <table class="soa">
    <thead>
      <tr>
        <th class="cn">#</th>
        <th style="width:50px;">ID</th>
        <th>Name</th>
        <th>Email</th>
        <th style="width:100px;">Phone</th>
        <th>Job Title</th>
        <th>Department</th>
        <th>Manager</th>
        <th style="width:90px;">Hire Date</th>
        <th style="width:70px;">Status</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; foreach ($rows as $r) {
        $mgr = ($r->MgrLast === null) ? '' : trim($r->MgrLast.', '.$r->MgrFirst);
      ?>
      <tr>
        <td class="cn"><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars($r->EmployeeID); ?></td>
        <td><?php echo htmlspecialchars(trim($r->LastName.', '.$r->FirstName)); ?></td>
        <td><?php echo dash($r->Email); ?></td>
        <td><?php echo dash($r->Phone); ?></td>
        <td><?php echo dash($r->JobTitle); ?></td>
        <td><?php echo dash($r->Department); ?></td>
        <td><?php echo dash($mgr); ?></td>
        <td><?php echo dash($r->HireDate); ?></td>
        <td><?php echo ($r->IsActive == 1) ? 'Active' : 'Inactive'; ?></td>
      </tr>
      <?php } ?>
      <tr class="grand">
        <td colspan="9">TOTAL EMPLOYEES</td>
        <td><?php echo count($rows); ?></td>
      </tr>
    </tbody>
  </table>

  <table class="summary">
    <tr><td class="k">Active</td><td class="v"><?php echo $activeCnt; ?></td></tr>
    <tr><td class="k">Inactive</td><td class="v"><?php echo $inactiveCnt; ?></td></tr>
    <tr class="bal"><td class="k">Total</td><td class="v"><?php echo count($rows); ?></td></tr>
  </table>

  <div class="signs">
    <div class="box"><div class="line">Prepared by: <?php echo htmlspecialchars($preparedBy); ?></div></div>
    <div class="box"><div class="line">Verified by</div></div>
  </div>

  <div class="foot-note">This report is system-generated and valid without alteration.</div>

  <?php } ?>
</div>

</body>
</html>