<?php
/* =====================================================================
   ABADIES SOLUTIONS - Payments Report (printable)

   Standalone document that mirrors module/cashier/soa.php exactly (same
   masthead, typography, bordered table, summary, signatures and print
   rules) so the Payments print matches the Statement of Account format.

   Prints ALL payment rows, or just one fee type when ?fee_type= is given.
   ===================================================================== */
require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID'])) { redirect(WEB_ROOT."login.php"); exit; }

$validFeeTypes = array('Tuition', 'Enrollment Fee', 'Entrance Exam', 'Admission Fee');
$feeType = isset($_GET['fee_type']) && in_array($_GET['fee_type'], $validFeeTypes) ? $_GET['fee_type'] : '';

$rows       = array();
$totalAll   = 0.0;

if ($mydb->tableExists('tblpayments')) {
	$where = ($feeType !== '') ? " WHERE p.FEE_TYPE = '".$mydb->escape_value($feeType)."' " : "";
	$mydb->setQuery("SELECT p.PAYMENT_ID, p.OR_NUMBER, p.FEE_TYPE, p.AMOUNT, p.DATE_PAID,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME,
			c.COURSE_CODE, u.DISPLAYNAME
		FROM `tblpayments` p
		JOIN `tblenrollment` e ON e.ENROLLMENT_ID = p.ENROLLMENT_ID
		JOIN `tblstudent`    s ON s.S_ID          = e.S_ID
		JOIN `tblcourses`    c ON c.COURSE_ID     = e.COURSE_ID
		LEFT JOIN `tblusers` u ON u.UID           = p.RECEIVED_BY
		".$where."
		ORDER BY p.DATE_PAID ASC, p.PAYMENT_ID ASC");
	foreach ($mydb->loadResultList() as $r) {
		$rows[] = $r;
		$totalAll += (float)$r->AMOUNT;
	}
}

function money($n) { return number_format((float)$n, 2); }
function peso($n)  { return '&#8369; '.number_format((float)$n, 2); }

$preparedBy = isset($_SESSION['DISPLAYNAME']) ? $_SESSION['DISPLAYNAME'] : 'Cashier';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payments Report<?php echo $feeType !== '' ? ' - '.htmlspecialchars($feeType) : ''; ?></title>
  <style>
    :root { --ink:#1a1a1a; --line:#333; --muted:#666; --accent:#c31f5e; --brand:#1c2f9e; }
    * { box-sizing: border-box; }
    body {
      font-family: "Segoe UI", Arial, Helvetica, sans-serif;
      color: var(--ink); background: #e9ecef; margin: 0; padding: 24px;
    }
    .sheet {
      background: #fff; width: 820px; max-width: 100%; margin: 0 auto;
      padding: 34px 40px 28px; box-shadow: 0 2px 14px rgba(0,0,0,.15);
    }
    .head { display: flex; align-items: center; gap: 16px; border-bottom: 3px double var(--line); padding-bottom: 14px; }
    .head img { width: 66px; height: 66px; object-fit: contain; }
    .head .org { line-height: 1.25; }
    .head .org .name { font-size: 20px; font-weight: 700; letter-spacing: .5px; color: var(--brand); }
    .head .org .sub  { font-size: 12px; color: var(--muted); }
    .doc-title { text-align: center; font-size: 17px; font-weight: 700; letter-spacing: 3px; margin: 16px 0 4px; color: var(--brand); }
    .doc-sub   { text-align: center; font-size: 12px; color: var(--muted); margin-bottom: 18px; }

    table.soa { width: 100%; border-collapse: collapse; font-size: 13px; }
    table.soa th, table.soa td { border: 1px solid var(--line); padding: 5px 10px; }
    table.soa thead th { background: #f1f1f1; text-align: left; letter-spacing: .5px; }
    table.soa td.amt, table.soa th.amt { text-align: right; width: 130px; font-variant-numeric: tabular-nums; }
    table.soa td.cn { text-align: center; width: 44px; }
    tr.subtot td   { font-weight: 700; }
    tr.grand td    { font-weight: 700; color: var(--accent); font-size: 14px; border-top: 2px solid var(--line); }

    .summary { width: 340px; margin-left: auto; margin-top: 18px; border-collapse: collapse; font-size: 13px; }
    .summary td { padding: 5px 10px; border: 1px solid var(--line); }
    .summary td.k { color: var(--muted); }
    .summary td.v { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }
    .summary tr.bal td { color: var(--accent); font-size: 15px; }

    .signs { display: flex; justify-content: space-between; margin-top: 46px; font-size: 12px; }
    .signs .box { width: 46%; text-align: center; }
    .signs .line { border-top: 1px solid var(--line); padding-top: 4px; }
    .foot-note { margin-top: 22px; font-size: 11px; color: var(--muted); text-align: center; }

    .toolbar { width: 820px; max-width: 100%; margin: 0 auto 16px; display: flex; gap: 10px; justify-content: flex-end; }
    .btn { border: 0; border-radius: 4px; padding: 9px 16px; font-size: 13px; cursor: pointer; color: #fff; text-decoration: none; display: inline-block; }
    .btn-print { background: #1c2f9e; }
    .btn-back  { background: #6c757d; }
    .empty { text-align:center; padding:40px 20px; color:var(--muted); }

    @media print {
      body { background: #fff; padding: 0; }
      .toolbar { display: none !important; }
      .sheet { box-shadow: none; width: auto; padding: 0 6mm; }
      @page { size: A4 portrait; margin: 12mm; }
    }
  </style>
</head>
<body>

<div class="toolbar">
  <a href="<?php echo WEB_ROOT; ?>module/payments/index.php" class="btn btn-back">&larr; Back</a>
  <a href="#" onclick="window.print();return false;" class="btn btn-print">&#128424; Print</a>
</div>

<div class="sheet">

  <div class="head">
    <img src="<?php echo WEB_ROOT; ?>csr-scc.png" alt="Logo">
    <div class="org">
      <div class="name">ABADIES SOLUTIONS</div>
      <div class="sub">Student and Alumni Records Management System</div>
      <div class="sub">Office of the Cashier</div>
    </div>
  </div>

  <div class="doc-title">PAYMENTS REPORT</div>
  <div class="doc-sub">
    <?php echo ($feeType !== '') ? htmlspecialchars($feeType).' only' : 'All fee types'; ?>
    &middot; Printed on <?php echo date('F d, Y g:i A'); ?>
  </div>

  <?php if (count($rows) === 0) { ?>
    <div class="empty">No payment records to show.</div>
  <?php } else { ?>

  <table class="soa">
    <thead>
      <tr>
        <th class="cn">#</th>
        <th style="width:100px;">Date</th>
        <th style="width:110px;">OR No.</th>
        <th>Student</th>
        <th style="width:80px;">Course</th>
        <th style="width:120px;">Fee Type</th>
        <th class="amt">Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; foreach ($rows as $r) { ?>
      <tr>
        <td class="cn"><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars($r->DATE_PAID); ?></td>
        <td><?php echo htmlspecialchars($r->OR_NUMBER); ?></td>
        <td><?php echo htmlspecialchars($r->IDNO.' - '.trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)); ?></td>
        <td><?php echo htmlspecialchars($r->COURSE_CODE); ?></td>
        <td><?php echo htmlspecialchars($r->FEE_TYPE); ?></td>
        <td class="amt"><?php echo money($r->AMOUNT); ?></td>
      </tr>
      <?php } ?>
      <tr class="grand">
        <td colspan="6">TOTAL COLLECTED</td>
        <td class="amt"><?php echo money($totalAll); ?></td>
      </tr>
    </tbody>
  </table>

  <table class="summary">
    <tr><td class="k">Records</td><td class="v"><?php echo count($rows); ?></td></tr>
    <tr class="bal"><td class="k">Total Collected</td><td class="v"><?php echo peso($totalAll); ?></td></tr>
  </table>

  <div class="signs">
    <div class="box"><div class="line">Prepared by: <?php echo htmlspecialchars($preparedBy); ?></div></div>
    <div class="box"><div class="line">Verified by</div></div>
  </div>

  <div class="foot-note">
    This report is system-generated and valid without alteration.
  </div>

  <?php } ?>
</div>

</body>
</html>
